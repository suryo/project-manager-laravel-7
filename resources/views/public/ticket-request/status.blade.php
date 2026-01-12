<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Request - {{ $ticket->tracking_token }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        @media (min-width: 768px) {
            .grid-span-2 {
                grid-column: span 2;
            }
        }
        
        .description-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #667eea;
            margin-top: 20px;
        }

        .approval-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-left: 4px solid #6c757d; /* default secondary */
            transition: transform 0.2s;
        }
        
        .approval-card.approved { border-left-color: #28a745; }
        .approval-card.rejected { border-left-color: #dc3545; }
        .approval-card.pending { border-left-color: #ffc107; }

        .contact-card {
            background: #fff9db;
            border: 1px solid #ffeeba;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-top: 40px;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .status-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .status-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .status-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        
        .tracking-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .tracking-token {
            font-size: 24px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }
        
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-body {
            padding: 40px;
        }
        
        .ticket-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            padding: 15px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .detail-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }
        
        /* Stage Timeline */
        .stage-timeline {
            position: relative;
            padding: 30px 0;
        }
        
        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 40px;
            bottom: -30px;
            width: 2px;
            background: #dee2e6;
        }
        
        .timeline-item:last-child::before {
            display: none;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }
        
        .timeline-item.completed .timeline-icon {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .timeline-item.active .timeline-icon {
            background: #667eea;
            border-color: #667eea;
            color: white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .timeline-content {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
        }
        
        .timeline-item.completed .timeline-content {
            border-color: #28a745;
            background: #f0fff4;
        }
        
        .timeline-item.active .timeline-content {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .timeline-title {
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }
        
        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        /* Documents Section */
        .documents-list {
            margin-top: 30px;
        }
        
        .document-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .document-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .document-icon {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #667eea;
        }
        
        .document-details h6 {
            margin: 0 0 5px;
            font-weight: 600;
        }
        
        .document-details small {
            color: #6c757d;
        }
        
        .btn-refresh {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-refresh:hover {
            transform: scale(1.1);
            background: #5568d3;
        }
        
        .contact-info {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <div class="status-card">
            <!-- Header -->
            <div class="status-header">
                <div class="tracking-info">
                    <div>
                        <div class="text-white-50 mb-1">Tracking Number</div>
                        <div class="tracking-token">{{ $ticket->tracking_token }}</div>
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'open' => ['bg' => 'warning', 'text' => 'dark'],
                                'in_progress' => ['bg' => 'info', 'text' => 'white'],
                                'completed' => ['bg' => 'success', 'text' => 'white'],
                                'closed' => ['bg' => 'secondary', 'text' => 'white'],
                            ];
                            $colors = $statusColors[$ticket->status] ?? ['bg' => 'secondary', 'text' => 'white'];
                        @endphp
                        <span class="status-badge bg-{{ $colors['bg'] }} text-{{ $colors['text'] }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                        <!-- Status Edit Trigger -->
                        <button type="button" class="btn btn-sm btn-light ms-2 rounded-circle" data-bs-toggle="modal" data-bs-target="#updateStatusModal" style="width: 32px; height: 32px; padding: 0;" title="Update Status">
                            <i class="bi bi-pencil-fill text-primary" style="font-size: 14px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="status-body">
                <div class="row">
                    <!-- Ticket Details (Left Column) -->
                    <div class="col-lg-8">
                        <div class="ticket-details h-100">
                            <h4 class="mb-4"><i class="bi bi-info-circle"></i> Request Details</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Ticket Number</div>
                                    <div class="detail-value">{{ $ticket->ticket_number }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Request Title</div>
                                    <div class="detail-value">{{ $ticket->title }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Requester</div>
                                    <div class="detail-value">{{ $ticket->guest_name }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Department</div>
                                    <div class="detail-value">{{ $ticket->guest_department }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Priority</div>
                                    <div class="detail-value" style="color: {{ ['very_low' => '#28a745', 'low' => '#28a745', 'medium' => '#ffc107', 'high' => '#fd7e14', 'very_high' => '#dc3545', 'urgent' => '#dc3545', 'super_urgent' => '#b02a37'][$ticket->priority] ?? '#6c757d' }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Estimation</div>
                                    <div class="detail-value">
                                        @if($ticket->estimation_in_days)
                                            {{ $ticket->estimation_in_days }} {{ Str::plural('Day', $ticket->estimation_in_days) }}
                                            <div class="small text-muted fw-normal mt-1" style="font-size: 0.75rem;">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                Est. Finish: {{ $ticket->created_at->addWeekdays($ticket->estimation_in_days)->format('M d, Y') }}
                                            </div>
                                        @else
                                            <span class="text-muted fw-normal">Not set</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Submitted</div>
                                    <div class="detail-value">{{ $ticket->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="detail-item grid-span-2">
                                    <div class="detail-label">Assigned To</div>
                                    <div class="detail-value">
                                        @php
                                            $activeAssignees = $ticket->assignees->filter(function($assignee) {
                                                return is_null($assignee->pivot->removed_at);
                                            });
                                        @endphp
                                        
                                        @if($activeAssignees->count() > 0)
                                            {{ $activeAssignees->pluck('name')->join(', ') }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($ticket->description)
                            <div class="description-card shadow-sm">
                                <div class="detail-label">Description</div>
                                <div class="mb-0 text-break">{!! $ticket->description !!}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status History (Right Column) -->
                    <div class="col-lg-4">
                        <div class="ticket-details h-100">
                            <h4 class="mb-4"><i class="bi bi-clock-history"></i> Status History</h4>
                            <div class="timeline-simple">
                                @forelse($ticket->statusHistory as $history)
                                <div class="d-flex mb-3 position-relative">
                                    <!-- Line -->
                                    @if(!$loop->last)
                                    <div style="position: absolute; left: 11px; top: 24px; bottom: -16px; width: 2px; background: #e9ecef;"></div>
                                    @endif
                                    
                                    <!-- Icon -->
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-3" style="width: 24px; height: 24px; z-index: 1;">
                                        <div class="bg-secondary rounded-circle" style="width: 8px; height: 8px;"></div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div>
                                        <div class="small fw-bold">{{ ucfirst(str_replace('_', ' ', $history->new_status)) }}</div>
                                        <div style="font-size: 11px;" class="text-muted">
                                            {{ $history->created_at->format('M d, Y H:i') }}
                                            @if($history->user)
                                            by {{ $history->user->name }}
                                            @elseif($history->guest_name)
                                            by {{ $history->guest_name }}
                                            <div class="mt-1 border-start border-2 ps-2">
                                                <div style="font-size: 10px;">
                                                    <i class="bi bi-envelope"></i> {{ $history->guest_email }}
                                                </div>
                                                <div style="font-size: 10px;">
                                                    <i class="bi bi-telephone"></i> {{ $history->guest_phone }}
                                                </div>
                                            </div>
                                            @else
                                            (System)
                                            @endif
                                        </div>
                                        @if($history->old_status)
                                        <div style="font-size: 11px;" class="text-muted mt-1">
                                            From: {{ ucfirst(str_replace('_', ' ', $history->old_status)) }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-center text-muted small py-3">
                                    No history recorded.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage Timeline -->
                <h4 class="mb-4 mt-5"><i class="bi bi-clock-history"></i> Progress Timeline</h4>
                <div class="stage-timeline">
                    @foreach($ticket->stages->whereIn('status', ['completed', 'in_progress', 'skipped']) as $stage)
                    <div class="timeline-item {{ $stage->status === 'completed' ? 'completed' : ($stage->status === 'in_progress' ? 'active' : '') }}">
                        <div class="timeline-icon">
                            @if($stage->status === 'completed')
                                <i class="bi bi-check-lg"></i>
                            @elseif($stage->status === 'in_progress')
                                <i class="bi bi-arrow-repeat"></i>
                            @elseif($stage->status === 'skipped')
                                <i class="bi bi-skip-forward"></i>
                            @else
                                <i class="bi bi-circle"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $stage->stage_name }}</div>
                            @if($stage->started_at)
                            <div class="timeline-date">
                                <i class="bi bi-calendar"></i> Started: {{ $stage->started_at->format('M d, Y - H:i') }}
                            </div>
                            @endif
                            @if($stage->completed_at)
                            <div class="timeline-date">
                                <i class="bi bi-check-circle"></i> Completed: {{ $stage->completed_at->format('M d, Y - H:i') }}
                            </div>
                            @endif
                            @if($stage->status === 'in_progress')
                            <div class="alert alert-info mb-0 mt-2">
                                <small><i class="bi bi-info-circle"></i> This stage is currently in progress</small>
                            </div>
                            @endif
                            @if($stage->status === 'skipped')
                            <div class="alert alert-warning mb-0 mt-2">
                                <small><i class="bi bi-skip-forward"></i> This stage was skipped</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Approvals Section -->
                @if($ticket->approvals->count() > 0)
                <div class="mb-5">
                    <h4 class="mb-4"><i class="bi bi-person-check"></i> Approval Status</h4>
                    <div class="row g-3">
                        @foreach($ticket->approvals as $approval)
                        <div class="col-md-6">
                            <div class="approval-card {{ $approval->status }} h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">{{ $approval->approver_name }}</h6>
                                    @if($approval->status === 'approved')
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.75rem;"><i class="bi bi-check-lg"></i> Approved</span>
                                    @elseif($approval->status === 'rejected')
                                        <span class="badge bg-danger rounded-pill" style="font-size: 0.75rem;"><i class="bi bi-x-lg"></i> Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.75rem;"><i class="bi bi-hourglass-split"></i> Pending</span>
                                    @endif
                                </div>
                                
                                @if($approval->approved_at)
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-clock"></i> {{ $approval->approved_at->format('M d, Y H:i') }}
                                    </div>
                                @endif

                                @if($approval->comment)
                                    <div class="mt-2 p-2 bg-light rounded small text-muted fst-italic">
                                        "{{ $approval->comment }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Documents -->
                @if($ticket->documents->count() > 0)
                <div class="documents-list">
                    <h4 class="mb-4"><i class="bi bi-file-earmark-text"></i> Submitted Documents ({{ $ticket->documents->count() }})</h4>
                    
                    @foreach($ticket->documents->where('parent_id', null) as $doc)
                    <div class="document-item">
                        <div class="document-info">
                            <div class="document-icon">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </div>
                            <div class="document-details">
                                <h6>{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</h6>
                                <small>
                                    <i class="bi bi-file-earmark"></i> {{ $doc->file_name }} 
                                    ({{ $doc->getFileSizeFormatted() }})
                                </small>
                                <br>
                                <small class="text-muted">
                                    Uploaded: {{ $doc->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div>
                            @if($doc->status === 'approved')
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                            @elseif($doc->status === 'rejected')
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending Review</span>
                            @endif
                        </div>
                    </div>
                    
                    @php
                        $children = $ticket->documents->where('parent_id', $doc->id);
                    @endphp
                    @if($children->count() > 0)
                        @foreach($children as $child)
                        <div class="document-item" style="margin-left: 40px; border-left: 4px solid #667eea;">
                            <div class="document-info">
                                <div class="document-icon" style="font-size: 20px;">
                                    <i class="bi bi-file-earmark"></i>
                                </div>
                                <div class="document-details">
                                    <h6 style="font-size: 14px;">{{ $child->file_name }}</h6>
                                    <small class="text-muted">{{ $child->getFileSizeFormatted() }}</small>
                                </div>
                            </div>
                            <div>
                                @if($child->status === 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                                @elseif($child->status === 'rejected')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @endforeach
                </div>
                @endif

                <!-- Contact Info -->
                <div class="contact-card">
                    <h5 class="mb-3 text-dark"><i class="bi bi-headset"></i> Need Help?</h5>
                    <p class="text-muted mb-3">If you have any questions about your request, our support team is here to help.</p>
                    <div class="d-flex justify-content-center gap-4 flex-wrap mb-3">
                        <div class="fw-bold text-dark"><i class="bi bi-envelope me-2"></i>support@company.com</div>
                        <div class="fw-bold text-dark"><i class="bi bi-telephone me-2"></i>+62 123-4567-8900</div>
                    </div>
                    <div class="small text-muted border-top border-warning pt-3 d-inline-block px-4">
                        Reference Number: <strong class="text-dark font-monospace">{{ $ticket->tracking_token }}</strong>
                    </div>
                </div>



                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <a href="{{ route('public.ticket-request') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Submit New Request
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="btn-refresh" onclick="window.location.reload()" title="Refresh Status">
        <i class="bi bi-arrow-clockwise"></i>
    </button>

    <!-- Status Update Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Update Ticket Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('public.ticket-request.update-status', $ticket->tracking_token) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" class="form-control" required placeholder="Enter your name">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="guest_email" class="form-control" required placeholder="Enter your email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="guest_phone" class="form-control" required placeholder="Enter phone number">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Status</label>
                            <select name="status" class="form-select" required>
                                <option value="" disabled selected>Select Status...</option>
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="cancelled" {{ $ticket->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="completed" {{ $ticket->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to update the status?')">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
