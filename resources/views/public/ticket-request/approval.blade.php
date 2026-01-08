<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Approval - {{ $approval->ticket->ticket_number }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .approval-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: none;
        }
        .approval-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .ticket-number {
            font-family: monospace;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .status-badge {
            font-size: 0.8em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .section-title {
            color: #667eea;
            font-weight: 600;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9em;
        }
        .info-value {
            color: #333;
            font-size: 1.05em;
        }
        .btn-approve {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-approve:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(session('success'))
                    <div class="alert alert-success mb-4 rounded-3 shadow-sm border-0">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger mb-4 rounded-3 shadow-sm border-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="approval-card">
                    <div class="approval-header">
                        <h4 class="mb-2">Approval Request</h4>
                        <p class="mb-0 opacity-75">
                            Hello <strong>{{ $approval->approver_name }}</strong>, please review the following ticket.
                        </p>
                    </div>

                    <div class="p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="ticket-number text-white bg-secondary">{{ $approval->ticket->ticket_number }}</span>
                            </div>
                            <div>
                                <span class="badge bg-{{ $approval->status == 'approved' ? 'success' : 'warning' }} px-3 py-2 status-badge">
                                    {{ $approval->status }}
                                </span>
                            </div>
                        </div>

                        <h2 class="mb-4 text-dark">{{ $approval->ticket->title }}</h2>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Project</div>
                                <div class="info-value">{{ $approval->ticket->project ? $approval->ticket->project->title : 'N/A' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Type</div>
                                <div class="info-value text-uppercase">{{ $approval->ticket->type }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Priority</div>
                                <div class="info-value">
                                    @php
                                        $priorityColors = [
                                            'very_low' => 'text-success',
                                            'low' => 'text-success',
                                            'medium' => 'text-warning',
                                            'high' => 'text-primary',
                                            'very_high' => 'text-danger',
                                            'urgent' => 'text-danger fw-bold',
                                            'super_urgent' => 'text-danger fw-bold'
                                        ];
                                        $pColor = $priorityColors[$approval->ticket->priority] ?? 'text-dark';
                                    @endphp
                                    <span class="{{ $pColor }} text-capitalize">
                                        @if(in_array($approval->ticket->priority, ['urgent', 'super_urgent']))
                                            <i class="bi bi-fire"></i>
                                        @endif
                                        {{ str_replace('_', ' ', $approval->ticket->priority) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Submitted By</div>
                                <div class="info-value">{{ $approval->ticket->guest_name }} ({{ $approval->ticket->guest_department }})</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="info-label">Target Deadline</div>
                            <div class="info-value">
                                @if($approval->ticket->documents->where('document_type', 'request_form')->first())
                                    {{ $approval->ticket->created_at->format('d M Y') }} 
                                    <small class="text-muted">(Check details in documents)</small>
                                @else
                                    {{ $approval->ticket->created_at->format('d M Y') }}
                                @endif
                            </div>
                        </div>

                        <h5 class="section-title">Description</h5>
                        <div class="bg-light p-3 rounded-3 mb-4 border">
                            {!! $approval->ticket->description !!}
                        </div>

                        <h5 class="section-title">Documents</h5>
                        @if($approval->ticket->documents->count() > 0)
                            <div class="list-group mb-4">
                                @foreach($approval->ticket->documents as $doc)
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i> {{ $doc->file_name }}
                                            <div class="small text-muted ms-4">{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</div>
                                        </div>
                                        <i class="bi bi-download text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-4">No documents attached.</p>
                        @endif

                        @if($approval->status === 'pending')
                            <hr class="my-4">
                            <form action="{{ route('public.approval.submit', $approval->approval_token) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Comments (Optional)</label>
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Add any notes for the team..."></textarea>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-lg me-md-2" onclick="return confirm('Are you sure you want to REJECT this ticket? This action cannot be undone.')">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                    <button type="submit" name="action" value="approve" class="btn btn-approve btn-lg text-white flex-grow-1" onclick="return confirm('Are you sure you want to approve this ticket?')">
                                        <i class="bi bi-check-lg"></i> Approve Ticket
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-success mt-4">
                                <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Approved!</h5>
                                <p class="mb-0">You approved this ticket on {{ $approval->approved_at->format('d M Y, H:i') }}</p>
                                @if($approval->comment)
                                    <hr>
                                    <p class="mb-0 small"><strong>Your Comment:</strong> {{ $approval->comment }}</p>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted small">
                    &copy; {{ date('Y') }} Project Manager System
                </div>
            </div>
        </div>
    </div>
</body>
</html>
