<?php

namespace App\Console;

use App\Console\Commands\BunkeringPricesLatestMigrator;
use App\Console\Commands\UpdateBunkeringPorts;
use App\Console\Commands\UpdateForexRates;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BunkeringPricesLatestMigrator::class,
        UpdateBunkeringPorts::class,
        UpdateForexRates::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('migrator:bunkering_prices_latest')->cron("0 */2 * * *");
        $schedule->command('migrator:forex_rates')->cron("0 */4 * * *");

        $schedule->command('update:bunkering_ports_flag')->cron("0 */24 * * *");
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
