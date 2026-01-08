<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketDocument;
use App\Models\TicketDocumentForm;
use App\Services\PdfTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketDocumentController extends Controller
{
    /**
     * Upload a document
     */
    public function upload(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $request->validate([
            'document_type' => 'required|in:request_form,user_requirements,functional_spec,project_plan,user_manual,bast,user_story,requirement_signoff,change_request,uat_report,installation_report',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ]);

        // Check if document type already exists (replace or reject)
        $existingDoc = $ticket->documents()
            ->where('document_type', $request->document_type)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingDoc) {
            return redirect()->back()
                ->with('error', 'Dokumen dengan tipe ini sudah ada. Hapus dokumen lama terlebih dahulu jika ingin mengganti.');
        }

        // Store file
        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('ticket_documents/' . $ticket->id, $fileName, 'public');

        // Create document record
        $document = $ticket->documents()->create([
            'document_type' => $request->document_type,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->back()
            ->with('success', 'Dokumen berhasil diupload dan menunggu approval.');
    }

    /**
     * Download a document
     */
    public function download(TicketDocument $document)
    {
        $this->authorize('view', $document->ticket);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Approve a document
     */
    public function approve(Request $request, TicketDocument $document)
    {
        // Only admin and assigned user can approve
        if (Auth::user()->role !== 'admin' && Auth::id() !== $document->ticket->assigned_to) {
            abort(403, 'Anda tidak memiliki wewenang untuk meng-approve dokumen ini.');
        }

        $document->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        // Create approval record
        $document->ticket->approvals()->create([
            'document_id' => $document->id,
            'approver_id' => Auth::id(),
            'approval_type' => 'document',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Dokumen berhasil di-approve.');
    }

    /**
     * Reject a document
     */
    public function reject(Request $request, TicketDocument $document)
    {
        // Only admin and assigned user can reject
        if (Auth::user()->role !== 'admin' && Auth::id() !== $document->ticket->assigned_to) {
            abort(403, 'Anda tidak memiliki wewenang untuk me-reject dokumen ini.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Create approval record
        $document->ticket->approvals()->create([
            'document_id' => $document->id,
            'approver_id' => Auth::id(),
            'approval_type' => 'document',
            'status' => 'rejected',
            'comment' => $request->rejection_reason,
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Dokumen di-reject. Alasan: ' . $request->rejection_reason);
    }

    /**
     * Delete a document (only before approval)
     */
    public function destroy(TicketDocument $document)
    {
        $this->authorize('view', $document->ticket);

        // Only uploader or admin can delete, and only if pending
        if ($document->status !== 'pending' && Auth::user()->role !== 'admin') {
            return redirect()->back()
                ->with('error', 'Dokumen yang sudah di-approve/reject tidak dapat dihapus.');
        }

        if ($document->uploaded_by !== Auth::id() && Auth::user()->role !== 'admin') {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus dokumen yang diupload oleh orang lain.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Show form for inline document creation
     */
    public function createForm(Ticket $ticket, $documentType)
    {
        $this->authorize('view', $ticket);
        
        $metadata = TicketDocument::getDocumentTypesWithMeta();
        $allTypes = array_merge(
            $metadata['mandatory'] ?? [],
            $metadata['supporting'] ?? []
        );
        
        if (!isset($allTypes[$documentType])) {
            abort(404, 'Document type not found');
        }
        
        $config = $allTypes[$documentType];
        
        if (!in_array('form', $config['input_methods'] ?? [])) {
            return redirect()->back()
                ->with('error', 'Document type ini tidak support form input.');
        }
        
        return view('tickets.documents.form', compact('ticket', 'documentType', 'config'));
    }

    /**
     * Store form data as document
     */
    public function storeForm(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        
        $documentType = $request->input('document_type');
        $metadata = TicketDocument::getDocumentTypesWithMeta();
        $allTypes = array_merge($metadata['mandatory'] ?? [], $metadata['supporting'] ?? []);
        $config = $allTypes[$documentType] ?? null;
        
        if (!$config || !in_array('form', $config['input_methods'] ?? [])) {
            return redirect()->back()
                ->with('error', 'Invalid document type for form input.');
        }
        
        // Build validation rules dynamically
        $rules = ['document_type' => 'required'];
        foreach ($config['form_fields'] ?? [] as $field => $fieldConfig) {
            if ($fieldConfig['required']) {
                $rules[$field] = 'required';
            }
        }
        
        $validated = $request->validate($rules);
        
        // Generate PDF from form data
        $pdfService = new PdfTemplateService();
        $formData = $request->except(['_token', 'document_type']);
        
        try {
            $pdfResult = $pdfService->generateFromFormData($ticket, $documentType, $formData);
            
            // Create document record
            $document = $ticket->documents()->create([
                'document_type' => $documentType,
                'input_method' => 'form',
                'file_path' => $pdfResult['path'],
                'file_name' => $pdfResult['filename'],
                'file_size' => filesize(storage_path('app/public/' . $pdfResult['path'])),
                'mime_type' => 'application/pdf',
                'uploaded_by' => Auth::id(),
                'status' => 'pending',
            ]);
            
            // Store form data
            $document->formData()->create([
                'form_data' => $formData,
            ]);
            
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Dokumen berhasil dibuat dari form dan menunggu approval.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate PDF: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Upload multiple files (for functional_spec)
     */
    public function uploadMultiple(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        
        $request->validate([
            'document_type' => 'required|in:functional_spec',
            'files' => 'required|array|max:10',
            'files.*' => 'file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240',
        ]);
        
        $documentType = $request->document_type;
        $uploadedCount = 0;
        $parentDocument = null;
        
        foreach ($request->file('files') as $index => $file) {
            $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('ticket_documents/' . $ticket->id, $fileName, 'public');
            
            $document = $ticket->documents()->create([
                'document_type' => $documentType,
                'input_method' => 'upload',
                'allow_multiple' => true,
                'parent_id' => $parentDocument ? $parentDocument->id : null,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
                'status' => 'pending',
            ]);
            
            // First file becomes parent
            if ($index === 0) {
                $parentDocument = $document;
            }
            
            $uploadedCount++;
        }
        
        return redirect()->back()
            ->with('success', "{$uploadedCount} file berhasil diupload dan menunggu approval.");
    }

    /**
     * Download PDF template
     */
    public function downloadTemplate($documentType)
    {
        $metadata = TicketDocument::getDocumentTypesWithMeta();
        $allTypes = array_merge($metadata['mandatory'] ?? [], $metadata['supporting'] ?? []);
        $config = $allTypes[$documentType] ?? null;
        
        if (!$config || !($config['has_template'] ?? false)) {
            return redirect()->back()
                ->with('error', 'Template tidak tersedia untuk document type ini.');
        }
        
        try {
            $pdfService = new PdfTemplateService();
            $pdf = $pdfService->generateTemplate($documentType);
            
            $filename = $documentType . '_template.pdf';
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate template: ' . $e->getMessage());
        }
    }

    /**
     * Auto-generate Project Plan from tasks
     */
    public function generateProjectPlan(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        
        // Check if ticket has project
        if (!$ticket->project) {
            return redirect()->back()
                ->with('error', 'Ticket tidak terhubung dengan project.');
        }
        
        try {
            $pdfService = new PdfTemplateService();
            $pdf = $pdfService->generateProjectPlanTemplate($ticket);
            
            // Save PDF
            $filename = 'project_plan_' . $ticket->ticket_number . '_' . time() . '.pdf';
            $path = 'ticket_documents/' . $ticket->id . '/' . $filename;
            $pdfService->savePdf($pdf, $path);
            
            // Create document record
            $document = $ticket->documents()->create([
                'document_type' => 'project_plan',
                'file_name' => $filename,
                'file_path' => $path,
                'file_size' => filesize(storage_path('app/public/' . $path)),
                'mime_type' => 'application/pdf',
                'uploaded_by' => auth()->id(),
                'status' => 'approved', // Auto-approved since it's auto-generated
                'input_method' => 'auto',
            ]);
            
            return redirect()->back()
                ->with('success', 'Project Plan berhasil di-generate dari ' . $ticket->project->tasks->count() . ' tasks.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate Project Plan: ' . $e->getMessage());
        }
    }
}
