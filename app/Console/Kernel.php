<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('cwb:runtest')->timezone('Asia/Ho_Chi_Minh')->weekly('08:00');
        // $schedule->command('cwb:runtest')->timezone('Asia/Ho_Chi_Minh')->dailyAt('13:17');
        // $schedule->command('cwb:runtest')->everyFiveMinutes()->withoutOverlapping();
        // $schedule->command('cwb:runtest')->everyMinute();
        $schedule->command('cwb:runtest')->cron('* * * * *')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
