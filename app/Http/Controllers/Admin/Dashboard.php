<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tv;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index(Request $request)
    {
        // Fetch all ads for the select2 dropdown
        $ads = Advertisement::all();

        // Variables for statistics
        $tvsCount = Tv::count();
        $adsCount = Advertisement::count();

        // Fetch the selected ad
        $selectedAd = null;
        $selectedTvs = collect();
        if ($request->has('ad')) {
            $selectedAd = Advertisement::with('schedules.tv', 'schedules.displayTimes')
                ->findOrFail($request->input('ad'));

            // Fetch only the TVs that will display this ad
            $selectedTvs = Tv::whereHas('schedules', function ($query) use ($selectedAd) {
                $query->where('advertisement_id', $selectedAd->id);
            })->with(['schedules' => function ($query) use ($selectedAd) {
                $query->where('advertisement_id', $selectedAd->id)->with('displayTimes');
            }])->get();
        }

        return view('admin.dashboard', compact('ads', 'tvsCount', 'adsCount', 'selectedAd', 'selectedTvs'));
    }
}
