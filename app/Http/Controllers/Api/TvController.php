<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tv;
use Illuminate\Http\Request;

use App\Models\TvDisplayTime;
use App\Models\AdSchedule;
use Illuminate\Support\Facades\Http; // Make sure to import Http for API call


use Carbon\Carbon;

class TvController extends Controller
{
    /**
     * Return all TVs.
     */
    public function index()
    {
        // Fetch all TVs
        $tvs = Tv::where('is_active',1)->get();

        // Return as JSON response
        return response()->json($tvs, 200);
    }
  
public function tv_end_time($tv_id){

try {

        $server   = '77.37.54.128';
        $port     = 1883;
        $clientId = 'ad-publisher';
        $mqtt     = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
        $mqtt->connect();
      $mqtt->publish("video/{$tv_id}", "null", 0);
        return response()->json(['message' => 'Ad end successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error publishing ad: ' . $e->getMessage()], 500);
    }


}

public function tvs_time($tv_id)
{
    $currentDate = Carbon::now()->format('Y-m-d');
    $currentTime = Carbon::now();

    // Check if the current time is within the TV display time period
    $time_tvs = TvDisplayTime::where('tv_id', $tv_id)->where('date', $currentDate)->first();

    if (!$time_tvs) {
        // If there's no display time for the current date, return a 404 response
        return response()->json(['message' => 'No TV display time found for the current date'], 404);
    }

    // Check if the current time is within the start_time and end_time period
    $startTime = Carbon::createFromFormat('H:i:s', $time_tvs->start_time);
    $endTime = Carbon::createFromFormat('H:i:s', $time_tvs->end_time);

    if ($currentTime->lt($startTime) || $currentTime->gt($endTime)) {
        // If the current time is outside the display period, return a response indicating this
        return response()->json(['message' => 'Current time is not within the TV display time period'], 400);
    }

    // Proceed to find the nearest record within the valid display period
    $adschedules = AdSchedule::where('tv_id', $tv_id)
        ->where('date', $currentDate)
        ->get();

    $nearestRecord = null;
    $smallestDifference = null;

    foreach ($adschedules as $adschedule) {
        $displayTimes = $adschedule->displayTimes;

        // Loop through each display time and calculate the time difference
        foreach ($displayTimes as $displayTime) {
            $displayTimeCarbon = Carbon::createFromFormat('H:i:s', $displayTime->display_time);

            // Calculate the difference in seconds between current time and display time
            $difference = $currentTime->diffInSeconds($displayTimeCarbon, false); // false to get negative values if in the past

            // Check if it's the smallest difference or if it's the first comparison
            if (is_null($smallestDifference) || abs($difference) < abs($smallestDifference)) {
                $smallestDifference = $difference;
                $nearestRecord = $displayTime;
            }
        }
    }

    // If a nearest record was found, proceed with the API call
    if ($nearestRecord) {
        // Extract the order from the nearest record's adSchedule relationship
        $order = $nearestRecord->adSchedule->order;

        // Make the API call to the external API
        $response = Http::get("https://saudiscai.com/api/tvs/{$tv_id}/{$order}");

        // Check if the API request was successful
        if ($response->successful()) {
            return response()->json([
                'nearest_display_time' => $nearestRecord,
                'api_response' => $response->json()
            ], 200);
        } else {
            return response()->json(['message' => 'Failed to retrieve data from external API'], $response->status());
        }
    }

    return response()->json(['message' => 'No records found for the current date'], 404);
}

  
}
