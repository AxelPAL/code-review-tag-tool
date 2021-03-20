<?php

namespace App\Console;

use App\Console\Commands\ParseComments;
use App\Console\Commands\PullRequests;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(PullRequests::class, ['--onlyActive'])->everyFourHours();
        $schedule->command(PullRequests::class)->dailyAt('02:00')->withoutOverlapping();
        $schedule->command(ParseComments::class)->daily()->withoutOverlapping();
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
