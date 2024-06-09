<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateBunkeringPorts extends Command {

    protected $signature = 'update:bunkering_ports_flag';

    protected $description = 'Keeps up to date is_bunkering flag in ports DB table.';

    public function handle() {
        DB::table('ports AS p')
                   ->whereExists(function ($query) {
                       $query->select(DB::raw(1))
                             ->from('bunkering_prices_latest AS bpl')
                             ->whereRaw('bpl.port_id = p.id');
                   })
                   ->update(['is_bunkering' => true]);
    }

}