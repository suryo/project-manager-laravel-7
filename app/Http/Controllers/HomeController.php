<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        if ($isAdmin) {
            $projectsCount = \App\Models\Project::count();
            $tasksCount = \App\Models\Task::where('status', '!=', 'done')->count();
            $pendingTasksCount = \App\Models\Task::where('status', 'todo')->count();
            $completedProjectsCount = \App\Models\Project::whereHas('status', function($q) {
                $q->where('name', 'Completed');
            })->count();
            
            $allProjects = \App\Models\Project::with(['status', 'user', 'tasks', 'department'])->latest()->get();
            $totalBudget = $allProjects->sum('budget');
            $totalActualCost = $allProjects->sum(function($p) { return $p->tasks->sum('cost'); });
            $overloadedStaff = \App\Models\User::withCount(['assignedTasks' => function($q) {
                $q->where('status', '!=', 'done');
            }])->having('assigned_tasks_count', '>', 3)->get();
            
            // Fetch all staff for Energy Monitor
            $staffMembers = \App\Models\User::with('departments')->where('role', '!=', 'client')->get();
            $hasNoDepartment = $user->departments->isEmpty();
        } else {
            $projectsCount = $user->projects()->count();
            $tasksCount = $user->assignedTasks()->where('status', '!=', 'done')->count();
            $pendingTasksCount = $user->assignedTasks()->where('status', 'todo')->count();
            $completedProjectsCount = $user->projects()->whereHas('status', function($q) {
                $q->where('name', 'Completed');
            })->count();
            
            $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
            $hasNoDepartment = empty($userDepartmentIds);
            
            $allProjects = \App\Models\Project::whereIn('department_id', $userDepartmentIds)
                ->with(['status', 'user', 'tasks', 'department'])
                ->latest()
                ->get();
            $totalBudget = $allProjects->sum('budget');
            $totalActualCost = $allProjects->sum(function($p) { return $p->tasks->sum('cost'); });
            $overloadedStaff = collect(); // Only relevant for admin
            
            // Non-admins only see their own energy
            $staffMembers = collect([$user]);
        }

        // Get pending approvals for current user (by email match)
        $pendingApprovals = \App\Models\TicketApproval::with(['ticket' => function($q) {
                $q->with(['requester']);
            }])
            ->where('approver_email', $user->email)
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('home', compact(
            'projectsCount', 
            'tasksCount', 
            'pendingTasksCount', 
            'completedProjectsCount',
            'allProjects',
            'overloadedStaff',
            'isAdmin',
            'totalBudget',
            'totalActualCost',
            'staffMembers',
            'hasNoDepartment',
            'pendingApprovals'
        ));
    }
}
