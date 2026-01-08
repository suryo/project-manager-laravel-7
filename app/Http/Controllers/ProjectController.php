<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get projects owned by user OR where user is assigned to tasks
        if ($user->role === 'admin') {
            // Admin sees all projects
            $query = Project::with(['status', 'tasks.assignees']);
        } else {
            // Get project IDs where user is owner
            $ownedProjectIds = $user->projects()->pluck('id');
            
            // Get project IDs where user is assigned to tasks
            $assignedProjectIds = Project::whereHas('tasks.assignees', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->pluck('id');
            
            // Merge both collections
            $projectIds = $ownedProjectIds->merge($assignedProjectIds)->unique();
            
            $query = Project::whereIn('id', $projectIds)->with(['status', 'tasks.assignees']);
        }

        // Filter by Status
        if ($request->has('status_id') && $request->status_id != '') {
            $query->where('project_status_id', $request->status_id);
        }

        // Filter by Group
        if ($request->has('group') && $request->group != '') {
            $query->where('group', $request->group);
        }

        // Search by Title
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Per Page Limit
        $perPage = $request->get('limit', 10);
        
        $projects = $query->latest()->paginate($perPage)->withQueryString();
        
        $statuses = ProjectStatus::all();
        
        // Get groups from visible projects
        $groups = Project::whereIn('id', $projectIds ?? [])->whereNotNull('group')->where('group', '!=', '')->distinct()->pluck('group');

        return view('projects.index', compact('projects', 'statuses', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = ProjectStatus::all();
        return view('projects.create', compact('statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'project_status_id' => 'required|exists:project_statuses,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project = Auth::user()->projects()->create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['tasks.assignees', 'status']);
        $users = \App\Models\User::all();
        return view('projects.show', compact('project', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $statuses = ProjectStatus::all();
        return view('projects.edit', compact('project', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'project_status_id' => 'required|exists:project_statuses,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
