@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-uppercase letter-spacing-1">
            <i class="bi bi-ticket-perforated"></i> Ticketing System
        </h2>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary border border-2 border-dark fw-bold text-uppercase" style="box-shadow: 4px 4px 0 #000;">
            <i class="bi bi-plus-circle me-1"></i> New Ticket
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-2 border-dark rounded-0 shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-2 border-dark rounded-0 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Main Content (Filters & List) -->
        <div class="col-lg-9">

    <!-- Filters -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Ticket number or title..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">All Priority</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="new_feature" {{ request('type') == 'new_feature' ? 'selected' : '' }}>New Feature</option>
                        <option value="update" {{ request('type') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="bug_fix" {{ request('type') == 'bug_fix' ? 'selected' : '' }}>Bug Fix</option>
                        <option value="enhancement" {{ request('type') == 'enhancement' ? 'selected' : '' }}>Enhancement</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('tickets.index') }}" class="btn btn-white border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-4 py-3">Ticket #</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Project</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Estimation</th>
                            <th class="px-4 py-3">Status & Priority</th>
                            <th class="px-4 py-3">Stage</th>
                            <th class="px-4 py-3">Assigned To</th>
                            <th class="px-4 py-3">Approvers</th>
                            <th class="px-4 py-3">Created</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4 py-3">
                                <strong>{{ $ticket->ticket_number }}</strong>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <h6 class="mb-0">{{ Str::limit($ticket->title, 50) }}</h6>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->project)
                                    @if(Auth::user()->role === 'admin')
                                        <button type="button" class="btn btn-light bg-light text-dark border border-1 border-dark text-decoration-none badge rounded-pill" 
                                                title="Change Project: {{ $ticket->project->title }}"
                                                onclick="openLinkProjectModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}', {{ $ticket->project_id }})">
                                            <i class="bi bi-folder me-1"></i> {{ Str::limit($ticket->project->title, 15) }}
                                        </button>
                                    @else
                                        <a href="{{ route('projects.show', $ticket->project) }}" class="badge bg-light text-dark border border-1 border-dark text-decoration-none" title="{{ $ticket->project->title }}">
                                            <i class="bi bi-folder me-1"></i> {{ Str::limit($ticket->project->title, 15) }}
                                        </a>
                                    @endif
                                @else
                                    @if(Auth::user()->role === 'admin')
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="openLinkProjectModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}')">
                                            <i class="bi bi-link-45deg"></i> Link
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $ticket->type)) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->estimation_in_days)
                                    <span class="fw-bold">{{ $ticket->estimation_in_days }} Days</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex flex-column gap-2">
                                    @php
                                        $statusColors = [
                                            'open' => 'primary',
                                            'in_progress' => 'info',
                                            'on_hold' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'dark'
                                        ];
                                        $statusColor = $statusColors[$ticket->status] ?? 'secondary';

                                        $priorityColors = [
                                            'low' => 'secondary',
                                            'medium' => 'info',
                                            'high' => 'warning',
                                            'urgent' => 'danger'
                                        ];
                                        $priorityColor = $priorityColors[$ticket->priority] ?? 'secondary';
                                    @endphp
                                    
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                    
                                    <span class="badge bg-{{ $priorityColor }} border border-1 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <small class="text-muted">Stage {{ $ticket->current_stage }}/6</small>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ ($ticket->current_stage / 6) * 100 }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->activeAssignees->count() > 0)
                                    <div class="d-flex align-items-center">
                                        @foreach($ticket->activeAssignees->take(3) as $index => $assignee)
                                            <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center border border-white" 
                                                 title="{{ $assignee->name }}"
                                                 style="width: 30px; height: 30px; font-size: 12px; margin-left: {{ $index > 0 ? '-10px' : '0' }}; z-index: {{ 3 - $index }};">
                                                {{ substr($assignee->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if($ticket->activeAssignees->count() > 3)
                                            <div class="avatar-initial rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center border border-white" 
                                                 style="width: 30px; height: 30px; font-size: 10px; margin-left: -10px; z-index: 0;">
                                                +{{ $ticket->activeAssignees->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <small class="text-muted">Unassigned</small>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->approvals->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($ticket->approvals as $approval)
                                            @php
                                                $statusIcon = 'bi-clock-fill text-warning';
                                                if ($approval->status === 'approved') {
                                                    $statusIcon = 'bi-check-circle-fill text-success';
                                                } elseif ($approval->status === 'rejected') {
                                                    $statusIcon = 'bi-x-circle-fill text-danger';
                                                }
                                                $statusText = ucfirst($approval->status);
                                            @endphp
                                            <div class="mb-2" title="{{ $approval->approver_name }}: {{ $statusText }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $statusIcon }} me-1" style="font-size: 0.8rem;"></i>
                                                    <small class="fw-bold" style="font-size: 0.75rem;">{{ Str::limit($approval->approver_name, 15) }}</small>
                                                </div>
                                                <div class="ms-3 lh-1 mt-1">
                                                    <small class="text-muted d-block" style="font-size: 0.65rem;">{{ $statusText }}</small>
                                                    @if($approval->approved_at)
                                                        <small class="text-muted d-block" style="font-size: 0.65rem;">{{ $approval->approved_at->format('M d, H:i') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <small class="text-muted">{{ $ticket->created_at ? $ticket->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                            </td>
                            <td class="text-end pe-4 py-3">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-white border border-2 border-dark px-2 py-0 fw-bold" style="box-shadow: 2px 2px 0 #000; font-size: 0.75rem;">
                                    VIEW
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No tickets found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
    
    </div> <!-- End Main Content Col -->

    <!-- Right Sidebar (Energy Monitor) -->
    <div class="col-lg-3">
        <!-- Energy Monitor -->
        <div class="card bg-white shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header pt-4 px-4 bg-white border-0">
                <h5 class="fw-bold mb-0 text-primary">⚡ Energy Monitor</h5>
            </div>
            <div class="card-body px-4">
                <h6 class="fw-bold small text-muted text-uppercase mb-3">Monthly Capacity (176 Units)</h6>
                
                @foreach($staffMembers as $staff)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small">{{ $staff->name }}</span>
                            @if($staff->departments->isNotEmpty())
                                @php
                                    $used = $staff->used_energy;
                                    $limit = $staff->monthly_energy_limit;
                                    $percentage = ($limit > 0) ? ($used / $limit) * 100 : 0;
                                    $color = $percentage > 100 ? 'danger' : ($percentage > 80 ? 'warning' : 'success');
                                @endphp
                                <span class="small fw-bold text-{{ $color }}">{{ $used }} / {{ $limit }}</span>
                            @endif
                        </div>

                        @if($staff->departments->isEmpty())
                            <div class="alert alert-soft-warning border border-1 border-warning p-2 extra-small mb-0" style="font-size: 0.7rem;">
                                <i class="bi bi-exclamation-circle me-1"></i> User belum terdaftar pada departemen
                            </div>
                        @else
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ min(100, $percentage) }}%" aria-valuenow="{{ $used }}" aria-valuemin="0" aria-valuemax="{{ $limit }}"></div>
                            </div>
                            @if(isset($percentage) && $percentage > 100)
                                <small class="text-danger extra-small fw-bold">Over Capacity!</small>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div> <!-- End Sidebar Col -->
    </div> <!-- End Row -->


</div>

<!-- Dynamic Link Project Modal -->
<div class="modal fade" id="linkProjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="linkProjectForm" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Link Project</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">Linking ticket: <strong id="linkProjectTicketTitle" class="text-dark"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select" required>
                            <option value="">-- Choose Project --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-link"></i> Link Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openLinkProjectModal(ticketId, ticketTitle, currentProjectId = null) {
        const form = document.getElementById('linkProjectForm');
        let url = "{{ route('tickets.link-project', ':id') }}";
        url = url.replace(':id', ticketId);
        
        form.action = url;
        document.getElementById('linkProjectTicketTitle').textContent = ticketTitle;
        
        // Pre-select project if exists
        const select = form.querySelector('select[name="project_id"]');
        if (currentProjectId) {
            select.value = currentProjectId;
        } else {
            select.value = "";
        }
        
        new bootstrap.Modal(document.getElementById('linkProjectModal')).show();
    }
</script>
@endsection
