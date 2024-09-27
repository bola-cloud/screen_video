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
                    ->where('id','!=',$id)
                    ->first();
    
                if ($existingDisplayTime) {
                    return redirect()->back()->withErrors(['conflict' => 'TV "' . Tv::find($tv_id)->name . '" already has a display time for this date.'])->withInput();
                }
            }
        
            $displayTime->delete();
            $sheadule_id=AdSchedule::where('tv_id',$tv_id)->where('date',$oldDate)->first();
            AdDisplayTime::where('ad_schedule_id',$sheadule_id->id)->delete();
            foreach ($data['tvs'] as $tv_id) {
                TvDisplayTime::create([
                    'tv_id' => $tv_id,
                    'date' => $data['date'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                ]);
                
                $this->scheduleAdsForTv($tv_id, $data['date']);
                // dd("s");
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
            // Fetch the ads scheduled for this TV on the given date, sorted by order in the ad_schedules table
            $adSchedules = AdSchedule::where('tv_id', $tv_id)
                                     ->where('date', $date)
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

            // Start calculating the ad display times based on the TV's operating time
            $currentTime = strtotime($tvDisplayTime->start_time);
            $endTime = strtotime($tvDisplayTime->end_time);
    
            Log::info("TV ID: $tv_id Operating from: " . date('H:i:s', $currentTime) . " to " . date('H:i:s', $endTime));

            // Repeat playing ads until the TV's operating time is reached
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
    
                        // Check if the current time + video duration exceeds the TV's operating time
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
        $schedules = AdSchedule::where('tv_id',$displayTime->tv_id)
                                ->where('date',$displayTime->date)
                                ->with('displayTimes')->get();
        foreach ($schedules as $schedule) {
            $schedule->displayTimes()->delete();
        }       
        $displayTime->delete();

        return redirect()->route('tv_display_times.index')->with('success', 'TV Display Time deleted successfully.');
    }
}
