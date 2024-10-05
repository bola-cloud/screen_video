<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TvDisplayTime;
use App\Models\Tv;
use App\Models\AdSchedule;
use App\Models\Advertisement;  // Add this line to import the Advertisement model
use App\Models\AdDisplayTime;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TvDisplayTimeController extends Controller
{
    public function index(Request $request)
    {
        // Fetch the query parameters
        $tv_name = $request->input('tv_name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
    
        // Build the query
        $query = TvDisplayTime::with('tv');
    
        // Filter by TV name
        if ($tv_name) {
            $query->whereHas('tv', function ($q) use ($tv_name) {
                $q->where('name', 'like', "%{$tv_name}%");
            });
        }
    
        // Filter by date range
        if ($start_date && $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->where('date', '>=', $start_date);
        } elseif ($end_date) {
            $query->where('date', '<=', $end_date);
        }
    
        // Filter by start time
        if ($start_time) {
            $query->where('start_time', '>=', $start_time);
        }
    
        // Filter by end time
        if ($end_time) {
            $query->where('end_time', '<=', $end_time);
        }
    
        // Get the filtered display times with pagination
        $displayTimes = $query->orderBy('date', 'desc')->paginate(10);
    
        return view('admin.tv_display_times.index', compact('displayTimes'));
    } 

    public function create()
    {
        // Fetch all institutions with their active TVs
        $institutions = Institution::with(['tvs' => function ($query) {
            $query->where('is_active', 1); // Fetch only active TVs
        }])->get();
    
        // Return the create view with the list of institutions and their TVs
        return view('admin.tv_display_times.create', compact('institutions'));
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
                    return redirect()->back()->withErrors(['conflict' => __('lang.tv_conflict', ['name' => Tv::find($tv_id)->name])])->withInput();
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
    
            return redirect()->route('tv_display_times.index')->with('success', __('lang.tv_display_time_added'));
        } catch (\Exception $e) {
            Log::error("Error in store method for TV Display Time - " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => __('lang.error_saving_tv_display_time')]);
        }
    }
    

    public function edit($id)
    {
        // Get the display time to be edited
        $displayTime = TvDisplayTime::findOrFail($id);
        
        // Fetch all TVs for selection
        $tvs = Tv::where('id', $displayTime->tv_id)->get();
        
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
            $oldDate = $displayTime->date;
    
            // Check for conflicts before updating
            foreach ($data['tvs'] as $tv_id) {
                $existingDisplayTime = TvDisplayTime::where('tv_id', $tv_id)
                    ->where('date', $data['date'])
                    ->where('id', '!=', $id)
                    ->first();
    
                if ($existingDisplayTime) {
                    return redirect()->back()->withErrors(['conflict' => __('lang.tv_conflict', ['name' => Tv::find($tv_id)->name])])->withInput();
                }
            }
    
            // Delete existing AdDisplayTimes for the old date and TV
            foreach ($data['tvs'] as $tv_id) {
                // Get all related AdSchedules for the old date and TV
                $schedules = AdSchedule::where('tv_id', $tv_id)->where('date', $oldDate)->get();
    
                foreach ($schedules as $schedule) {
                    // Delete related AdDisplayTimes
                    AdDisplayTime::where('ad_schedule_id', $schedule->id)->delete();
                }
            }
    
            // Update the TvDisplayTime with new data
            $displayTime->update([
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time']
            ]);
    
            // Recreate TvDisplayTime and recalculate ads for each TV
            foreach ($data['tvs'] as $tv_id) {
                // Recalculate ads for the updated TV and date
                $this->scheduleAdsForTv($tv_id, $data['date']);
            }
    
            return redirect()->route('tv_display_times.index')->with('success', __('lang.tv_display_time_updated'));
        } catch (\Exception $e) {
            Log::error("Error in update method for TV Display Time - " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => __('lang.error_updating_tv_display_time')]);
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
    
            // Fetch the ads scheduled for this TV on the given date, sorted by order in the ad_schedules table
            $adSchedules = AdSchedule::where('tv_id', $tv_id)
                                     ->where('date', $date)
                                     ->orderBy('order', 'asc')
                                     ->get();
    
            if ($adSchedules->isEmpty()) {
                Log::error("No ads found for TV ID: $tv_id on date: $date");
                return false;
            }
    
            // Calculate the total available time (in seconds) from start to end time
            $currentTime = strtotime($tvDisplayTime->start_time);
            $endTime = strtotime($tvDisplayTime->end_time);
    
            Log::info("TV ID: $tv_id Operating from: " . date('H:i:s', $currentTime) . " to " . date('H:i:s', $endTime));
    
            // Create an array to keep track of remaining turns for each ad
            $remainingTurns = [];
            foreach ($adSchedules as $adSchedule) {
                $remainingTurns[$adSchedule->id] = $adSchedule->turns ?? 1; // Default to 1 turn if not set
            }
    
            // Schedule ads while there is available time and turns remaining
            while ($currentTime < $endTime) {
                $allTurnsCompleted = true;
    
                foreach ($adSchedules as $adSchedule) {
                    // Check if this ad still has turns remaining
                    if ($remainingTurns[$adSchedule->id] > 0) {
                        $allTurnsCompleted = false;
    
                        // Fetch the ad's video duration
                        $ad = Advertisement::find($adSchedule->advertisement_id);
                        if (!$ad) {
                            Log::error("Ad not found for AdSchedule ID: {$adSchedule->id}");
                            continue;
                        }
    
                        $videoDurationInSeconds = $this->convertDurationToSeconds($ad->video_duration);
    
                        // Check if there's enough time left to display the ad
                        if ($currentTime + $videoDurationInSeconds > $endTime) {
                            Log::info("Stopping because TV operating time will end. Current Time: " . date('H:i:s', $currentTime));
                            return true;
                        }
    
                        // Store the display time for the ad
                        $displayTime = date('H:i:s', $currentTime);
                        Log::info("Scheduling Ad ID: {$ad->id}, Display Time: {$displayTime}, Remaining Turns: {$remainingTurns[$adSchedule->id]}");
    
                        AdDisplayTime::create([
                            'ad_schedule_id' => $adSchedule->id,
                            'display_date' => $date,
                            'display_time' => $displayTime,
                        ]);
    
                        // Decrease the number of remaining turns for this ad
                        $remainingTurns[$adSchedule->id]--;
    
                        // Update the current time by adding the video duration
                        $currentTime += $videoDurationInSeconds;
    
                        // If the current time exceeds the TV operating time, stop
                        if ($currentTime >= $endTime) {
                            Log::info("Stopping because TV operating time has exceeded. Current Time: " . date('H:i:s', $currentTime));
                            return true;
                        }
                    }
                }
    
                // If all ads have finished their turns, break the loop
                if ($allTurnsCompleted) {
                    Log::info("All ads have finished their scheduled turns.");
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error scheduling ads for TV ID: $tv_id on date: $date - " . $e->getMessage());
            return false;
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
        $schedules = AdSchedule::where('tv_id',$displayTime->tv_id)
                                ->where('date',$displayTime->date)
                                ->with('displayTimes')->get();
        foreach ($schedules as $schedule) {
            $schedule->displayTimes()->delete();
        }       
        $displayTime->delete();

        return redirect()->route('tv_display_times.index')->with('success', __('lang.tv_display_time_deleted'));
    }
}
