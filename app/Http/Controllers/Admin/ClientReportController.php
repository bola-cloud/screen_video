<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\User;

class ClientReportController extends Controller
{
    public function index(Request $request)
    {
       // Fetch all clients for the select2 dropdown
       $clients = User::where('category', 'client')->get();

       // Fetch the selected client if the form is submitted
       $selectedClient = null;
       $ads = collect();
       if ($request->has('client')) {
           $selectedClient = User::findOrFail($request->input('client'));

           // Fetch ads that belong to the selected client along with TVs and their schedule times
           $ads = Advertisement::where('client_id', $selectedClient->id)
               ->with(['schedules.tv', 'schedules.displayTimes'])
               ->get();
       }

        return view('admin.reports.client', compact('clients', 'selectedClient', 'ads'));
    }
}
