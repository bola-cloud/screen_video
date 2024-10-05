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
public function publishNextAd($tv_id,$order)
{
$currentDate = Carbon::now()->format('Y-m-d');

    $time_tvs = TvDisplayTime::where('tv_id', $tv_id)->where('date', $currentDate)->first();
try {
$last_order = AdSchedule::where('tv_id', $tv_id)
              ->where('date', $currentDate)
              ->orderBy('order', 'desc')
              ->first(); 
$adSchedule = AdSchedule::where('tv_id', $tv_id)
              	->where('order', $order)
  				->where('date',$currentDate)
              ->first();
        $ad = Advertisement::find($adSchedule->advertisement_id);
        $server   = '77.37.54.128';
        $port     = 1883;
        $clientId = 'ad-publisher';
        $mqtt     = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
        // Conect, publish, and disconnect
        $mqtt->connect();
   
$mqtt->publish("video/{$tv_id}", "{$ad->video_link},{$adSchedule->order},{$last_order->order},{$time_tvs->end_time}", 0);
        return response()->json(['message' => 'Ad published successfully', 'order'=>$adSchedule->order,'ad_id'=>$adSchedule->advertisement_id ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error publishing ad: ' . $e->getMessage()], 500);
    }
}


}
