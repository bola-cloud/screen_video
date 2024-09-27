<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdSchedule;
use App\Http\Controllers\Admin\TvDisplayTimeController;
use App\Models\TvDisplayTime;

class AdOrderController extends Controller
{
    public function show($tv_id, Request $request)
    {
        $date = $request->input('date');

        // Fetch ads only if a date is selected
        $ads = [];
        if ($date) {
            $ads = AdSchedule::where('tv_id', $tv_id)
                ->where('date', $date) // Filter by selected date
                ->orderBy('order', 'asc')
                ->with('advertisement')
                ->get()
                ->toArray();
        }
        return view('admin.tvs.ad-order', compact('ads', 'tv_id'));
    }

    public function updateOrder(Request $request)
    {
        $orderedAds = $request->input('order');
        $date = $request->input('date');
        $tv_id=$request->input('tv_id');
        foreach ($orderedAds as $order) {
            // Update the order for the specific ad and date
            AdSchedule::where('id', $order['id'])
                ->where('date', $date) // Ensure it's for the correct date
                ->update(['order' => $order['order']]);
        }
        $tvHasTime=TvDisplayTime::where('tv_id',$tv_id)->where('date',$date)->first();
        if($tvHasTime){
            $ad_sschedule=AdSchedule::where('date', $date)->where('tv_id', $tv_id)->with('displayTimes')->get();
            $ad_sschedule->each(function ($item, $key) {
                $item->displayTimes()->delete();
            });
            $tvDisplayController = new TvDisplayTimeController();
            $tvDisplayController->scheduleAdsForTv($tv_id, $date);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully for date ' . $date]);
    }
}
