<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TvDisplayTime;
use App\Models\AdSchedule;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpMqtt\Client\Facades\MQTT;
use App\Http\Controllers\AdController;

class TriggerVideo extends Controller
{
    public function triggerVideo($tv_id,$order)
    {
      
      dd("33");
        try {
            // Validate the request data
            $data = $request->validate([
                'order' => 'required|integer',
                'tv_id' => 'required|integer',
            ]);
				
        $adController = new AdController();
        $adController->publishNextAd($tv_id,$order+1);
        }
    }
}
