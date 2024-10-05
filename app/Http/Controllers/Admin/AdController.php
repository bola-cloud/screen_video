<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Tv;
use App\Models\AdSchedule;
use App\Models\AdDisplayTime;
use App\Models\TvDisplayTime;
use App\Models\Institution;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\TvDisplayTimeController;

class AdController extends Controller
{
    public function index(Request $request)
    {
        // Get the search input
        $search = $request->input('search');

        // Query advertisements with optional search filtering
        $ads = Advertisement::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
        })
        ->orderBy('id', 'asc') // Optional: Adjust ordering if needed
        ->paginate(10); // Paginate the results for easier display

        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        $clients = \App\Models\User::where('category', 'client')->get();
        return view('admin.ads.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'video_duration' => 'required|date_format:H:i:s',
            'client_id' => 'required|exists:users,id',
        ]);

        $ad = Advertisement::create($data);

        return redirect()->route('ads.chooseTvs', $ad->id)->with('success', __('lang.ad_created_successfully'));
    }

    public function chooseTvs(Advertisement $ad)
    {
        // Get all institutions with their related TVs
        $institutions = Institution::with('tvs')->get();
    
        // Check if there are any TVs available
        if ($institutions->isEmpty() || $institutions->every(fn($institution) => $institution->tvs->isEmpty())) {
            return redirect()->route('ads.index')->with('error', __('lang.no_tvs_available'));
        }
    
        // Pass the advertisement and the institutions (with TVs) to the view
        return view('admin.ads.choose_tvs', compact('ad', 'institutions'));
    }
    

    public function storeTvs(Request $request, Advertisement $ad)
    {
        $data = $request->validate([
            'tvs' => 'sometimes|array',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'turns' => 'sometimes|array', // Validate turns input
        ]);
    
        $selectedTvs = $data['tvs'] ?? [];
        $turnsData = $data['turns'] ?? [];
    
        // Remove any AdSchedules that are no longer selected
        AdSchedule::where('advertisement_id', $ad->id)
            ->whereNotIn('tv_id', $selectedTvs)
            ->delete();
    
        foreach ($selectedTvs as $tv_id) {
            $startDate = new \DateTime($data['start_at']);
            $endDate = new \DateTime($data['end_at']);
            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($startDate, $interval, $endDate);
    
            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                $maxOrder = AdSchedule::where('tv_id', $tv_id)
                    ->where('date', $formattedDate)
                    ->max('order') ?? 0;
    
                $turns = $turnsData[$tv_id] ?? 1; // Default to 1 if no input is given
    
                AdSchedule::create([
                    'advertisement_id' => $ad->id,
                    'tv_id' => $tv_id,
                    'date' => $formattedDate,
                    'order' => $maxOrder + 1,
                    'turns' => $turns,
                ]);
    
                $this->recalculateDisplayTimes($tv_id, $formattedDate);
            }
        }
    
        return redirect()->route('ads.index')->with('success', __('lang.ad_tv_assignment_updated'));
    }
    

    public function updatescheduleads(Request $request,$ad)
    {   
        // dd($ad);
        $adDisplayTime =AdSchedule::where('advertisement_id',$ad)->delete();
        $data = $request->validate([
            'tvs' => 'sometimes|array',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
        ]);

        $selectedTvs = $data['tvs'] ?? [];

        AdSchedule::where('advertisement_id', $ad)
            ->whereNotIn('tv_id', $selectedTvs)
            ->delete();
            // dd($ad->id);
        // dd(AdSchedule::where('advertisement_id', $ad->id)
        // ->whereNotIn('tv_id', $selectedTvs)
        // ->delete());
        foreach ($selectedTvs as $tv_id) {
            $startDate = new \DateTime($data['start_at']);
            $endDate = new \DateTime($data['end_at']);
            $endDate->modify('+1 day');
            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($startDate, $interval, $endDate);

            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                $maxOrder = AdSchedule::where('tv_id', $tv_id)
                    ->where('date', $formattedDate)
                    ->max('order') ?? 0;

                $existingSchedule = AdSchedule::where('advertisement_id', $ad)
                    ->where('tv_id', $tv_id)
                    ->where('date', $formattedDate)
                    ->first();

                $adSchedule = AdSchedule::create([
                    'advertisement_id' => $ad,
                    'tv_id' => $tv_id,
                    'date' => $formattedDate,
                    'order' => $maxOrder + 1,
                ]);
                $this->recalculateDisplayTimes($tv_id, $formattedDate);

            }
        }

        return redirect()->route('ads.index')->with('success', __('lang.ad_tv_assignment_updated'));
    }

    public function edit(Advertisement $ad)
    {
        $assignedTvs = AdSchedule::with('tv')
            ->where('advertisement_id', $ad->id)
            ->get();

        $tvs = Tv::where('is_active', 1)->get();
        $clients = \App\Models\User::where('category', 'client')->get();

        $firstDate = $assignedTvs->min('date');
        $lastDate = $assignedTvs->max('date');
        $institutions=Institution::all();
        return view('admin.ads.edit', compact('ad', 'assignedTvs', 'tvs', 'clients', 'firstDate', 'lastDate','institutions'));
    }

    public function update(Request $request, Advertisement $ad)
    {
        // Validate and update the basic ad information
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'video_duration' => 'required|date_format:H:i:s',
            'client_id' => 'required|exists:users,id'
        ]);
        // Update the advertisement details
        $ad->update($data);
        // Fetch the existing `tv_id`s related to the advertisement_id
        $schedules = AdSchedule::where('advertisement_id', $ad->id)->get();

        foreach ($schedules as $schedule) {
            // Check if the schedule has any related displayTimes
            if ($schedule->displayTimes->isNotEmpty()) {
                $schedule->displayTimes()->delete();
                $this->recalculateDisplayTimes($schedule->tv_id, $schedule->date);
            }
        }

        // // Remove existing schedules for this ad
        // AdSchedule::where('advertisement_id', $ad->id)->delete();

        // // Process the `tvs` array and create schedules
        // foreach ($scheduleData['tvs'] as $tv_id) {
        //     $formattedDate = $scheduleData['date'];

        //     // Get the maximum order for the selected TV and for this specific date
        //     $maxOrder = AdSchedule::where('tv_id', $tv_id)
        //         ->where('date', $formattedDate)
        //         ->max('order') ?? 0;

        //     // Create a new schedule for this specific date
        //     AdSchedule::create([
        //         'advertisement_id' => $ad->id,
        //         'tv_id' => $tv_id,
        //         'date' => $formattedDate,
        //         'order' => $maxOrder + 1,
        //     ]);

        //     // Recalculate display times if needed
        //     $this->recalculateDisplayTimes($tv_id, $formattedDate);
        // }
    
        return redirect()->route('ads.edit', $ad->id)->with('success', __('lang.ad_updated_with_schedules'));
    }

    public function addSingleDay(Request $request, $ad_id)
    {
        // Validate the incoming data
        $data = $request->validate([
            'tvs_single_day' => 'required|array|min:1',
            'tvs_single_day.*' => 'exists:tvs,id',
            'date' => 'required|date',
            'turns' => 'required|array',  // Validate turns array
            'turns.*' => 'integer|min:1'  // Each turn must be a positive integer
        ]);
    
        $ad = Advertisement::findOrFail($ad_id);
    
        foreach ($data['tvs_single_day'] as $tv_id) {
            $turns = $data['turns'][$tv_id] ?? 1; // Default to 1 if no turns specified
    
            // Check if a schedule already exists for the TV and date
            $existingSchedule = AdSchedule::where('advertisement_id', $ad_id)
                ->where('tv_id', $tv_id)
                ->where('date', $data['date'])
                ->first();
    
            if ($existingSchedule) {
                continue; // Skip if the schedule exists
            }
    
            // Get the max order for this TV and date
            $maxOrder = AdSchedule::where('tv_id', $tv_id)
                ->where('date', $data['date'])
                ->max('order') ?? 0;
    
            // Create the new schedule
            AdSchedule::create([
                'advertisement_id' => $ad_id,
                'tv_id' => $tv_id,
                'date' => $data['date'],
                'order' => $maxOrder + 1,
                'turns' => $turns // Store the turns value
            ]);
    
            // Recalculate display times for the TV and date
            $this->recalculateDisplayTimes($tv_id, $data['date']);
        }
    
        return redirect()->route('ads.edit', $ad_id)->with('success', __('lang.single_day_added_successfully'));
    }
    

    public function deleteSchedule($ad_id, $schedule_id)
    {
        // Fetch the schedule to delete
        $schedule = AdSchedule::findOrFail($schedule_id);
    
        // Delete related display times
        if ($schedule->displayTimes()->exists()) {
            $schedule->displayTimes()->delete();
        }
    
        // Delete the schedule itself
        $schedule->delete();
    
        // Recalculate display times for the TV and date
        $this->recalculateDisplayTimes($schedule->tv_id, $schedule->date);
    
        return redirect()->route('ads.edit', $ad_id)->with('success', __('lang.schedule_deleted_successfully'));
    }
    
    private function recalculateDisplayTimes($tv_id, $date)
    {
        // Delete existing entries in ad_display_times for the given TV and date
        AdDisplayTime::whereHas('adSchedule', function ($query) use ($tv_id, $date) {
            $query->where('tv_id', $tv_id)->where('date', $date);
        })->delete();
    
        // Recalculate and reschedule ads for the TV on the given date
        $tvDisplayController = new TvDisplayTimeController();
        $tvDisplayController->scheduleAdsForTv($tv_id, $date);
    }   
    
    public function destroy($advertisement_id)
    {
        // Fetch the advertisement
        $advertisement = Advertisement::findOrFail($advertisement_id);

        // Fetch all schedules related to this ad
        $schedules = $advertisement->schedules;

        // Delete the advertisement and its schedules
        $advertisement->delete();

        // Loop through each schedule and rearrange the ads for that TV and date
        foreach ($schedules as $schedule) {
            $this->rearrangeAdsForDate($schedule->tv_id, $schedule->date);
        }

        return redirect()->back()->with('success', __('lang.ad_deleted_successfully'));
    }
    public function rearrangeAdsForDate($tv_id, $date)
    {
        // Fetch the TV's display time for the specific date
        $tvDisplayTime = TvDisplayTime::where('tv_id', $tv_id)
            ->where('date', $date)
            ->first();

        if ($tvDisplayTime) {
            // Convert start and end times to timestamps
            $start_time = strtotime($tvDisplayTime->start_time);
            $end_time = strtotime($tvDisplayTime->end_time);
            $schedules = AdSchedule::where('tv_id', $tv_id)
                                    ->where('date', $date)
                                    ->with('advertisement')  // Include related advertisement for video_duration
                                    ->orderBy('order')       // Order based on 'order' column
                                    ->get();
            foreach ($schedules as $schedule) {
                // Delete related displayTimes
                $schedule->displayTimes()->delete();
            }
            $tvDisplayController = new TvDisplayTimeController();
            $tvDisplayController->scheduleAdsForTv($tv_id, $date);
            // // Fetch all remaining ad schedules for the TV and date
            // $schedules = AdSchedule::where('tv_id', $tv_id)
            //     ->where('date', $date)
            //     ->with('advertisement')  // Include related advertisement for video_duration
            //     ->orderBy('order')       // Order based on 'order' column
            //     ->get();

            // // Start with the TV schedule's start time
            // $current_time = $start_time;
            // $new_order = 1; // Start the new order at 1

            // // Loop through each schedule and update the display times and order based on video duration
            // foreach ($schedules as $schedule) {
            //     // Get the ad's video duration (in seconds) from the related advertisement
            //     $video_duration = $this->convertDurationToSeconds($schedule->advertisement->video_duration);

            //     // Ensure we don't exceed the TV schedule's end time
            //     if ($current_time + $video_duration > $end_time) {
            //         break; // If adding this ad would exceed the end time, stop.
            //     }

            //     // Debugging log: Check current time and video duration for each ad
            //     \Log::info('Current Time: '.date('H:i:s', $current_time).' | Video Duration: '.$video_duration.' seconds');

            //     // Calculate the display time for the current ad (convert current_time to H:i:s format)
            //     $display_timee = date('H:i:s', $current_time);  // Ensure current_time is formatted correctly

            //     // Update all associated AdDisplayTime entries with the new display time
            //     foreach ($schedule->displayTimes as $displayTime) {
            //         $displayTime->update([
            //             'display_time' => $display_timee,  // Properly formatted display time
            //         ]);
            //     $current_time += $video_duration;
            //     }

            //     // Update the order of the ad schedule
            //     $schedule->update([
            //         'order' => $new_order,  // Update the order of the schedule
            //     ]);

            //     // Increment the current time by the video duration of the current ad
            //     $current_time += $video_duration;
            //     $new_order++; // Increment the order for the next ad
            // }
        }
    }

    public function convertDurationToSeconds($video_duration)
    {
        $parts = explode(':', $video_duration);
        $hours = isset($parts[0]) ? (int)$parts[0] : 0;
        $minutes = isset($parts[1]) ? (int)$parts[1] : 0;
        $seconds = isset($parts[2]) ? (int)$parts[2] : 0;

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }


    public function activateAd(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->is_active = $request->is_active;
        $ad->save();

        return response()->json(['success' => true, 'message' => __('lang.ad_activation_updated')]);
    }
}
