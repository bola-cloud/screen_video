<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdSchedule;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AdController extends Controller
{
  
public function publishNextAd($tv_id)
{
    try {
        // Get the next ad for the TV
        $adSchedule = AdSchedule::where('tv_id', $tv_id)
                                ->orderBy('order')
                                ->first();

        if (!$adSchedule) {
            // If no unplayed ad, reset the played status to restart the cycle
            $adSchedule = AdSchedule::where('tv_id', $tv_id)->orderBy('order')->first();
        }

        $ad = Advertisement::find($adSchedule->advertisement_id);

        // Prepare data to publish
        $data = json_encode([
            'advertisement_id' => $ad->id,
            'video_link' => $ad->video_link,
            'tv_id' => $tv_id // Make sure the tv_id is included for the topic
        ]);

        // Use Process to execute Node.js script
        $process = new Process(['C:\\Program Files\\nodejs\\node', base_path('node_scripts/mqtt_publisher.js'), $data]);
        $process->run();
        

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            \Log::error('Node.js Script Error: ' . $process->getErrorOutput());
            throw new ProcessFailedException($process);
        }

        // Log the output for debugging
        \Log::info('Node.js Script Output: ' . $process->getOutput());

        return response()->json(['message' => 'Ad published successfully', 'output' => $process->getOutput(), 'ad' => $ad]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Error publishing ad: ' . $e->getMessage()], 500);
    }
}

    /**
     * Endpoint to trigger the ad scheduling.
     */
    public function triggerAdScheduling($tv_id)
    {
        return $this->publishNextAd($tv_id);
    }
}
