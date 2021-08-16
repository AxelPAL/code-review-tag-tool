<?php

namespace App\Console;

use App\Console\Commands\ParseComments;
use App\Console\Commands\PullRequests;
use App\Console\Commands\RefreshToken;
use App\Console\Commands\UsersUpdate;
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
        $schedule->command(RefreshToken::class)->hourly();
        $schedule->command(PullRequests::class, ['--onlyActive'])->everyFourHours();
        $schedule->command(PullRequests::class)->dailyAt('02:00');
        $schedule->command(ParseComments::class)->daily();
        $schedule->command(UsersUpdate::class)->dailyAt('15:00');
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
