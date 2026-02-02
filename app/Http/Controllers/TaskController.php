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
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // Admin sees all tasks
            $tasks = Task::with('project', 'assignees')->latest()->paginate(10);
        } else {
            // Get user's department IDs
            $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
            
            // Get tasks that user can access:
            // 1. Tasks assigned to user
            // 2. Tasks from projects in user's departments
            // 3. Tasks from projects owned by user
            $tasks = Task::with('project', 'assignees')
                ->where(function($query) use ($user, $userDepartmentIds) {
                    // Tasks assigned to user
                    $query->whereHas('assignees', function($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    // OR tasks from user's department projects
                    ->orWhereHas('project', function($q) use ($userDepartmentIds) {
                        $q->whereIn('department_id', $userDepartmentIds);
                    })
                    // OR tasks from user's own projects
                    ->orWhereHas('project', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })
                ->latest()
                ->paginate(10);
        }
        
        // Get POAC logs for tasks assigned to this user (created by this user)
        $poacLogs = \App\Models\PoacLog::where('user_id', $user->id)
            ->where('poacable_type', 'App\Models\Task')
            ->with(['poacable'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('tasks.index', compact('tasks', 'poacLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Admins can create tasks for any project
        // Users can create tasks for their own projects OR projects in their departments
        if (Auth::user()->role === 'admin') {
            $projects = Project::all();
        } else {
            $userDepartmentIds = Auth::user()->departments()->pluck('departments.id');
            $projects = Project::where('user_id', Auth::id())
                ->orWhereIn('department_id', $userDepartmentIds)
                ->get();
        }
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
        
        $isCreator = $project->user_id === Auth::id();
        $isDepartmentMember = $project->department_id && Auth::user()->departments()->where('departments.id', $project->department_id)->exists();
        
        if (Auth::user()->role !== 'admin' && !$isCreator && !$isDepartmentMember) {
            abort(403);
        }

        $taskData = $validated;
        $taskData['user_id'] = Auth::id();

        $task = Task::create($taskData);

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
        
        if (request()->ajax()) {
            return view('tasks.partials.modal_content', compact('task'));
        }
        
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
     * Update the task status only.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,test,check,done',
        ]);

        // Restriction for 'done' status
        if ($validated['status'] === 'done') {
            $isSpv = auth()->user()->departments()
                ->where('departments.id', $task->project->department_id)
                ->where('department_members.role', 'SPV')
                ->exists();
            
            if (auth()->user()->role !== 'admin' && !$isSpv) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only Admin or SPV can mark tasks as done.'
                    ], 403);
                }
                abort(403, 'Only Admin or SPV can mark tasks as done.');
            }
        }

        $oldStatus = $task->status;
        $task->update(['status' => $validated['status']]);

        // Automatically create a POAC log for status change
        $statusLabels = [
            'todo' => 'Todo',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'test' => 'Test',
            'check' => 'Check',
            'done' => 'Done'
        ];

        \App\Models\PoacLog::create([
            'user_id' => Auth::id(),
            'poacable_type' => 'App\Models\Task',
            'poacable_id' => $task->id,
            'phase' => 'Actuating',
            'title' => 'Status Updated',
            'description' => "Task status changed from " . ($statusLabels[$oldStatus] ?? $oldStatus) . " to " . ($statusLabels[$validated['status']] ?? $validated['status']) . "."
        ]);

        if ($request->ajax()) {
            $task->load('comments.user', 'assignees', 'project');
            return response()->json([
                'success' => true,
                'new_status' => $task->status,
                'status_label' => $statusLabels[$task->status] ?? ucfirst($task->status),
                'status_class' => $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'warning'),
                'modal_html' => view('tasks.partials.modal_content', compact('task'))->render()
            ]);
        }

        return back()->with('success', 'Task status updated successfully.');
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
