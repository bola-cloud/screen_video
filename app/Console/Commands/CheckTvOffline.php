<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tv;
use Carbon\Carbon;

class CheckTvOffline extends Command
{
    protected $signature = 'tv:check-offline';
    protected $description = 'Mark TVs as offline if they have not reported in the last 30 minutes';

    public function handle()
    {
        // Get the current time and calculate the cutoff time (30 minutes ago)
        $cutoffTime = Carbon::now()->subMinutes(2);

        // Update any TV that hasn't updated in the last 30 minutes
        Tv::where('updated_at', '<', $cutoffTime)->update(['status' => 0]);

        $this->info('TVs that haven’t responded in the last 30 minutes have been marked offline.');
    }
}
