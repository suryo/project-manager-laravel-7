<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
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
        return true; // All authenticated users can view tickets list
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function view(User $user, Ticket $ticket)
    {
        // Admin can view all tickets (case-insensitive)
        if (strtolower($user->role) === 'admin') {
            return true;
        }

        // Requester can view their own tickets
        if ($user->id === $ticket->requester_id) {
            return true;
        }

        // Assigned user can view assigned tickets (check pivot)
        if ($ticket->assignees->contains($user->id)) {
            return true;
        }

        // SPV of the project's department can view
        if ($ticket->project_id && $ticket->project) {
            $isSpv = $user->departments()
                ->where('departments.id', $ticket->project->department_id)
                ->where('department_members.role', 'SPV')
                ->exists();
            if ($isSpv) return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create tickets
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Admin can update all tickets
        if ($user->role === 'admin') {
            return true;
        }

        // Assigned user can update
        if ($ticket->assignees->contains($user->id)) {
            return true;
        }

        // Requester can update only if ticket is still open
        if ($user->id === $ticket->requester_id && $ticket->status === 'open') {
            return true;
        }

        // SPV of the project's department can update
        if ($ticket->project_id && $ticket->project) {
            $isSpv = $user->departments()
                ->where('departments.id', $ticket->project->department_id)
                ->where('department_members.role', 'SPV')
                ->exists();
            if ($isSpv) return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Only admin can delete tickets
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can approve documents.
     */
    public function approveDocuments(User $user, Ticket $ticket): bool
    {
        // Admin can approve
        if ($user->role === 'admin') {
            return true;
        }

        // Assigned user can approve
        if ($ticket->assignees->contains($user->id)) {
            return true;
        }

        return false;
    }
}
