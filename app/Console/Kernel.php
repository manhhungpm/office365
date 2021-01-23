<?php

namespace App\Console;

use App\Console\Commands\AssignUserLicenseCommand;
use App\Console\Commands\OfficeGetTokenCommand;
use App\Console\Commands\SyncDomainCommand;
use App\Console\Commands\SyncSubscribedSkuCommand;
use App\Console\Commands\SyncUserCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        OfficeGetTokenCommand::class,
        SyncDomainCommand::class,
        SyncUserCommand::class,
        SyncSubscribedSkuCommand::class,
        AssignUserLicenseCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('office:get-token')->everyThirtyMinutes();
        $schedule->command('office:sync-domain')->hourly();
        $schedule->command('office:sync-user')->everyFiveMinutes(); //trước là 1 phút
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
