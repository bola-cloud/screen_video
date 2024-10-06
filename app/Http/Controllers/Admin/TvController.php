<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tv;
use App\Models\Institution;
use Illuminate\Http\Request;

class TvController extends Controller
{
    // Display the list of TVs
    public function index(Request $request)
    {
        $search = $request->input('search');
        $institutionId = $request->input('institution_id');
    
        // Fetch all institutions for the dropdown filter
        $institutions = Institution::all();
    
        // Build the query with optional search and institution filtering
        $tvs = Tv::when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('location', 'like', "%{$search}%");
            })
            ->when($institutionId, function($query, $institutionId) {
                return $query->where('institution_id', $institutionId);
            })
            ->orderBy('id', 'asc')
            ->paginate(10);
    
        // Handle AJAX requests by returning just the table rows and pagination
        if ($request->ajax()) {
            return response()->json([
                'tableRows' => view('admin.tvs.index', ['tvs' => $tvs])->render(),
                'pagination' => view('admin.tvs.pagination', ['tvs' => $tvs])->render()
            ]);
        }
    
        // Pass the institutions to the view along with the TVs
        return view('admin.tvs.index', compact('tvs', 'institutions'));
    }           

    // Show the form to create a new TV
    public function create()
    {
        // Fetch all institutions to populate the select dropdown
        $institutions = Institution::all();
        return view('admin.tvs.create', compact('institutions'));
    }

    // Store a new TV in the database
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id', // Validate institution_id
        ]);
    
        // Get the maximum screen_id and increment it for the new TV
        $maxScreenId = Tv::max('screen_id');
        $data['screen_id'] = $maxScreenId ? $maxScreenId + 1 : 1;
    	
        Tv::create($data);
        return redirect()->route('tvs.index')->with('success', __('messages.tv_added_successfully'));
    }


    // Show the form to edit a TV
    public function edit(Tv $tv)
    {
        // Fetch all institutions to populate the select dropdown
        $institutions = Institution::all();
        return view('admin.tvs.edit', compact('tv', 'institutions'));
    }

    // Update a TV in the database
    public function update(Request $request, Tv $tv)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id', // Validate institution_id
        ]);
    
        $tv->update($data);
        return redirect()->route('tvs.index')->with('success', __('messages.tv_updated_successfully'));
    }

    // Delete a TV from the database
    public function destroy(Tv $tv)
    {
        $tv->delete();
    
        // Reorder the screen_ids after deletion
        $this->reorderScreenIds();
    
        return redirect()->route('tvs.index')->with('success', __('messages.tv_deleted_successfully'));
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
        // Activate the selected TV
        $tv = Tv::findOrFail($id);
        $tv->is_active = $request->is_active;
        $tv->save();

        return response()->json(['success' => true, 'message' => __('messages.tv_activation_updated')]);
    }

}
