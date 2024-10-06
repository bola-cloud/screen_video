<?php

namespace App\Console;
use App\Models\TvDisplayTime;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
          $currentDate = Carbon::now()->format('Y-m-d');
        
        $ads = TvDisplayTime::where('date', $currentDate)->get();
       foreach ($ads as $ad) {
            // Schedule the command for each ad with its tv_id and start_time
            $schedule->command("ad:publish {$ad->tv_id} 1")
                     ->dailyAt(Carbon::parse($ad->start_time)->format('H:i')); // Ensure proper time format
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
