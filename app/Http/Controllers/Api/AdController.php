<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdSchedule;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function publishNextAd($tv_id)
    {
        try {
            // Get the next ad for the TV
            $adSchedule = AdSchedule::where('tv_id', $tv_id)
                                    ->where('is_played', 0)
                                    ->orderBy('order')
                                    ->first();

            if (!$adSchedule) {
                // If no unplayed ad, reset the played status to restart the cycle
                AdSchedule::where('tv_id', $tv_id)->update(['is_played' => 0]);
                $adSchedule = AdSchedule::where('tv_id', $tv_id)->orderBy('order')->first();
            }

            $ad = Advertisement::find($adSchedule->advertisement_id);

            // Prepare data to publish
            $data = json_encode([
                'advertisement_id' => $ad->id,
                'video_link' => $ad->video_link
            ]);

            // Execute Node.js script to publish the message via MQTT
            $output = shell_exec("node " . base_path('node_scripts/mqtt_publisher.js') . " '" . $data . "'");

            // Mark the ad as played
            $adSchedule->update(['is_played' => 1]);

            return response()->json(['message' => 'Ad published successfully', 'ad' => $ad]);
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
