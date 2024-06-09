<?php

namespace App\Console\Commands;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BunkeringPricesLatestMigrator extends Command {

    protected $signature = 'migrator:bunkering_prices_latest';

    protected $description = 'Migrates latest bunkering prices from docks DB to orca core DB.';

    public function handle() {
        $pricesFromDocks = DB::connection('docks')
                             ->table(
                                 DB::raw("
                                    (
                                        SELECT port_code,
                                               label AS fuel_grade_label,
                                               price,
                                               average_7_days_price,
                                               last_updated,
                                               source
                                        FROM (
                                                 SELECT *, max(last_updated) OVER (PARTITION BY port_code, fuel_grade_id) AS max_last_updated
                                                 FROM bunkering_prices AS bp
                                                          JOIN fuel_grades fg on bp.fuel_grade_id = fg.id
                                                     AND port_code <> ''
                                             ) AS t1
                                        WHERE last_updated = max_last_updated
                                    ) AS datatable
                                ")
                             )
                             ->get();
        $pricesFromDocks = $pricesFromDocks->groupBy('port_code');

        $pricesForCore = [];
        $pricesFromDocks
            ->each(function ($portPrices, $portCode) use (&$pricesForCore) {
                $port = DB::table('ports')
                          ->where('code', $portCode)
                          ->select('id')
                          ->first();

                if (is_null($port)) {
                    Bugsnag::notifyException(new \Exception("$portCode port code not found in ports table."));  // Here we want to log the missing port and continue with the execution
                } else {
                    $portPricesFormatted = [
                        'port_id'                     => $port->id,
                        'lsmgo_price'                 => null,
                        'lsmgo_average_7_days_price'  => null,
                        'lsmgo_last_updated'          => null,
                        'ifo380_price'                => null,
                        'ifo380_average_7_days_price' => null,
                        'ifo380_last_updated'         => null,
                        'ifo180_price'                => null,
                        'ifo180_average_7_days_price' => null,
                        'ifo180_last_updated'         => null,
                        'vlsfo_price'                 => null,
                        'vlsfo_average_7_days_price'  => null,
                        'vlsfo_last_updated'          => null,
                        'ulsfo_price'                 => null,
                        'ulsfo_average_7_days_price'  => null,
                        'ulsfo_last_updated'          => null,
                        'source'                      => 'bunkerex',
                    ];
                    $portPrices->each(function ($price) use (&$portPricesFormatted) {
                        $portPricesFormatted[$price->fuel_grade_label . '_price'] = $price->price;
                        $portPricesFormatted[$price->fuel_grade_label . '_average_7_days_price'] = $price->average_7_days_price;
                        $portPricesFormatted[$price->fuel_grade_label . '_last_updated'] = $price->last_updated;
                    });
                    $pricesForCore[] = $portPricesFormatted;
                }
            });

        array_map(function ($values) {       // we could drop the table and bulk insert, but I believe it would be less robust
            DB::table('bunkering_prices_latest')
              ->updateOrInsert(
                  ['port_id' => $values['port_id']], $values
              );
        }, $pricesForCore);
    }

}