<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\TvDisplayTimeController;


class ScheduleTvAds extends Command
{
    // Define the command signature
    protected $signature = 'tv:schedule-ads {tv_id} {date}';

    // Define the command description
    protected $description = 'Schedules ads for a specific TV on a specific date';

    // Execute the console command
    public function handle()
    {
        // Retrieve arguments passed to the command
        $tv_id = $this->argument('tv_id');
        $date = $this->argument('date');

        // Instantiate the TvDisplayTimeController
        $controller = new TvDisplayTimeController();

        // Call the scheduleAdsForTv method from the controller
        $result = $controller->scheduleAdsForTv($tv_id, $date);

        if ($result) {
            $this->info("Ads scheduled for TV ID: $tv_id on $date");
        } else {
            $this->error("Failed to schedule ads for TV ID: $tv_id on $date");
        }
    }
}
