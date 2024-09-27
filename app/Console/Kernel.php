<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\TvDisplayTime; // Add your model here
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $tvDisplayTimes = TvDisplayTime::all();
        foreach ($tvDisplayTimes as $tvDisplayTime) {
            $schedule->command('tv:schedule-ads', [$tvDisplayTime->tv_id, $tvDisplayTime->date])
                ->timezone('Africa/Cairo')
                ->everyMinute();  // Set this for immediate testing
        }
    }
    


    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
