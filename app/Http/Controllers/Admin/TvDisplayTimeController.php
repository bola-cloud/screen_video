<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TvDisplayTime;
use App\Models\Tv;
use App\Models\AdSchedule;
use App\Models\Advertisement;  // Add this line to import the Advertisement model
use App\Models\AdDisplayTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TvDisplayTimeController extends Controller
{
    public function index()
    {
        // Fetch all display times with their associated TVs
        $displayTimes = TvDisplayTime::with('tv')->get();

        return view('admin.tv_display_times.index', compact('displayTimes'));
    }

    public function create()
    {
        // Fetch all TVs for the dropdown
        $tvs = Tv::where('is_active', 1)->get();

        return view('admin.tv_display_times.create', compact('tvs'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the input data
            $data = $request->validate([
                'tvs' => 'required|array|min:1', // Must select at least one TV
                'date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ]);
    
            // Check if any of the selected TVs already have a display time for the given date
            foreach ($data['tvs'] as $tv_id) {
                $existingDisplayTime = TvDisplayTime::where('tv_id', $tv_id)
                    ->where('date', $data['date'])
                    ->first();
    
                if ($existingDisplayTime) {
                    return redirect()->back()->withErrors(['conflict' => 'TV "' . Tv::find($tv_id)->name . '" already has a display time for this date.'])->withInput();
                }
            }
    
            // Store display time for each selected TV and schedule the ads
            foreach ($data['tvs'] as $tv_id) {
                TvDisplayTime::create([
                    'tv_id' => $tv_id,
                    'date' => $data['date'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                ]);
    
                // Call the method to schedule ads
                $this->scheduleAdsForTv($tv_id, $data['date']);
            }
    
            return redirect()->route('tv_display_times.index')->with('success', 'TV Display Time added and ads scheduled successfully.');
        } catch (\Exception $e) {
            Log::error("Error in store method for TV Display Time - " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while saving TV Display Time.']);
        }
    }
    

    public function edit($id)
    {
        // Get the display time to be edited
        $displayTime = TvDisplayTime::findOrFail($id);
        
        // Fetch all TVs for selection
        $tvs = Tv::where('is_active', 1)->get();
        
        // Fetch all the currently assigned TVs for this display time
        $assignedTvs = TvDisplayTime::where('date', $displayTime->date)
                                    ->where('start_time', $displayTime->start_time)
                                    ->where('end_time', $displayTime->end_time)
                                    ->pluck('tv_id')->toArray();

        return view('admin.tv_display_times.edit', compact('displayTime', 'tvs', 'assignedTvs'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the input data
            $data = $request->validate([
                'tvs' => 'required|array|min:1', // Must select at least one TV
                'date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ]);
    
            // Get the display time to update
            $displayTime = TvDisplayTime::findOrFail($id);
    
            // Check for conflicts before updating
            foreach ($data['tvs'] as $tv_id) {
                $existingDisplayTime = TvDisplayTime::where('tv_id', $tv_id)
                    ->where('date', $data['date'])
                    ->where('id', '!=', $id) // Exclude the current record from the check
                    ->first();
    
                if ($existingDisplayTime) {
                    return redirect()->back()->withErrors(['conflict' => 'TV "' . Tv::find($tv_id)->name . '" already has a display time for this date.'])->withInput();
                }
            }
    
            // Delete existing display times for this date, start_time, and end_time
            TvDisplayTime::where('date', $displayTime->date)
                ->where('start_time', $displayTime->start_time)
                ->where('end_time', $displayTime->end_time)
                ->delete();
    
            // Recreate the display time for all selected TVs and schedule the ads
            foreach ($data['tvs'] as $tv_id) {
                TvDisplayTime::create([
                    'tv_id' => $tv_id,
                    'date' => $data['date'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                ]);
    
                // Call the method to schedule ads
                $this->scheduleAdsForTv($tv_id, $data['date']);
            }
    
            return redirect()->route('tv_display_times.index')->with('success', 'TV Display Time updated and ads scheduled successfully.');
        } catch (\Exception $e) {
            Log::error("Error in update method for TV Display Time - " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating TV Display Time.']);
        }
    }
    

    public function scheduleAdsForTv($tv_id, $date)
    {
        try {
            // Fetch the TV's operating time for the given date
            $tvDisplayTime = TvDisplayTime::where('tv_id', $tv_id)
                                          ->where('date', $date)
                                          ->first();
    
            if (!$tvDisplayTime) {
                Log::error("No operating time found for TV ID: $tv_id on date: $date");
                return false;
            }
    
            // Fetch the ads scheduled for this TV, sorted by the order in the ad_schedules table
            $adSchedules = AdSchedule::where('tv_id', $tv_id)
                                     ->whereDate('start_at', '<=', $date)
                                     ->whereDate('end_at', '>=', $date)
                                     ->orderBy('order', 'asc')
                                     ->get();
    
            if ($adSchedules->isEmpty()) {
                Log::error("No ads found for TV ID: $tv_id on date: $date");
                return false;
            }
    
            // Calculate the total duration of all ads
            $totalAdsDuration = 0;
            foreach ($adSchedules as $adSchedule) {
                $ad = Advertisement::find($adSchedule->advertisement_id);
                if ($ad) {
                    $totalAdsDuration += $this->convertDurationToSeconds($ad->video_duration);
                }
            }
    
            // Start calculating the ad display times
            $currentTime = strtotime($tvDisplayTime->start_time);
            $endTime = strtotime($tvDisplayTime->end_time);
    
            Log::info("TV ID: $tv_id Operating from: " . date('H:i:s', $currentTime) . " to " . date('H:i:s', $endTime));
    
            // Repeat playing ads until TV's operating time is reached
            while ($currentTime < $endTime) {
                foreach ($adSchedules as $adSchedule) {
                    try {
                        // Fetch the ad's video duration from the advertisements table
                        $ad = Advertisement::find($adSchedule->advertisement_id);
                        if (!$ad) {
                            Log::error("Ad not found for AdSchedule ID: {$adSchedule->id}");
                            continue;
                        }
    
                        $videoDurationInSeconds = $this->convertDurationToSeconds($ad->video_duration);
                        Log::info("Ad ID: {$ad->id}, Video Duration in seconds: $videoDurationInSeconds");
    
                        // Check if the current time + video duration goes beyond TV operating time
                        if ($currentTime + $videoDurationInSeconds > $endTime) {
                            // Display part of the ad until the TV closes
                            $remainingTime = $endTime - $currentTime;
                            $displayTime = date('H:i:s', $currentTime);
    
                            Log::info("Displaying part of Ad ID: {$ad->id} from $displayTime until TV closes at " . date('H:i:s', $endTime));
    
                            // Store the partial ad display time
                            AdDisplayTime::create([
                                'ad_schedule_id' => $adSchedule->id,
                                'display_date' => $date,
                                'display_time' => $displayTime,
                            ]);
    
                            // TV closes at this point, so break the loop
                            return true;
                        }
    
                        // Convert the current time back to H:i:s format for full ad display
                        $displayTime = date('H:i:s', $currentTime);
                        Log::info("Storing ad display time: TV ID: $tv_id, Ad Schedule ID: {$adSchedule->id}, Display Time: $displayTime");
    
                        // Store the ad display time in the ad_display_times table
                        AdDisplayTime::create([
                            'ad_schedule_id' => $adSchedule->id,
                            'display_date' => $date,
                            'display_time' => $displayTime,
                        ]);
    
                        // Update the current time by adding the video duration
                        $currentTime += $videoDurationInSeconds;
    
                        // Break the loop if the current time exceeds the end time of TV operation
                        if ($currentTime >= $endTime) {
                            Log::info("TV operating time exceeded for TV ID: $tv_id");
                            break;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error while processing AdSchedule ID: {$adSchedule->id} - " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error scheduling ads for TV ID: $tv_id on date: $date - " . $e->getMessage());
        }
    
        return true;
    } 

    /**
     * Helper function to convert H:i:s duration to seconds
     */
    private function convertDurationToSeconds($duration)
    {
        $parts = explode(':', $duration);
        return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
    }

    public function destroy($id)
    {
        // Find the display time and delete it
        $displayTime = TvDisplayTime::findOrFail($id);
        $displayTime->delete();

        return redirect()->route('tv_display_times.index')->with('success', 'TV Display Time deleted successfully.');
    }
}
