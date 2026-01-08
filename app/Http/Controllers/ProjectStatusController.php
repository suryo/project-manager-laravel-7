<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use Illuminate\Http\Request;

class ProjectStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = ProjectStatus::all();
        return view('statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:project_statuses|max:255',
            'color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
        ]);

        ProjectStatus::create($validated);

        return redirect()->route('statuses.index')
            ->with('success', 'Status created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectStatus $status)
    {
        return view('statuses.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectStatus $status)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_statuses,name,' . $status->id,
            'color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
        ]);

        $status->update($validated);

        return redirect()->route('statuses.index')
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectStatus $status)
    {
        // Prevent deleting if used? Or nullify? Migration said nullOnDelete.
        $status->delete();

        return redirect()->route('statuses.index')
            ->with('success', 'Status deleted successfully.');
    }
}
