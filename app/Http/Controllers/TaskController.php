<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show tasks assigned to user or created by user's projects?
        // Let's show tasks assigned to the current user
        $tasks = Auth::user()->assignedTasks()->with('project')->latest()->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Admins can create tasks for any project, users for their own
        $projects = Auth::user()->role === 'admin' ? Project::all() : Auth::user()->projects;
        // And assign to any user (or maybe just restrict to system users)
        $users = User::all();
        $selectedProjectId = $request->input('project_id');
        return view('tasks.create', compact('projects', 'users', 'selectedProjectId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'cost' => 'nullable|numeric|min:0',
        ]);

        // Verify permissions
        $project = Project::findOrFail($validated['project_id']);
        if (Auth::user()->role !== 'admin' && $project->user_id !== Auth::id()) {
            abort(403);
        }

        $task = Task::create($validated);

        if ($request->has('assigned_to')) {
            $task->assignees()->sync($validated['assigned_to']);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task created successfully.');
    }

    public function details(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('comments.user', 'assignees', 'project');
        
        return response()->json([
            'task' => $task,
            'can_update' => auth()->user()->can('update', $task),
            'can_delete' => auth()->user()->can('delete', $task),
            'current_user_id' => auth()->id()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('comments.user', 'assignees', 'project');
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $projects = Auth::user()->role === 'admin' ? Project::all() : Auth::user()->projects;
        $users = User::all();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $task->update($validated);

        if ($request->has('assigned_to')) {
            $task->assignees()->sync($validated['assigned_to']);
        } else {
            $task->assignees()->detach();
        }

        return redirect()->route('projects.show', $task->project_id)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $projectId = $task->project_id;
        $task->delete();

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Get POAC logs for a task
     */
    public function getPoacLogs(Task $task)
    {
        // Load project relationship for authorization
        $task->load('project', 'assignees');
        
        // Temporarily disable authorization to debug
        // $this->authorize('view', $task);
        
        $logs = $task->poacLogs()->with('user')->get()->map(function($log) {
            return [
                'id' => $log->id,
                'phase' => $log->phase,
                'title' => $log->title,
                'description' => $log->description,
                'created_at' => $log->created_at->format('d/m/Y H:i'),
                'user_name' => $log->user ? $log->user->name : 'Unknown'
            ];
        });

        return response()->json(['logs' => $logs]);
    }
}
