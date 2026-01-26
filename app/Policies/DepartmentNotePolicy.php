<?php

namespace App\Policies;

use App\Models\DepartmentNote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if admin has full access.
     */
    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view any notes.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the note.
     */
    public function view(User $user, DepartmentNote $note)
    {
        // User must be in the same department
        return $user->departments->contains($note->department_id);
    }

    /**
     * Determine whether the user can create notes.
     */
    public function create(User $user)
    {
        return true; // All authenticated users can create notes in their departments
    }

    /**
     * Determine whether the user can update the note.
     */
    public function update(User $user, DepartmentNote $note)
    {
        // User must be the creator or in the same department
        return $user->id === $note->user_id || $user->departments->contains($note->department_id);
    }

    /**
     * Determine whether the user can delete the note.
     */
    public function delete(User $user, DepartmentNote $note)
    {
        // Only the creator can delete their own note
        return $user->id === $note->user_id;
    }
}
