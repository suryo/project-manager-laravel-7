<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Load project and assignees relationships if not loaded
        $task->loadMissing('project', 'assignees');
        
        // Safety check if project is missing
        if (!$task->project) {
            return false;
        }

        // Project owner can view
        if ($user->id == $task->project->user_id) {
            return true;
        }
        
        // Assignee can view - using 'id' explicitly for robustness
        if ($task->assignees->contains('id', $user->id)) {
            return true;
        }
        
        // Department members can view tasks from projects in their department
        if ($task->project->department_id) {
            $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
            if (in_array((int)$task->project->department_id, array_map('intval', $userDepartmentIds))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Load project and assignees relationships if not loaded
        $task->loadMissing('project', 'assignees');

        // Safety check if project is missing
        if (!$task->project) {
            return false;
        }
        
        // Project owner can update
        if ($user->id == $task->project->user_id) {
            return true;
        }
        
        // Assignee can update
        if ($task->assignees->contains('id', $user->id)) {
            return true;
        }
        
        // Department members can update tasks from projects in their department
        if ($task->project->department_id) {
            $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
            if (in_array((int)$task->project->department_id, array_map('intval', $userDepartmentIds))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Only creator can delete
        return $user->id == $task->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        //
    }
}
