<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Models\DepartmentMember;
use App\Models\Meeting;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['parent', 'children', 'members'])->latest()->paginate(10);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments|max:255',
            'description' => 'nullable',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        Department::create($data);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['parent', 'children', 'members', 'meetings' => function($query) {
            $query->latest('meeting_date')->limit(5);
        }]);
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        // Get all departments except self and descendants (to prevent circular reference)
        $descendants = $department->allDescendants()->pluck('id')->push($department->id);
        $departments = Department::whereNotIn('id', $descendants)->orderBy('name')->get();
        return view('departments.edit', compact('department', 'departments'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        // Prevent circular reference
        if ($request->parent_id) {
            $descendants = $department->allDescendants()->pluck('id');
            if ($descendants->contains($request->parent_id)) {
                return back()->withErrors(['parent_id' => 'Cannot set a descendant as parent.']);
            }
        }

        $data = $request->all();
        if ($department->name !== $request->name) {
            $data['slug'] = Str::slug($request->name);
        }

        $department->update($data);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    // Public Landing Page
    public function landingPage($slug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        
        // Load members and recent meetings for authenticated users
        if (auth()->check()) {
            $department->load([
                'members',
                'meetings' => function($query) {
                    $query->latest('meeting_date')->limit(5);
                }
            ]);

            // Fetch recent projects from department members
            $memberIds = $department->members()->pluck('users.id');
            $department->recent_projects = \App\Models\Project::whereIn('user_id', $memberIds)
                ->with(['user', 'status'])
                ->latest()
                ->limit(5)
                ->get();
        }
        
        return view('departments.landing', compact('department'));
    }

    // Member Management
    public function members(Department $department)
    {
        $department->load('members');
        $users = User::orderBy('name')->get();
        return view('departments.members', compact('department', 'users'));
    }

    public function addMember(Request $request, Department $department)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|max:255',
        ]);

        // Check if already a member
        if ($department->members()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'User is already a member of this department.');
        }

        $department->members()->attach($request->user_id, [
            'role' => $request->role,
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Department $department, $userId)
    {
        $department->members()->detach($userId);
        return back()->with('success', 'Member removed successfully.');
    }

    // Public Meeting Pages for Department Landing
    public function createMeetingPublic($slug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        return view('departments.public-meeting-create', compact('department'));
    }

    public function showMeetingPublic($slug, $meetingId)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        $meeting = Meeting::where('id', $meetingId)
            ->where('department_id', $department->id)
            ->with(['creator', 'attendances.user'])
            ->firstOrFail();
        return view('departments.public-meeting-show', compact('department', 'meeting'));
    }

    public function showProjectPublic($slug, $projectSlug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        
        $project = \App\Models\Project::where('slug', $projectSlug)
            ->whereIn('user_id', $department->members()->pluck('users.id'))
            ->with(['tasks.assignees', 'status', 'tickets'])
            ->firstOrFail();

        // Ensure user can view this
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('departments.public-project-show', compact('department', 'project'));
    }

    public function updateMgmtPhase(Request $request, $slug, $projectSlug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        
        // Ensure user is a member of the department
        if (!auth()->check() || !$department->members->contains(auth()->user())) {
            abort(403);
        }

        $project = \App\Models\Project::where('slug', $projectSlug)->firstOrFail();
        
        $request->validate([
            'mgmt_phase' => 'required|in:Planning,Organizing,Actuating,Controlling',
            'phase_name' => 'required|in:Planning,Organizing,Actuating,Controlling',
            'notes' => 'required',
        ]);

        $phase = $request->phase_name;
        $column = 'mgmt_' . strtolower($phase) . '_notes';

        $project->update([
            'mgmt_phase' => $request->mgmt_phase,
            $column => $request->notes
        ]);

        return back()->with('success', 'Management phase ' . $phase . ' updated successfully.');
    }

    public function updateTaskMgmt(Request $request, $slug, $projectSlug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        
        // Ensure user is a member of the department
        if (!auth()->check() || !$department->members->contains(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'mgmt_phase' => 'required|in:Planning,Organizing,Actuating,Controlling',
            'mgmt_notes' => 'nullable',
        ]);

        $task = \App\Models\Task::findOrFail($request->task_id);
        
        $task->update([
            'mgmt_phase' => $request->mgmt_phase,
            'mgmt_notes' => $request->mgmt_notes
        ]);

        return back()->with('success', 'Task management info updated successfully.');
    }
}
