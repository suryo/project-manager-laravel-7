<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfTemplateService
{
    /**
     * Generate Request Form Template
     */
    public function generateRequestFormTemplate($ticket = null, $formData = null)
    {
        $data = [
            'ticket' => $ticket,
            'formData' => $formData,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.request_form', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    /**
     * Generate User Requirements Template
     */
    public function generateUserRequirementsTemplate($ticket = null, $formData = null)
    {
        $data = [
            'ticket' => $ticket,
            'formData' => $formData,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.user_requirements', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    /**
     * Generate Functional Spec Template
     */
    public function generateFunctionalSpecTemplate($ticket = null)
    {
        $data = [
            'ticket' => $ticket,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.functional_spec', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    /**
     * Generate User Manual Template
     */
    public function generateUserManualTemplate($ticket = null)
    {
        $data = [
            'ticket' => $ticket,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.user_manual', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    /**
     * Generate BAST Template
     */
    public function generateBASTTemplate($ticket = null)
    {
        $data = [
            'ticket' => $ticket,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.bast', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    /**
     * Generate Project Plan Template
     */
    public function generateProjectPlanTemplate($ticket = null)
    {
        $tasks = collect();
        
        if ($ticket && $ticket->project) {
            // Get all tasks from the ticket's project
            $tasks = $ticket->project->tasks()
                ->with(['assignees'])
                ->orderBy('start_date')
                ->get();
        }
        
        $data = [
            'ticket' => $ticket,
            'tasks' => $tasks,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.project_plan', $data);
        $pdf->setPaper('a4', 'landscape'); // Landscape untuk table yang lebar
        
        return $pdf;
    }

    /**
     * Generate template based on document type
     */
    public function generateTemplate($documentType, $ticket = null, $formData = null)
    {
        switch ($documentType) {
            case 'request_form':
                return $this->generateRequestFormTemplate($ticket, $formData);
            
            case 'user_requirements':
                return $this->generateUserRequirementsTemplate($ticket, $formData);
            
            case 'functional_spec':
                return $this->generateFunctionalSpecTemplate($ticket);
            
            case 'project_plan':
                return $this->generateProjectPlanTemplate($ticket);
            
            case 'user_manual':
                return $this->generateUserManualTemplate($ticket);
            
            case 'bast':
                return $this->generateBASTTemplate($ticket);
            
            default:
                throw new \Exception("Template not available for document type: {$documentType}");
        }
    }

    /**
     * Save PDF to storage
     */
    public function savePdf($pdf, $path)
    {
        $directory = dirname(storage_path('app/public/' . $path));
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->save(storage_path('app/public/' . $path));
        
        return $path;
    }

    /**
     * Generate and save form data as PDF
     */
    public function generateFromFormData($ticket, $documentType, $formData)
    {
        $pdf = $this->generateTemplate($documentType, $ticket, $formData);
        $filename = $documentType . '_' . $ticket->ticket_number . '_' . time() . '.pdf';
        $path = 'ticket_documents/' . $ticket->id . '/' . $filename;
        
        $this->savePdf($pdf, $path);
        
        return [
            'path' => $path,
            'filename' => $filename,
        ];
    }
}
