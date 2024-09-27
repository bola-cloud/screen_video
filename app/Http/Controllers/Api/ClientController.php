<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ClientController extends Controller
{
    public function getClientAds(Request $request)
    {
        try {
            $client_id = $request->client_id;

            // Fetch all ads related to the client with their associated TVs and display times
            $ads = AdSchedule::with(['advertisement', 'tv.displayTimes'])
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

                // Get the TVs where this ad is displayed
                $tvs = $groupedAds->map(function ($adSchedule) {
                    if ($adSchedule->tv && $adSchedule->tv->displayTimes) {
                        return [
                            'tv_name' => $adSchedule->tv->name,
                            'tv_location' => $adSchedule->tv->location,
                            'display_times' => $adSchedule->tv->displayTimes->map(function ($displayTime) {
                                return [
                                    'date' => $displayTime->date,
                                    'start_time' => $displayTime->start_time,
                                    'end_time' => $displayTime->end_time,
                                ];
                            }),
                        ];
                    }
                    return null;
                })->filter(); // Remove null if any TV or displayTime is not found

                // Return the structured ad details with associated TVs
                return [
                    'ad_name' => $firstAd->advertisement->name,
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

    public function getTvsByClient($clientId)
    {
        // Retrieve the client
        $client = User::where('category', 'client')->findOrFail($clientId);

        // Get all TVs where the client's ads are scheduled
        $tvs = $client->ads()->with('tvs')->get()->pluck('tvs')->flatten()->unique('id')->values();

        return response()->json([
            'client' => $client->name,
            'tvs' => $tvs->map(function ($tv) {
                return [
                    'id' => $tv->id,
                    'name' => $tv->name,
                    'location' => $tv->location,
                ];
            }),
        ]);
    }
}
