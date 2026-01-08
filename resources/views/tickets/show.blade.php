@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-ticket-perforated"></i> {{ $ticket->ticket_number }}
                    </h2>
                    <p class="text-muted mb-0">{{ $ticket->title }}</p>
                </div>
                <div>
                    @can('update', $ticket)
                    <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-action me-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('tickets.index') }}" class="btn btn-action">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
@push('styles')
<style>
    .detail-card-grid .card-item {
        background: #fff;
        border-left: 4px solid #435ebe;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        height: 100%;
        transition: transform 0.2s ease;
    }
    .detail-card-grid .card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .detail-card-grid .label {
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #8898aa;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .detail-card-grid .value {
        font-size: 1rem;
        font-weight: 600;
        color: #32325d;
        line-height: 1.4;
    }
    .detail-card-grid .card-item.full-width {
        border-left-color: #2dce89; /* Different color for assignees or keep blue */
        border-left-color: #435ebe;
    }
</style>
<style>
    /* Sidebar Card Gradient */
    .card-purple-gradient {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 24px;
        background: #fff;
    }
    .card-purple-gradient .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-bottom: none;
        padding: 18px 20px;
    }
    .card-purple-gradient .card-header h6 {
        color: white;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 0;
        font-size: 1rem;
    }
    .card-purple-gradient .card-body {
        padding: 20px;
    }
    
    /* Document Summary Progress */
    .doc-progress-label {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        font-size: 0.85rem;
        color: #344767;
        margin-bottom: 6px;
    }
    .doc-progress-bar {
        height: 6px;
        border-radius: 10px;
        background-color: #e9ecef;
        overflow: hidden;
    }
    .doc-progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    
    /* Quick Action Alert */
    .quick-action-alert {
        background-color: #fff8e1; /* Light yellow */
        border-left: 4px solid #ffc107;
        border-radius: 6px;
        padding: 12px 15px;
        color: #856404;
        font-size: 0.9rem;
    }
</style>
@endpush

            <!-- Ticket Info Grid -->
            <div class="mb-4">
                <h5 class="mb-4 text-dark fw-bold"><i class="bi bi-info-circle"></i> Request Details</h5>
                
                <div class="row g-3 detail-card-grid">
                    <!-- Ticket Number -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Ticket Number</div>
                            <div class="value">{{ $ticket->ticket_number }}</div>
                        </div>
                    </div>
                    
                    <!-- Request Title -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Request Title</div>
                            <div class="value">{{ $ticket->title }}</div>
                        </div>
                    </div>

                    <!-- Requester -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Requester</div>
                            <div class="value d-flex align-items-center">
                                <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ substr($ticket->requester->name, 0, 1) }}
                                </div>
                                {{ $ticket->requester->name }}
                            </div>
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Department</div>
                            <div class="value">
                                {{ $ticket->guest_department ?? 'General' }}
                            </div>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Priority</div>
                            <div class="value">
                                @php
                                    $priorityColors = ['low' => 'success', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
                                    $pColor = $priorityColors[$ticket->priority] ?? 'secondary';
                                @endphp
                                <span class="text-{{ $pColor }}">{{ ucfirst($ticket->priority) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Submitted</div>
                            <div class="value">{{ $ticket->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Status</div>
                            <div class="value d-flex align-items-center justify-content-between">
                                @php
                                    $statusColors = ['open' => 'primary', 'in_progress' => 'info', 'on_hold' => 'warning', 'completed' => 'success', 'cancelled' => 'dark'];
                                    $sColor = $statusColors[$ticket->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $sColor }} text-white">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                
                                @can('update', $ticket)
                                    <button type="button" class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="modal" data-bs-target="#updateStatusModal" title="Edit Status">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                @endcan
                            </div>
                            @if($ticket->status_changed_at)
                                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                    Updated: {{ $ticket->status_changed_at->format('M d, H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>

                     <!-- Type -->
                     <div class="col-md-6">
                        <div class="card-item">
                            <div class="label">Type</div>
                            <div class="value">
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $ticket->type)) }}</span>
                            </div>
                        </div>
                    </div>


                    <!-- Assigned To (Full Width) -->
                    <div class="col-12">
                        <div class="card-item full-width">
                             <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="label mb-0">Assigned To</div>
                                @can('update', $ticket)
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#assignModal" title="Manage Assignees">
                                    <small class="fw-bold"><i class="bi bi-pencil-square"></i> Manage</small>
                                </button>
                                @endcan
                            </div>
                            
                             @if($ticket->assignees->count() > 0)
                                <div class="row g-2">
                                    {{-- Active Assignees --}}
                                    @foreach($ticket->assignees->where('pivot.removed_at', null)->sortByDesc('pivot.assigned_at') as $assignee)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-2 rounded bg-light">
                                                <div class="avatar-initial rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; font-size: 13px;">
                                                    {{ substr($assignee->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">{{ $assignee->name }}</span>
                                                    @if($assignee->pivot->assigned_at)
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            {{ \Carbon\Carbon::parse($assignee->pivot->assigned_at)->format('M d, H:i') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Removed Assignees (History) --}}
                                @php
                                    $removedAssignees = $ticket->assignees->whereNotNull('pivot.removed_at')->sortByDesc('pivot.removed_at');
                                @endphp
                                
                                @if($removedAssignees->count() > 0)
                                    <div class="mt-3 pt-2 border-top">
                                        <small class="text-muted fw-bold d-block mb-2" style="font-size: 0.7rem; text-transform: uppercase;">History</small>
                                        <div class="row g-2">
                                            @foreach($removedAssignees as $assignee)
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-1 opacity-75">
                                                        <div class="avatar-initial rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 28px; height: 28px; font-size: 11px;">
                                                            {{ substr($assignee->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <span class="text-decoration-line-through text-muted d-block" style="line-height: 1.2; font-size: 0.85rem;">
                                                                {{ $assignee->name }}
                                                            </span>
                                                            <small class="text-danger" style="font-size: 0.7rem;">
                                                                Removed: {{ \Carbon\Carbon::parse($assignee->pivot->removed_at)->format('M d, H:i') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-muted small fst-italic py-2">No active assignees.</div>
                                @can('update', $ticket)
                                <button type="button" class="btn btn-sm btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#assignModal">
                                    Assign Ticket
                                </button>
                                @endcan
                            @endif
                        </div>
                    </div>

                    @if($ticket->project)
                    <div class="col-12">
                         <div class="card-item">
                            <div class="label">Related Project</div>
                            <div class="value">
                                <a href="{{ route('projects.show', $ticket->project) }}" class="text-decoration-none">
                                    <i class="bi bi-folder-fill me-1 text-warning"></i> {{ $ticket->project->title }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Description -->
                    @if($ticket->description)
                    <div class="col-12">
                        <div class="card-item">
                            <div class="label">Description</div>
                            <div class="value text-break">
                                {!! $ticket->description !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stage Tracker -->
            <div class="card card-ticket mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Workflow Progress</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach($ticket->stages->whereIn('status', ['completed', 'in_progress', 'skipped']) as $stage)
                        <div class="col-md-2 text-center mb-3">
                            <div class="stage-item {{ $stage->status }}">
                                @if($stage->status == 'completed')
                                <div class="stage-circle bg-success text-white">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                @elseif($stage->status == 'in_progress')
                                <div class="stage-circle bg-primary text-white">
                                    {{ $stage->stage_number }}
                                </div>
                                @elseif($stage->status == 'skipped')
                                <div class="stage-circle bg-warning text-white">
                                    <i class="bi bi-skip-forward"></i>
                                </div>
                                @else
                                <div class="stage-circle bg-secondary text-white">
                                    {{ $stage->stage_number }}
                                </div>
                                @endif
                                <small class="d-block mt-2 fw-bold">{{ $stage->stage_name }}</small>
                                <small class="text-muted d-block">{{ ucfirst($stage->status) }}</small>
                                @if($stage->completed_at)
                                <small class="text-muted d-block">{{ $stage->completed_at->format('M d') }}</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @can('update', $ticket)
                    @if($ticket->status != 'completed' && $ticket->current_stage < 6)
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary border border-2 border-dark fw-bold" data-bs-toggle="modal" data-bs-target="#stageModal" style="box-shadow: 2px 2px 0 #000;">
                            <i class="bi bi-arrow-right-circle"></i> Progress to Next Stage
                        </button>
                    </div>
                    @endif
                    @endcan
                </div>
            </div>

            <!-- Document Management -->
            <div class="card card-ticket mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Documents</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#mandatory-docs">
                                Mandatory ({{ $ticket->documents()->mandatory()->count() }}/6)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" data-bs-toggle="tab" href="#supporting-docs">
                                Supporting ({{ $ticket->documents()->supporting()->count() }}/5)
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4">
                        <!-- Mandatory Documents -->
                        <div class="tab-pane fade show active" id="mandatory-docs">
                            @foreach($documentTypes['mandatory'] as $key => $name)
                            @php
                                // Get all documents for this type (parent + children)
                                $parentDoc = $ticket->documents->where('document_type', $key)->whereNull('parent_id')->first();
                                $allDocs = $parentDoc ? collect([$parentDoc])->merge($parentDoc->children) : collect();
                            @endphp
                            <div class="doc-item p-3 mb-3">
                                <h6 class="fw-bold mb-3">{{ $name }}</h6>
                                
                                @if($allDocs->count() > 0)
                                    @foreach($allDocs as $doc)
                                    <div class="d-flex justify-content-between align-items-start mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($doc->status) }}
                                                </span>
                                                <small class="text-muted">{{ $doc->file_name }} ({{ $doc->getFileSizeFormatted() }})</small>
                                                @if($allDocs->count() > 1)
                                                    <span class="badge bg-secondary">{{ $loop->iteration }}/{{ $allDocs->count() }}</span>
                                                @endif
                                            </div>
                                            @if($loop->first)
                                                <small class="text-muted d-block mt-1">
                                                    Uploaded by {{ $doc->uploader->name }} on {{ $doc->created_at->format('M d, Y') }}
                                                </small>
                                            @endif
                                            @if($doc->rejection_reason)
                                            <div class="alert alert-danger mt-2 mb-0 py-2">
                                                <small><strong>Rejected:</strong> {{ $doc->rejection_reason }}</small>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0">
                                            <a href="{{ route('tickets.documents.download', $doc) }}" class="btn btn-sm btn-info" title="Download">
                                                <i class="bi bi-download"></i> <span class="d-none d-md-inline">Download</span>
                                            </a>
                                            @if($doc->status == 'pending')
                                                @can('update', $ticket)
                                                <form action="{{ route('tickets.documents.approve', $doc) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                        <i class="bi bi-check-circle"></i> <span class="d-none d-md-inline">Approve</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $doc->id }})" title="Reject">
                                                    <i class="bi bi-x-circle"></i> <span class="d-none d-md-inline">Reject</span>
                                                </button>
                                                @endcan
                                            @endif
                                            @if($doc->status == 'pending' || $doc->status == 'rejected')
                                                <form action="{{ route('tickets.documents.destroy', $doc) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-warning text-dark" onclick="return confirm('Delete this document?')" title="Delete">
                                                        <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <small class="text-muted">Not yet uploaded</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            @php
                                                $metadata = \App\Models\TicketDocument::getDocumentTypesWithMeta();
                                                $docConfig = $metadata['mandatory'][$key] ?? [];
                                                $inputMethods = $docConfig['input_methods'] ?? ['upload'];
                                                $hasTemplate = $docConfig['has_template'] ?? false;
                                                $allowMultiple = $docConfig['allow_multiple'] ?? false;
                                            @endphp
                                            
                                            @if(in_array('form', $inputMethods))
                                                <a href="{{ route('tickets.documents.form', [$ticket, $key]) }}" class="btn btn-sm btn-success" title="Fill Form">
                                                    <i class="bi bi-pencil-square"></i> Form
                                                </a>
                                            @endif
                                            
                                            @if(in_array('upload', $inputMethods))
                                                @if($allowMultiple)
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="showMultipleUploadModal('{{ $key }}', '{{ is_array($name) ? $name['name'] : $name }}')" title="Upload Multiple Files">
                                                        <i class="bi bi-cloud-upload"></i> Upload
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="showUploadModal('{{ $key }}', '{{ is_array($name) ? $name['name'] : $name }}')" title="Upload File">
                                                        <i class="bi bi-cloud-upload"></i> Upload
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            @if($key == 'project_plan' && $ticket->project)
                                                <form action="{{ route('tickets.documents.generate-plan', $ticket) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning text-dark" title="Auto-Generate dari Tasks">
                                                        <i class="bi bi-gear-fill"></i> Generate
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($hasTemplate)
                                                <a href="{{ route('tickets.documents.template', $key) }}" class="btn btn-sm btn-secondary" title="Download Template" target="_blank">
                                                    <i class="bi bi-download"></i> Template
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <!-- Supporting Documents -->
                        <div class="tab-pane fade" id="supporting-docs">
                            @foreach($documentTypes['supporting'] as $key => $name)
                            @php
                                $doc = $ticket->documents->where('document_type', $key)->first();
                            @endphp
                            <div class="doc-item p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $name }}</h6>
                                        @if($doc)
                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($doc->status) }}
                                                </span>
                                                <small class="text-muted">{{ $doc->file_name }} ({{ $doc->getFileSizeFormatted() }})</small>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                Uploaded by {{ $doc->uploader->name }} on {{ $doc->created_at->format('M d, Y') }}
                                            </small>
                                            @if($doc->rejection_reason)
                                            <div class="alert alert-danger mt-2 mb-0 py-2">
                                                <small><strong>Rejected:</strong> {{ $doc->rejection_reason }}</small>
                                            </div>
                                            @endif
                                        @else
                                            <small class="text-muted">Not yet uploaded (Optional)</small>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($doc)
                                            <a href="{{ route('tickets.documents.download', $doc) }}" class="btn btn-sm btn-info" title="Download">
                                                <i class="bi bi-download"></i> <span class="d-none d-md-inline">Download</span>
                                            </a>
                                            @if($doc->status == 'pending')
                                                @can('update', $ticket)
                                                <form action="{{ route('tickets.documents.approve', $doc) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                        <i class="bi bi-check-circle"></i> <span class="d-none d-md-inline">Approve</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $doc->id }})" title="Reject">
                                                    <i class="bi bi-x-circle"></i> <span class="d-none d-md-inline">Reject</span>
                                                </button>
                                                @endcan
                                            @endif
                                            @if($doc->status == 'pending' || $doc->status == 'rejected')
                                                <form action="{{ route('tickets.documents.destroy', $doc) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-warning text-dark" onclick="return confirm('Delete this document?')" title="Delete">
                                                        <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            @php
                                                $metadata = \App\Models\TicketDocument::getDocumentTypesWithMeta();
                                                $docConfig = $metadata['supporting'][$key] ?? [];
                                                $inputMethods = $docConfig['input_methods'] ?? ['upload'];
                                                $hasTemplate = $docConfig['has_template'] ?? false;
                                            @endphp
                                            
                                            @if(in_array('upload', $inputMethods))
                                                <button type="button" class="btn btn-sm btn-primary border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;" onclick="showUploadModal('{{ $key }}', '{{ is_array($name) ? $name['name'] : $name }}')">
                                                    <i class="bi bi-cloud-upload"></i> Upload
                                                </button>
                                            @endif
                                            
                                            @if($hasTemplate)
                                                <a href="{{ route('tickets.documents.template', $key) }}" class="btn btn-sm btn-secondary" title="Download Template" target="_blank">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            @can('update', $ticket)
            @if($ticket->status != 'completed')
            <div class="card card-purple-gradient mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @if($ticket->hasAllMandatoryDocuments() && $ticket->current_stage == 6)
                    <form action="{{ route('tickets.complete', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 fw-bold mb-2">
                            <i class="bi bi-check-circle"></i> Mark as Completed
                        </button>
                    </form>
                    @else
                    <div class="quick-action-alert">
                        Complete all stages and upload all mandatory documents to finish this ticket.
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endcan

            <!-- Document Summary -->
            <div class="card card-purple-gradient mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Document Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="doc-progress-label">
                            <span>Mandatory Docs</span>
                            <span class="badge bg-{{ $ticket->hasAllMandatoryDocuments() ? 'warning' : 'warning' }} text-dark border border-warning" style="background-color: #ffc107 !important; color: #fff !important;">
                                {{ $ticket->documents()->mandatory()->approved()->count() }}/6
                            </span>
                        </div>
                        <div class="doc-progress-bar">
                            <div class="doc-progress-fill {{ $ticket->hasAllMandatoryDocuments() ? 'bg-success' : 'bg-secondary' }}" style="width: {{ ($ticket->documents()->mandatory()->approved()->count() / 6) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="doc-progress-label">
                            <span>Supporting Docs</span>
                            <span class="badge bg-info text-white">
                                {{ $ticket->documents()->supporting()->approved()->count() }}/5
                            </span>
                        </div>
                        <div class="doc-progress-bar">
                            <div class="doc-progress-fill bg-info" style="width: {{ ($ticket->documents()->supporting()->approved()->count() / 5) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between small text-muted border-top pt-3">
                        <div>Pending Approval: <strong class="text-dark">{{ $ticket->documents()->pending()->count() }}</strong></div>
                        <div>Approved: <strong class="text-success">{{ $ticket->documents()->approved()->count() }}</strong></div>
                        <div>Rejected: <strong class="text-danger">{{ $ticket->documents()->rejected()->count() }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Status History -->
            <div class="card card-purple-gradient mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Status History</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ticket->statusHistory as $history)
                        <li class="list-group-item border-bottom-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">{{ $history->created_at->format('M d, Y H:i') }}</small>
                                    <span class="fw-bold text-dark small">
                                        {{ ucfirst(str_replace('_', ' ', $history->old_status ?? 'New')) }}
                                        <span class="mx-2 text-muted">
                                            <!-- Space or dot -->
                                        </span>
                                        {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="avatar-initial rounded-circle bg-white text-dark border d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px; font-size: 11px;" title="{{ $history->user->name ?? 'System' }}">
                                        {{ substr($history->user->name ?? 'S', 0, 1) }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted small py-3">
                            No history recorded yet.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

    
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="uploadForm" action="{{ route('tickets.documents.upload', $ticket) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_type" id="document_type">
                    <h6 class="fw-bold mb-3" id="document_name"></h6>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="document_file" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                        <small class="text-muted">Accepted: PDF, DOC, DOCX, XLS, XLSX (Max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tickets.assign', $ticket) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Assign Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" name="assigned_to[]" multiple required style="height: 120px;">
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $ticket->assignees->contains('id', $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple users.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-check"></i> Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stage Progress Modal -->
<div class="modal" id="stageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tickets.stage', $ticket) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Progress to Next Stage</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> Anda dapat memilih stage tujuan. Stage yang dilewati akan ditandai sebagai "Skipped".
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Stage <span class="text-danger">*</span></label>
                        <select class="form-select" name="target_stage" required>
                            <option value="">Pilih stage tujuan...</option>
                            @foreach($ticket->stages as $stage)
                                @if($stage->stage_number > $ticket->current_stage)
                                    <option value="{{ $stage->stage_number }}" {{ $stage->stage_number == $ticket->current_stage + 1 ? 'selected' : '' }}>
                                        {{ $stage->stage_number }}. {{ $stage->stage_name }}
                                        @if($stage->stage_number == $ticket->current_stage + 1)
                                            (Next)
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih stage yang ingin dituju. Default: Stage berikutnya.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes about this stage completion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-right-circle"></i> Progress
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Update Status Modal -->
<div class="modal" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tickets.update-status', $ticket) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Update Ticket Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            @foreach(['open', 'in_progress', 'on_hold', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ $ticket->status == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Multiple Upload Modal -->
<div class="modal" id="multipleUploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tickets.documents.upload-multiple', $ticket) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Upload Multiple Files</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_type" id="multiple_document_type">
                    <h6 class="fw-bold mb-3" id="multiple_document_name"></h6>
                    <div class="alert alert-info py-2">
                        <small><i class="bi bi-info-circle"></i> Upload max 10 files (PDF, Images, Docs)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Files <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="files[]" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" multiple required>
                        <small class="text-muted">PDF, PNG, JPG, DOC, DOCX (Max 10MB each)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload"></i> Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
function showUploadModal(type, name) {
    document.getElementById('document_type').value = type;
    document.getElementById('document_name').textContent = name;
    new bootstrap.Modal(document.getElementById('uploadModal')).show();
}

function showMultipleUploadModal(type, name) {
    document.getElementById('multiple_document_type').value = type;
    document.getElementById('multiple_document_name').textContent = name;
    new bootstrap.Modal(document.getElementById('multipleUploadModal')).show();
}

function showRejectModal(documentId) {
    const form = document.getElementById('rejectForm');
    form.action = '{{ url("tickets/documents") }}/' + documentId + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
