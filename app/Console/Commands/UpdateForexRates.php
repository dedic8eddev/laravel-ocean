<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateForexRates extends Command {

    protected $signature = 'migrator:forex_rates';

    protected $description = 'Keeps up to date is_bunkering flag in ports DB table.';

    public function handle() {
        $forexRatesFromDocks = DB::connection('docks')
                                 ->table('forex_rates')
                                 ->get();

        $forexRatesFromDocks->each(function ($values) {       // we could drop the table and bulk insert, but I believe it would be less robust
            DB::table('forex_rates')
              ->updateOrInsert(
                  ['currency_label' => $values->currency_label],
                  [
                      'base'        => $values->base,
                      'rate'        => $values->rate,
                      'last_update' => $values->last_update,
                  ]
              );
        });
    }

}