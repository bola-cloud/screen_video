<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AdSchedule;
use App\Http\Controllers\Admin\TvDisplayTimeController;

class AdOrder extends Component
{
    public $tv_id;
    public $ads = [];

    public function mount($tv_id)
    {
        // Load the ads related to this TV, ordered by the current order field
        $this->tv_id = $tv_id;
        $this->ads = AdSchedule::where('tv_id', $tv_id)->orderBy('order', 'asc')->get()->toArray();
    }

    public function saveOrder($orderedAds)
    {
        // Update the order in the database based on the new order
        foreach ($orderedAds as $ad) {
            AdSchedule::where('id', $ad['id'])->update(['order' => $ad['order']]);
        }

        // Optionally trigger the scheduleAdsForTv method to re-arrange ads display
        $controller = new TvDisplayTimeController();
        $controller->scheduleAdsForTv($this->tv_id, now()->format('Y-m-d'));

        session()->flash('message', 'Ad order updated and scheduled successfully.');
    }

    public function render()
    {
        return view('livewire.ad-order')
            ->extends('layouts.admin')
            ->section('content');
    }
}
