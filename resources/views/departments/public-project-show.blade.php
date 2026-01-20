@extends('layouts.guest')

@push('styles')
<style>
    /* Override guest layout background if needed, or adapt components */
    .modal-content {
        border: 4px solid #000 !important;
        border-radius: 0 !important;
        box-shadow: 10px 10px 0 #000;
        background-color: var(--neo-card-bg, #fff);
    }
    .modal-header {
        border-bottom: 4px solid #000 !important;
        background-color: var(--neo-primary, #667eea);
        color: #fff;
        text-shadow: 1px 1px 0 #000;
    }
    .hover-link:hover {
        text-decoration: underline !important;
        color: var(--neo-primary, #667eea) !important;
    }
    /* Ensure container has some padding in guest mode if needed */
    .container {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('department.landing', $department->slug) }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
                <i class="bi bi-arrow-left me-1"></i> Back to {{ $department->name }}
            </a>
            <h1 class="fw-bold mb-0 text-uppercase h3 mt-2">{{ $project->title }}</h1>
        </div>
        @if($project->status)
            <span class="badge rounded-pill border border-2 border-dark" style="background-color: {{ $project->status->color }}; color: #fff; font-size: 1rem;">
                {{ $project->status->name }}
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-body">
                    <div class="text-muted mb-3 description-content small">
                        {!! $project->description !!}
                    </div>
                    <hr class="border-2 border-dark opacity-100 mb-3">
                    
                    <div class="mb-4">
                        <label class="text-uppercase extra-small fw-900 d-block mb-2 text-muted">Project Owner</label>
                        <div class="d-flex align-items-center bg-white border border-2 border-dark p-2" style="box-shadow: 3px 3px 0 #000;">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3 border border-2 border-dark" style="width: 36px; height: 36px; font-size: 0.9rem; font-weight: 800; box-shadow: 2px 2px 0 rgba(0,0,0,0.2);">
                                {{ strtoupper(substr($project->user->name, 0, 1)) }}
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-900 text-dark" style="font-size: 0.95rem; line-height: 1.1;">{{ $project->user->name }}</span>
                                <span class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Project Lead</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-uppercase extra-small fw-800 d-block text-muted">Start Date</label>
                            <span class="fw-bold small">{{ optional($project->start_date)->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-uppercase extra-small fw-800 d-block text-muted">End Date</label>
                            <span class="fw-bold small">{{ optional($project->end_date)->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- TICKETS --}}
            <div class="card mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header py-3 px-4 bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h4">TICKETS</h5>
                </div>
                <div class="card-body p-0">
                    @if($project->tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase small fw-900 border-0">Number</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0">Title</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0">Status</th>
                                        <th class="text-end pe-4 py-3 text-uppercase small fw-900 border-0">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->tickets as $ticket)
                                        <tr class="border-bottom border-1 border-dark">
                                            <td class="ps-4 fw-bold text-dark">{{ $ticket->ticket_number }}</td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket) }}" class="fw-bold text-dark text-decoration-none hover-link">
                                                    {{ $ticket->title }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary border border-1 border-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem; box-shadow: 2px 2px 0 #000;">
                                                    {{ $ticket->status }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 text-muted small">
                                                {{ $ticket->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-ticket-perforated display-4 text-muted mb-3 d-block"></i>
                            <p class="text-muted italic">No tickets found for this project.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TASKS (Read Only for Department View) --}}
            <div class="card" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header py-3 px-4 bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h4">Project Tasks</h5>
                </div>
                <div class="card-body p-0">
                     @if($project->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase small fw-900 border-0">Title</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0">Status</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0">Assignee</th>
                                        <th class="text-end pe-4 py-3 text-uppercase small fw-900 border-0">Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->tasks as $task)
                                        <tr class="border-bottom border-1 border-dark">
                                            <td class="ps-4 fw-bold text-dark">{{ $task->title }}</td>
                                            <td>
                                                @php
                                                    $statusClass = $task->status === 'done' ? 'bg-success' : ($task->status === 'in_progress' ? 'bg-info text-dark' : 'bg-secondary');
                                                @endphp
                                                <span class="badge {{ $statusClass }} border border-1 border-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem; box-shadow: 2px 2px 0 #000;">
                                                    {{ str_replace('_', ' ', $task->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center flex-wrap gap-1">
                                                    @forelse($task->assignees as $member)
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center border border-1 border-dark" style="width: 24px; height: 24px; font-size: 0.65rem; margin-right: -8px;" title="{{ $member->name }}">
                                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                                        </div>
                                                    @empty
                                                        <span class="text-muted extra-small">None</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                @if($task->due_date)
                                                    <span class="extra-small {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger fw-800' : 'text-muted fw-600' }}">
                                                        {{ $task->due_date->format('M d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted extra-small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x display-4 text-muted mb-3 d-block"></i>
                            <p class="text-muted italic">No tasks found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
