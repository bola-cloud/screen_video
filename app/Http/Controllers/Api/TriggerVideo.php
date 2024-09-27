<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TvDisplayTime;
use App\Models\AdSchedule;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpMqtt\Client\Facades\MQTT;

class TriggerVideo extends Controller
{
    public function triggerVideo(Request $request)
    {
        try {
            // Validate the request data
            $data = $request->validate([
                'order' => 'required|integer',
                'tv_id' => 'required|integer',
                'ad_id' => 'required|integer',
            ]);

            Log::info('Video trigger request received:', $data);

            $tv_id = $data['tv_id'];
            $order = $data['order'];

            // Get the current date and time
            $currentDateTime = Carbon::now();
            $currentDate = $currentDateTime->toDateString();
            $currentTime = $currentDateTime->format('H:i:s');

            // Check if the TV is operating at the current time
            $tvDisplayTime = TvDisplayTime::where('tv_id', $tv_id)
                                          ->where('date', $currentDate)
                                          ->first();

            if (!$tvDisplayTime) {
                return response()->json(['error' => 'No operating time found for TV on this date'], 404);
            }

            // Ensure the current time is within the TV's operating time range
            $startTime = Carbon::parse($tvDisplayTime->start_time);
            $endTime = Carbon::parse($tvDisplayTime->end_time);

            if (!$currentDateTime->between($startTime, $endTime)) {
                return response()->json(['error' => 'TV is not within its operating hours'], 403);
            }

            // Find the next ad in the sequence based on the current order
            $nextAdSchedule = AdSchedule::where('tv_id', $tv_id)
                                        ->where('order', '>', $order)
                                        ->orderBy('order', 'asc')
                                        ->first();

            // If there is no next ad (i.e., we reached the end of the order), loop back to the first one
            if (!$nextAdSchedule) {
                $nextAdSchedule = AdSchedule::where('tv_id', $tv_id)
                                            ->orderBy('order', 'asc')
                                            ->first();
            }

            if (!$nextAdSchedule) {
                return response()->json(['error' => 'No ads found for TV'], 404);
            }

            $ad = Advertisement::find($nextAdSchedule->advertisement_id);

            if (!$ad) {
                return response()->json(['error' => 'Ad not found'], 404);
            }

            // Extract the video ID and construct a new video link
            $currentLink = $ad->video_link;
            $parsedUrl = parse_url($currentLink);
            parse_str($parsedUrl['query'], $queryParams);

            // Assuming the video ID is stored in the 'v' query parameter
            $videoId = $queryParams['v'] ?? basename($parsedUrl['path']);
            $newVideoLink = "https://youtu.be/{$videoId}";

            // Publish the video link via MQTT
            $server   = '77.37.54.128';
            $port     = 1883;
            $clientId = 'ad-publisher';
            $mqtt     = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);

            // Connect to MQTT broker, publish the new video link, and disconnect
            $mqtt->connect();
            $mqtt->publish("video/{$tv_id}", $newVideoLink, 0);
            $mqtt->disconnect();

            Log::info("Triggered video for TV ID: $tv_id, Ad ID: {$ad->id}, Next Order: {$nextAdSchedule->order}");

            // Return a success response with the next ad order
            return response()->json([
                'success' => true,
                'message' => 'Next video triggered successfully',
                'next_order' => $nextAdSchedule->order,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error triggering video: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to trigger next video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
