<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Tv;
use App\Models\AdSchedule;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index()
    {
        $ads = Advertisement::all();
        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.ads.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'video_duration' => 'required|date_format:H:i:s',
        ]);

        $ad = Advertisement::create($data);

        // Redirect to the TV assignment view after creating the ad
        return redirect()->route('ads.chooseTvs', $ad->id);
    }

    public function chooseTvs(Advertisement $ad)
    {
        // Pass the ad and all TVs to the view for TV assignment
        $tvs = Tv::all();
        return view('admin.ads.choose_tvs', compact('ad', 'tvs'));
    }

    public function storeTvs(Request $request, Advertisement $ad)
    {
        $data = $request->validate([
            'tvs' => 'sometimes|array', // Array of selected TV IDs (may not be provided if none selected)
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
        ]);
    
        $selectedTvs = $data['tvs'] ?? []; // Handle the case where no TV is selected
    
        // Remove relations for deselected TVs
        AdSchedule::where('advertisement_id', $ad->id)
            ->whereNotIn('tv_id', $selectedTvs)
            ->delete();
    
        foreach ($selectedTvs as $tv_id) {
            $existingSchedule = AdSchedule::where('advertisement_id', $ad->id)
                ->where('tv_id', $tv_id)
                ->first();
    
            if ($existingSchedule) {
                // Update the existing schedule
                $existingSchedule->update([
                    'start_at' => $data['start_at'],
                    'end_at' => $data['end_at'],
                ]);
            } else {
                // Get the maximum order for the selected TV
                $maxOrder = AdSchedule::where('tv_id', $tv_id)->max('order') ?? 0;
    
                // Create the schedule for the ad on the selected TV
                AdSchedule::create([
                    'advertisement_id' => $ad->id,
                    'tv_id' => $tv_id,
                    'start_at' => $data['start_at'],
                    'end_at' => $data['end_at'],
                    'order' => $maxOrder + 1, // Increment the order based on existing ads
                ]);
            }
        }
    
        return redirect()->route('ads.index')->with('success', 'Ad and TV assignments updated successfully!');
    }
    
    

    public function edit(Advertisement $ad)
    {
        // Get the TVs attached to the ad along with their schedules (start_at, end_at)
        $assignedTvs = AdSchedule::with('tv')
            ->where('advertisement_id', $ad->id)
            ->get();

        $tvs = Tv::all(); // Fetch all TVs for reassignment if needed

        return view('admin.ads.edit', compact('ad', 'assignedTvs', 'tvs'));
    }

    public function update(Request $request, Advertisement $ad)
    {
        // Validate and update the basic ad information
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'video_duration' => 'required|date_format:H:i:s',
        ]);
    
        $ad->update($data);
    
        // Check if there are TVs selected to update the schedule
        if ($request->has('tvs')) {
            $scheduleData = $request->validate([
                'tvs' => 'required|array',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after_or_equal:start_at',
            ]);
    
            foreach ($scheduleData['tvs'] as $tv_id) {
                // Check if there is already a schedule for this TV and ad
                $existingSchedule = AdSchedule::where('advertisement_id', $ad->id)
                    ->where('tv_id', $tv_id)
                    ->first();
    
                if ($existingSchedule) {
                    // Update the existing schedule for this TV and ad
                    $existingSchedule->update([
                        'start_at' => $scheduleData['start_at'],
                        'end_at' => $scheduleData['end_at'],
                    ]);
                } else {
                    // If no existing schedule, get the maximum order for the selected TV
                    $maxOrder = AdSchedule::where('tv_id', $tv_id)->max('order') ?? 0;
    
                    // Create the schedule if no duplicate is found
                    AdSchedule::create([
                        'advertisement_id' => $ad->id,
                        'tv_id' => $tv_id,
                        'start_at' => $scheduleData['start_at'],
                        'end_at' => $scheduleData['end_at'],
                        'order' => $maxOrder + 1, // Increment the order based on existing ads
                    ]);
                }
            }
        }
    
        return redirect()->route('ads.edit', $ad->id)->with('success', 'Ad updated successfully with new or updated schedules!');
    }
    


}
