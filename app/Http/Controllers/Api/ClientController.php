<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdSchedule;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\AdDisplayTime;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
class ClientController extends Controller
{
    public function getClientAds(Request $request)
    {
        try {
            $client_id = $request->client_id;

            // Fetch all ads related to the client with their associated TVs and ad display times (from ad_display_times)
            $ads = AdSchedule::with(['advertisement', 'tv', 'displayTimes']) // Changed to use 'displayTimes' from AdDisplayTime
                ->whereHas('advertisement', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
                })
                ->get();

            // Check if ads exist
            if ($ads->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No ads found for this client.'], 404);
            }

            // Map the ads and their related information
            $adDetails = $ads->groupBy('advertisement.id')->map(function ($groupedAds) {
                // Fetch the first ad since the details will be the same for grouped ads
                $firstAd = $groupedAds->first();

                // Get the TVs where this ad is displayed along with their display times
                $tvs = $groupedAds->map(function ($adSchedule) {
                    if ($adSchedule->tv && $adSchedule->displayTimes) {
                        return [
                            'tv_name' => $adSchedule->tv->name,
                            'tv_location' => $adSchedule->tv->location,
                            'display_times' => $adSchedule->displayTimes->map(function ($displayTime) {
                                return [
                                    'display_date' => $displayTime->display_date,
                                    'display_time' => $displayTime->display_time,
                                ];
                            }),
                        ];
                    }
                    return null;
                })->filter(); // Remove null if any TV or displayTime is not found

                // Return the structured ad details with associated TVs
                return [
                    'ad_name' => $firstAd->advertisement->title,
                    'tvs' => $tvs->values(),
                ];
            })->values(); // Reset array keys after mapping

            // Return the response with the ads and their TV details
            return response()->json([
                'success' => true,
                'ads' => $adDetails,
            ]);

        } catch (\Exception $e) {
            Log::error("Error fetching client ads: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching client ads.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
  
   public function getAdsByClient($client_id)
    {
        // Retrieve the advertisements by client ID
        $ads = Advertisement::where('client_id', $client_id)->get();

        // Add the YouTube thumbnail link to each ad
        $ads->each(function ($ad) {
            $ad->thumbnail_link = $this->generateYouTubeThumbnail($ad->video_link);
        });

        // Return the ads as a JSON response
        return response()->json($ads);
    }
  private function generateYouTubeThumbnail($videoLink)
{
    // Extract the video ID from the YouTube link
    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoLink, $matches);

    if (!empty($matches) && isset($matches[1])) {
        // Return the thumbnail URL
        return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
    }

    // Return null if no video ID found
    return null;
}
public function getTvsByAd($adId)
{
    // Get all unique TVs related to the specified ad_id
    $tvs = DB::table('tvs')
        ->join('ad_schedules', 'tvs.id', '=', 'ad_schedules.tv_id')
        ->where('ad_schedules.advertisement_id', $adId)
        ->select('tvs.id', 'tvs.name', 'tvs.location')
        ->distinct() // Ensure unique TVs
        ->get();

    // Return the response in JSON format
    return response()->json([
        'advertisement_id' => $adId,
        'tvs' => $tvs
    ]);
}


  public function getAds($tv_id, $advertisement_id, $date)
    {
        // Validate the date format to ensure it's correct
        if (!strtotime($date)) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Find the ad schedule that matches the tv_id, advertisement_id, and date
        $adSchedule = AdSchedule::where('tv_id', $tv_id)
            ->where('advertisement_id', $advertisement_id)
            ->whereDate('date', $date)
            ->first();

        // If no ad schedule is found, return an error message
        if (!$adSchedule) {
            return response()->json(['message' => 'No records found'], 404);
        }

        // Fetch the corresponding ad display times using the ad_schedule_id
        $adDisplayTimes = AdDisplayTime::where('ad_schedule_id', $adSchedule->id)
            ->get();

        return response()->json($adDisplayTimes, 200);
    }

}
