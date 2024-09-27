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
        $tvsCount = Tv::count();
        $adsCount = Advertisement::count();

        $ads = Advertisement::query();

        // Check if date filters are provided
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Filter by date range if provided
        if ($startDate && $endDate) {
            $ads->whereHas('schedules', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            });
        }

        // Fetch the ads and their schedules within the date range
        $filteredAds = $ads->with(['schedules' => function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        }])->get();

        return view('admin.dashboard', compact('tvsCount', 'adsCount', 'filteredAds'));
    }

}
