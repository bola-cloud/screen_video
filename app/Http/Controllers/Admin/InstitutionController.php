<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institution;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Institution::query();

        if ($search) {
            $query->where('name', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
        }

        $institutions = $query->paginate(10);

        return view('admin.institutions.index', compact('institutions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        Institution::create($request->all());

        return redirect()->route('institutions.index')->with('success', __('lang.success_message'));
    }

    public function edit($id)
    {
        $institution = Institution::findOrFail($id);
        return response()->json($institution);
    }

    public function update(Request $request, $id)
    {
        $institution = Institution::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $institution->update($request->all());

        return redirect()->route('institutions.index')->with('success', __('lang.success_message'));
    }

    public function destroy($id)
    {
        $institution = Institution::findOrFail($id);
        $institution->delete();

        return redirect()->route('institutions.index')->with('success', __('lang.success_message'));
    }
}
