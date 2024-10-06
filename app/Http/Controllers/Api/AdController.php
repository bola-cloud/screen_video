<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdSchedule;
use App\Models\Advertisement;
use App\Models\TvDisplayTime;
use App\Models\AdDisplayTime;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use Carbon\Carbon;

class AdController extends Controller
{
public function publishNextAd($tv_id, $order)
{
    $currentDate = Carbon::now()->format('Y-m-d');

    // Get the TV display time for the current date
    $time_tvs = TvDisplayTime::where('tv_id', $tv_id)->where('date', $currentDate)->first();

    try {
        // Get the last order for the day
        $last_order = AdSchedule::where('tv_id', $tv_id)
                      ->where('date', $currentDate)
                      ->orderBy('order', 'desc')
                      ->first(); 

        // Fetch the ad schedule based on the current order and date
        $adSchedule = AdSchedule::where('tv_id', $tv_id)
                        ->where('order', $order)
                        ->where('date', $currentDate)
                        ->first();

        // If the 'turns' for this ad is 0, skip to the next order
        if ($adSchedule->turns <= 0) {
            // Determine the next order (loop back to the first order if this is the last one)
            $nextOrder = $order + 1 > $last_order->order ? 1 : $order + 1;
            return $this->publishNextAd($tv_id, $nextOrder);  // Recursive call to the next order
        }

        // Fetch the advertisement information
        $ad = Advertisement::find($adSchedule->advertisement_id);
        
        // MQTT server connection setup
        $server   = '77.37.54.128';
        $port     = 1883;
        $clientId = 'ad-publisher';
        $mqtt     = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
        
        // Connect, publish, and disconnect
        $mqtt->connect();

        $mqtt->publish("video/{$tv_id}", "{$ad->video_link},{$adSchedule->order},{$last_order->order},{$time_tvs->end_time}", 0);

        // After publishing, decrement the 'turns' column by 1
        $adSchedule->turns -= 1;
        $adSchedule->save();  // Save the updated 'turns' count in the database

        // Return success response
        return response()->json(['message' => 'Ad published successfully', 'order' => $adSchedule->order, 'ad_id' => $adSchedule->advertisement_id]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error publishing ad: ' . $e->getMessage()], 500);
    }
}


}
