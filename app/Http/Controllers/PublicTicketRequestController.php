<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketDocument;
use App\Models\User;
use App\Services\PdfTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicTicketRequestController extends Controller
{
    /**
     * Show public ticket request form
     */
    /**
     * Show public ticket request form
     */
    public function showForm()
    {
        // Fetch projects for dropdown
        $projects = \App\Models\Project::where('project_status_id', '!=', 3) // Assuming 3 is 'Completed' or similar, adjust if needed or just get all
            ->orderBy('title')
            ->get(['id', 'title']);
            
        return view('public.ticket-request.form', compact('projects'));
    }

    /**
     * Submit public ticket request
     */
    public function submitRequest(Request $request)
    {
        \Log::info('Public request submission started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            $validated = $request->validate([
                // Guest Information
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_department' => 'required|string|max:255',
                'guest_phone' => 'nullable|string|max:20',
                
                // Ticket Details
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:DM,Design,Web',
                'priority' => 'nullable|string', // Hidden from user, defaults to medium
                'asset_url' => 'nullable|url',
                
                // Request Form Data
                
                // Project Selection Logic
                'project_id' => 'required|string',
                'project_name' => 'required_if:project_id,other|nullable|string|max:255',
                
                // Merged Reason & Impact - Removed
                // 'request_description' => 'required_if:form_method,inline|nullable|string', 
                
                // Approvers (Required, min 1)
                'approvers' => 'required|array|min:1',
                'approvers.*.name' => 'required|string|max:255',
                
                'target_deadline' => 'required|date',
                
                // Functional Spec Files
                'functional_spec_files.*' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240',
            ]);

            \Log::info('Validation passed');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        DB::beginTransaction();
        
        try {
            \Log::info('Creating guest user');
            
            // Get or create Guest user
            $guestUser = User::firstOrCreate(
                ['email' => 'guest@system.local'],
                [
                    'name' => 'Guest User',
                    'password' => bcrypt(Str::random(32)),
                    'role' => 'client'
                ]
            );

            \Log::info('Guest user ready', ['id' => $guestUser->id]);

            // Generate tracking token
            $trackingToken = $this->generateTrackingToken();
            \Log::info('Token generated', ['token' => $trackingToken]);

            // Create ticket
            $ticket = Ticket::create([
                'ticket_number' => Ticket::generateTicketNumber(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'priority' => 'medium', // Default for public requests
                'estimation_in_days' => null, // Admin only
                'asset_url' => $validated['asset_url'] ?? null,
                'status' => 'open',
                'current_stage' => 1,
                'project_id' => ($validated['project_id'] !== 'other') ? $validated['project_id'] : null,
                'requester_id' => $guestUser->id,
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_department' => $validated['guest_department'],
                'guest_phone' => $validated['guest_phone'] ?? null,
                'tracking_token' => $trackingToken,
                'is_public_request' => true,
            ]);

            \Log::info('Ticket created', ['ticket_id' => $ticket->id, 'number' => $ticket->ticket_number]);

            // Handle Approvers
            if (isset($validated['approvers']) && is_array($validated['approvers'])) {
                foreach ($validated['approvers'] as $approver) {
                    if (!empty($approver['name'])) {
                        \App\Models\TicketApproval::create([
                            'ticket_id' => $ticket->id,
                            'approver_name' => $approver['name'],
                            'approval_token' => Str::orderedUuid(), // Using ordered UUID for simpler indexing
                            'status' => 'pending',
                            'approval_type' => 'final', // Or 'initial' based on logic
                        ]);
                    }
                }
            }

            // Initialize stages
            $ticket->initializeStages();
            // ... (Rest of existing logic handleStages etc)


            // Handle Functional Spec Files
            if ($request->hasFile('functional_spec_files')) {
                $this->handleFunctionalSpecFiles($ticket, $request->file('functional_spec_files'));
                \Log::info('Functional spec files handled');
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            // TODO: Send email notifications
            
            return redirect()->route('public.ticket-request.success', ['token' => $trackingToken])
                ->with('success', 'Request submitted successfully! Check your email for tracking information.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Public request submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit request: ' . $e->getMessage());
        }
    }

    /**
     * Handle Request Form submission
     */
    /**
     * Handle Request Form submission
     */
    protected function handleRequestForm($ticket, $validated, $request)
    {
        if ($validated['form_method'] === 'upload') {
            // Upload file
            $file = $request->file('request_form_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('ticket_documents/' . $ticket->id, $fileName, 'public');

            $ticket->documents()->create([
                'document_type' => 'request_form',
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $ticket->requester_id,
                'status' => 'pending',
                'input_method' => 'upload',
            ]);
        } else {
            // Generate PDF from inline form
            
            // Determine project name
            $projectName = $validated['project_id'];
            if ($projectName === 'other') {
                $projectName = $validated['project_name'];
            } else {
                // If it's an ID, fetch the project title (optional but good for PDF)
                $project = \App\Models\Project::find($projectName);
                if ($project) {
                    $projectName = $project->title;
                }
            }

            $formData = [
                'project_name' => $projectName,
                'request_reason' => '-', // Field removed as per request
                // 'business_impact' => '', // Merged into request_reason
                // 'stakeholders' => '', // Removed
                'target_deadline' => $validated['target_deadline'],
                'requester_name' => $validated['guest_name'],
                'requester_department' => $validated['guest_department'],
                'requester_email' => $validated['guest_email'],
            ];

            $pdfService = new PdfTemplateService();
            $pdf = $pdfService->generateRequestFormTemplate($ticket, $formData);
            
            $filename = 'request_form_' . $ticket->ticket_number . '_' . time() . '.pdf';
            $path = 'ticket_documents/' . $ticket->id . '/' . $filename;
            $pdfService->savePdf($pdf, $path);

            $ticket->documents()->create([
                'document_type' => 'request_form',
                'file_name' => $filename,
                'file_path' => $path,
                'file_size' => filesize(storage_path('app/public/' . $path)),
                'mime_type' => 'application/pdf',
                'uploaded_by' => $ticket->requester_id,
                'status' => 'pending',
                'input_method' => 'form',
            ]);
        }
    }

    /**
     * Handle User Requirements submission
     */
    protected function handleUserRequirements($ticket, $validated, $request)
    {
        if ($validated['requirements_method'] === 'upload') {
            // Upload file
            $file = $request->file('requirements_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('ticket_documents/' . $ticket->id, $fileName, 'public');

            $ticket->documents()->create([
                'document_type' => 'user_requirements',
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $ticket->requester_id,
                'status' => 'pending',
                'input_method' => 'upload',
            ]);
        } else {
            // Generate PDF from inline form
            $formData = [
                'functional_requirements' => $validated['functional_requirements'] ?? 'Not specified',
                'non_functional_requirements' => $validated['non_functional_requirements'] ?? 'Not specified',
                'user_stories' => $validated['user_stories'] ?? 'Not specified',
                'acceptance_criteria' => $validated['acceptance_criteria'] ?? 'Not specified',
            ];

            $pdfService = new PdfTemplateService();
            $pdf = $pdfService->generateUserRequirementsTemplate($ticket, $formData);
            
            $filename = 'user_requirements_' . $ticket->ticket_number . '_' . time() . '.pdf';
            $path = 'ticket_documents/' . $ticket->id . '/' . $filename;
            $pdfService->savePdf($pdf, $path);

            $ticket->documents()->create([
                'document_type' => 'user_requirements',
                'file_name' => $filename,
                'file_path' => $path,
                'file_size' => filesize(storage_path('app/public/' . $path)),
                'mime_type' => 'application/pdf',
                'uploaded_by' => $ticket->requester_id,
                'status' => 'pending',
                'input_method' => 'form',
            ]);
        }
    }

    /**
     * Handle Functional Spec Files upload
     */
    protected function handleFunctionalSpecFiles($ticket, $files)
    {
        $parentDocument = null;
        
        foreach ($files as $index => $file) {
            $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('ticket_documents/' . $ticket->id, $fileName, 'public');
            
            $document = $ticket->documents()->create([
                'document_type' => 'functional_spec',
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $ticket->requester_id,
                'status' => 'pending',
                'input_method' => 'upload',
                'parent_id' => $parentDocument ? $parentDocument->id : null,
            ]);

            if ($index === 0) {
                $parentDocument = $document;
            }
        }
    }

    /**
     * Show success page
     */
    public function showSuccess($token)
    {
        $ticket = Ticket::where('tracking_token', $token)
            ->with(['documents', 'approvals'])
            ->firstOrFail();

        return view('public.ticket-request.success', compact('ticket'));
    }

    /**
     * View submitted request details (Similar to success but without success message)
     */
    public function viewRequest($token)
    {
        $ticket = Ticket::where('tracking_token', $token)
            ->with(['documents', 'approvals'])
            ->firstOrFail();

        return view('public.ticket-request.view', compact('ticket'));
    }

    /**
     * Check request status
     */
    public function checkStatus($token)
    {
        $ticket = Ticket::where('tracking_token', $token)
            ->with(['documents', 'stages', 'approvals', 'statusHistory.user', 'assignees'])
            ->firstOrFail();
        
        return view('public.ticket-request.status', compact('ticket'));
    }

    /**
     * Show approval page
     */
    public function showApprovalPage($token)
    {
        $approval = \App\Models\TicketApproval::where('approval_token', $token)
            ->with(['ticket.documents', 'ticket.project'])
            ->firstOrFail();

        if ($approval->status !== 'pending') {
            return view('public.ticket-request.approval_done', compact('approval'));
        }

        return view('public.ticket-request.approval', compact('approval'));
    }

    /**
     * Submit approval
     */
    public function submitApproval(Request $request, $token)
    {
        $approval = \App\Models\TicketApproval::where('approval_token', $token)->firstOrFail();

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $action = $request->input('action');
        $status = ($action === 'reject') ? 'rejected' : 'approved';
        $message = ($action === 'reject') 
            ? 'You have rejected this ticket.' 
            : 'Thank you! You have successfully approved this ticket.';

        $approval->update([
            'status' => $status,
            'approved_at' => now(),
            'ip_address' => $request->ip(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->route('public.approval.show', ['token' => $token])
            ->with('success', $message);
    }

    /**
     * Update ticket status from public page
     */
    public function updateStatus(Request $request, $token)
    {
        $ticket = Ticket::where('tracking_token', $token)->firstOrFail();
        
        $request->validate([
            'status' => 'required|in:open,in_progress,cancelled,completed',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
        ]);

        $newStatus = $request->status;
        $oldStatus = $ticket->status;

        if ($newStatus !== $oldStatus) {
            $ticket->update(['status' => $newStatus]);

            // Record history
            \App\Models\TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $request->guest_name, // Store name in changed_by or use guest columns
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'user_id' => null // Null for guest changes
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    protected function generateTrackingToken()
    {
        do {
            $token = 'PUBR-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (Ticket::where('tracking_token', $token)->exists());

        return $token;
    }
}
