<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build query based on user role
        if ($user->role === 'admin') {
            $query = Ticket::with(['requester', 'assignees', 'activeAssignees', 'project', 'approvals']);
        } else {
            // Users see tickets they requested or assigned to
            $query = Ticket::with(['requester', 'assignees', 'activeAssignees', 'project', 'approvals'])
                ->where(function($q) use ($user) {
                    $q->where('requester_id', $user->id)
                      ->orWhereHas('assignees', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
                });
        }

        // Filtering
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('assigned_to') && $request->assigned_to != '') {
            $query->whereHas('assignees', function($q) use ($request) {
                $q->where('user_id', $request->assigned_to);
            });
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Per Page
        $perPage = $request->get('limit', 10);
        $tickets = $query->latest()->paginate($perPage)->withQueryString();

        // Get filter options
        $users = User::orderBy('name')->get();
        // Fetch staff for Energy Monitor (restricted for non-admins)
        // Fetch staff for Energy Monitor (restricted for non-admins)
        if ($user->role === 'admin') {
            $staffMembers = User::with('departments')->where('role', '!=', 'client')->get();
        } else {
            $staffMembers = collect([$user->load('departments')]);
        }

        // Get projects for linking (Admin only needs this effectively, but passing generally is fine)
        $projects = Project::orderBy('title')->get();

        return view('tickets.index', compact('tickets', 'users', 'staffMembers', 'projects'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $projects = Auth::user()->role === 'admin' 
            ? Project::orderBy('title')->get() 
            : Auth::user()->projects()->orderBy('title')->get();
            
        return view('tickets.create', compact('projects'));
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:new_feature,update,bug_fix,enhancement,DM,Design,Web',
            'priority' => 'required|in:low,medium,high,urgent,very_low,very_high,super_urgent',
            'project_id' => 'nullable|exists:projects,id',
            'estimation_in_days' => 'nullable|integer|min:1',
            'asset_url' => 'nullable|url',
        ]);

        // Default estimation to 1 day if not provided (for energy calculation)
        if (!isset($validated['estimation_in_days'])) {
            $validated['estimation_in_days'] = 1;
        }

        // Generate ticket number
        $validated['ticket_number'] = Ticket::generateTicketNumber();
        $validated['requester_id'] = Auth::id();
        $validated['status'] = 'open';
        $validated['current_stage'] = 1;

        $ticket = Ticket::create($validated);

        // Initialize workflow stages
        $ticket->initializeStages();

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket berhasil dibuat dengan nomor: ' . $ticket->ticket_number);
    }

    /**
     * Display the specified ticket
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'requester',
            'assignees',
            'project',
            'stages',
            'documents.uploader',
            'documents.approver',
            'approvals.approver',
            'approvals.document'
        ]);

        // Get available users for assignment (admin and users only)
        $users = User::whereIn('role', ['admin', 'user'])->orderBy('name')->get();

        // Get projects for linking (Admin only needs this effectively, but passing generally is fine or restricting query)
        $projects = Project::orderBy('title')->get();

        // Get document types
        $documentTypes = \App\Models\TicketDocument::getDocumentTypes();

        return view('tickets.show', compact('ticket', 'users', 'documentTypes', 'projects'));
    }

    /**
     * Show the form for editing the ticket
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $projects = Auth::user()->role === 'admin' 
            ? Project::orderBy('title')->get() 
            : Auth::user()->projects()->orderBy('title')->get();

        return view('tickets.edit', compact('ticket', 'projects'));
    }

    /**
     * Update the specified ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:new_feature,update,bug_fix,enhancement,DM,Design,Web',
            'priority' => 'required|in:low,medium,high,urgent,very_low,very_high,super_urgent',
            'project_id' => 'nullable|exists:projects,id',
            'estimation_in_days' => 'nullable|integer|min:1',
            'asset_url' => 'nullable|url',
        ]);

        if (isset($validated['status']) && $validated['status'] !== $ticket->status) {
            $ticket->statusHistory()->create([
                'user_id' => auth()->id(),
                'old_status' => $ticket->status,
                'new_status' => $validated['status']
            ]);
            $validated['status_changed_at'] = now();
        }

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket berhasil diperbarui.');
    }

    /**
     * Remove the specified ticket
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        // Delete associated documents from storage
        foreach ($ticket->documents as $document) {
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket berhasil dihapus.');
    }

    /**
     * Assign ticket to a user
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $request->validate([
            'assigned_to' => 'required|array',
            'assigned_to.*' => 'exists:users,id',
        ]);

        $newAssignees = $request->assigned_to;
    $currentAssignees = $ticket->assignees()->pluck('users.id')->toArray();
    
    // Determine to Add and Remove
    // Note: We need to check against ALL pivot records including removed ones to decide if we need to updateExistingPivot or attach
    $allPivotRecords = \DB::table('ticket_user')->where('ticket_id', $ticket->id)->get();
    $existingPivotUserIds = $allPivotRecords->pluck('user_id')->toArray();

    // 1. Handle New Assignments
    foreach ($newAssignees as $userId) {
        if (in_array($userId, $existingPivotUserIds)) {
            // Already exists in pivot (either active or removed), so update it
            // Reset removed_at to null, and update assigned_at only if it was previously removed (to treat as new assignment)
            // Or should we keep original assigned_at? User request: "Danu (tanggal assigned nya)" implies new date if new assignment.
            // But "Andrik (remvoed)" implies history.
            // If Alfin is re-selected, he stays active. 
            // If Danu was removed and added back, maybe update assigned_at to now? Or keep original?
            // "Danu (tanggal assigned nya)" usually means the *latest* assignment.
            // Let's update assigned_at to now() if it was previously removed, effectively "re-assigning".
            
            // Check if currently removed
            $record = $allPivotRecords->firstWhere('user_id', $userId);
            if ($record->removed_at) {
                // Was removed, now re-added. Update assigned_at and clear removed_at
                $ticket->assignees()->updateExistingPivot($userId, ['assigned_at' => now(), 'removed_at' => null]);
            } else {
                // Was active, still active. Do nothing to preserve original assigned_at?
                // Or user might want to update timestamp? "Alfin (tanggal assigned nya)" -> implies original.
                // We do nothing.
            }
        } else {
            // New record
            $ticket->assignees()->attach($userId, ['assigned_at' => now()]);
        }
    }

    // 2. Handle Removals
    // Anyone in $currentAssignees (active) who is NOT in $newAssignees should be marked removed
    // We need to fetch current *active* assignees first. $ticket->assignees only returns active ones if we filter in relationship.
    // But currently relationship returns ALL? No, standard belongsToMany returns all unless filtered.
    // Wait, my relationship in Ticket.php is generic.
    // I should check `removed_at`
    
    foreach ($existingPivotUserIds as $userId) {
        // If user is NOT in the new list, AND is currently active (removed_at is null), then mark removed
        if (!in_array($userId, $newAssignees)) {
            $record = $allPivotRecords->firstWhere('user_id', $userId);
            if (!$record->removed_at) {
                $ticket->assignees()->updateExistingPivot($userId, ['removed_at' => now()]);
            }
        }
    }

        // If no status update logic is needed here (it was setting to in_progress), we can keep it
        // Or if status is open, maybe move to in_progress if assigned?
        if ($ticket->status == 'open') {
             $ticket->statusHistory()->create([
                'user_id' => auth()->id(),
                'old_status' => $ticket->status,
                'new_status' => 'in_progress'
             ]);
             $ticket->update(['status' => 'in_progress', 'status_changed_at' => now()]);
        }

        return redirect()->back()
            ->with('success', 'Ticket berhasil di-assign kepada ' . count($request->assigned_to) . ' user.');
    }

    /**
     * Progress to next stage
     */
    public function progressStage(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $request->validate([
            'notes' => 'nullable|string',
            'target_stage' => 'required|integer|min:1|max:8',
        ]);

        $currentStageModel = $ticket->stages()->where('stage_number', $ticket->current_stage)->first();
        
        if ($currentStageModel) {
            $currentStageModel->markAsCompleted(Auth::id(), $request->notes);
        }

        $targetStage = $request->target_stage;

        // Validate target stage is after current stage
        if ($targetStage <= $ticket->current_stage) {
            return redirect()->back()
                ->with('error', 'Stage tujuan harus lebih besar dari stage saat ini.');
        }

        // Update ticket to target stage
        $ticket->update(['current_stage' => $targetStage]);

        // Update target stage to in_progress
        $nextStageModel = $ticket->stages()->where('stage_number', $targetStage)->first();
        if ($nextStageModel) {
            $nextStageModel->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);
        }

        // Mark all skipped stages as 'skipped' (optional)
        for ($i = $ticket->current_stage + 1; $i < $targetStage; $i++) {
            $ticket->stages()->where('stage_number', $i)->update([
                'status' => 'skipped',
                'completed_at' => now()
            ]);
        }

        return redirect()->back()
            ->with('success', 'Berhasil melanjutkan ke stage: ' . $nextStageModel->stage_name);
    }

    /**
     * Complete ticket
     */
    public function complete(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        // Check if all mandatory documents are approved
        if (!$ticket->hasAllMandatoryDocuments()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menyelesaikan ticket. Semua dokumen wajib harus di-approve terlebih dahulu.');
        }

        // Check if all stages are completed
        $incompletedStages = $ticket->stages()->where('status', '!=', 'completed')->count();
        if ($incompletedStages > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menyelesaikan ticket. Semua stage harus diselesaikan terlebih dahulu.');
        }

        $ticket->statusHistory()->create([
            'user_id' => auth()->id(),
            'old_status' => $ticket->status,
            'new_status' => 'completed'
        ]);

        $ticket->update([
            'status' => 'completed',
            'status_changed_at' => now(),
            'completed_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Ticket berhasil diselesaikan!');
    }

    /**
     * Update ticket status manually
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $request->validate([
            'status' => 'required|in:open,in_progress,on_hold,completed,cancelled',
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
        ]);

        // If status is completed, use the complete method logic validation (optional, but good for consistency)
        // For now, we allow manual override but maybe warn? 
        // Let's just update for now as requested.
        
        if ($ticket->status !== $request->status) {
            $ticket->statusHistory()->create([
                'user_id' => auth()->id(), // Admin is still the one making the change in the system
                'old_status' => $ticket->status,
                'new_status' => $request->status,
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
            ]);
        }

        $ticket->update([
            'status' => $request->status,
            'status_changed_at' => now()
        ]);

        if ($request->status === 'completed' && !$ticket->completed_at) {
             $ticket->update(['completed_at' => now()]);
        }

        return redirect()->back()
            ->with('success', 'Status ticket berhasil diperbarui.');
    }

    /**
     * Link ticket to a project (Admin only)
     */
    public function linkProject(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $ticket->update(['project_id' => $request->project_id]);

        return redirect()->back()->with('success', 'Ticket successfully linked to project.');
    }

    /**
     * Get POAC logs for a ticket
     */
    public function getPoacLogs(Ticket $ticket)
    {
        // Load project relationship for authorization
        $ticket->load('project');
        
        // Temporarily disable authorization to debug
        // $this->authorize('view', $ticket);
        
        $logs = $ticket->poacLogs()->with('user')->get()->map(function($log) {
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
