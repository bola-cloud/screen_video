<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tv;
use Illuminate\Http\Request;

class TvController extends Controller
{
    // Display the list of TVs
    public function index()
    {
        $tvs = Tv::all();
        return view('admin.tvs.index', compact('tvs'));
    }

    // Show the form to create a new TV
    public function create()
    {
        return view('admin.tvs.create');
    }

    // Store a new TV in the database
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        // Get the maximum screen_id and increment it for the new TV
        $maxScreenId = Tv::max('screen_id');
        $data['screen_id'] = $maxScreenId ? $maxScreenId + 1 : 1;

        Tv::create($data);
        return redirect()->route('tvs.index')->with('success', 'TV added successfully');
    }


    // Show the form to edit a TV
    public function edit(Tv $tv)
    {
        return view('admin.tvs.edit', compact('tv'));
    }

    // Update a TV in the database
    public function update(Request $request, Tv $tv)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $tv->update($data);
        return redirect()->route('tvs.index')->with('success', 'TV updated successfully');
    }

    // Delete a TV from the database
    public function destroy(Tv $tv)
    {
        $tv->delete();
    
        // Reorder the screen_ids after deletion
        $this->reorderScreenIds();
    
        return redirect()->route('tvs.index')->with('success', 'TV deleted successfully');
    }
    
    // Function to reorder screen_ids after a deletion
    protected function reorderScreenIds()
    {
        $tvs = Tv::orderBy('id')->get();
        $screenId = 1;
    
        foreach ($tvs as $tv) {
            $tv->update(['screen_id' => $screenId]);
            $screenId++;
        }
    }
    
    public function activateTv(Request $request, $id)
    {
        // Deactivate all other TVs
        Tv::where('is_active', 1)->update(['is_active' => 0]);

        // Activate the selected TV
        $tv = Tv::findOrFail($id);
        $tv->is_active = $request->is_active;
        $tv->save();

        return response()->json(['success' => true, 'message' => 'TV activation updated.']);
    }

}
