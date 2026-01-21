@extends('layouts.visitor')

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .gantt-container {
        border: 2px solid #000;
        background: #fff;
        overflow: auto;
        min-height: 250px;
        box-shadow: 5px 5px 0 #000;
    }
</style>
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
        <div class="col-12 mb-5">
            {{-- POAC Management Pillars --}}
            <div class="card border-3 border-dark rounded-0" style="box-shadow: 10px 10px 0 #000;">
                <div class="card-header py-3 px-4 bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">
                        <i class="bi bi-grid-3x3-gap me-2"></i> Management Framework (POAC)
                    </h5>
                    <div class="badge bg-warning text-dark border border-1 border-dark px-3 py-2 text-uppercase fw-900" style="font-size: 0.7rem; box-shadow: 3px 3px 0 rgba(255,255,255,0.2);">
                        Current Phase: {{ $project->mgmt_phase }}
                    </div>
                </div>
                <div class="card-body p-4 bg-light">
                    <div class="row g-4">
                        @php
                            $phases = [
                                'Planning' => ['icon' => 'bi-journal-check', 'color' => '#FF6B6B', 'notes' => 'mgmt_planning_notes'],
                                'Organizing' => ['icon' => 'bi-diagram-3', 'color' => '#4D96FF', 'notes' => 'mgmt_organizing_notes'],
                                'Actuating' => ['icon' => 'bi-play-circle', 'color' => '#6BCB77', 'notes' => 'mgmt_actuating_notes'],
                                'Controlling' => ['icon' => 'bi-shield-check', 'color' => '#FFD93D', 'notes' => 'mgmt_controlling_notes'],
                            ];
                        @endphp
                        @foreach($phases as $name => $info)
                            <div class="col-lg-3 col-md-6">
                                <div class="h-100 bg-white border border-3 border-dark p-3 transition-hover" 
                                     style="box-shadow: 5px 5px 0 #000; position: relative; overflow: hidden;">
                                    <div style="position: absolute; top: 0; left: 0; width: 6px; height: 100%; background-color: {{ $info['color'] }};"></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-0 d-flex align-items-center justify-content-center text-white border border-2 border-dark" 
                                             style="width: 32px; height: 32px; background-color: {{ $info['color'] }}; box-shadow: 2px 2px 0 #000;">
                                            <i class="bi {{ $info['icon'] }} small"></i>
                                        </div>
                                        <h6 class="fw-900 mb-0 ms-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ $name }}</h6>
                                    </div>
                                    <div class="mgmt-notes-content text-muted small" style="min-height: 80px; font-size: 0.75rem; line-height: 1.5;">
                                        @if($project->{$info['notes']})
                                            {!! nl2br(e($project->{$info['notes']})) !!}
                                        @else
                                            <span class="fst-italic opacity-50">No documentation for this phase yet.</span>
                                        @endif
                                    </div>
                                    @if(auth()->check() && $department->members->contains(auth()->user()))
                                        <div class="mt-3 pt-3 border-top border-1 border-dark opacity-75">
                                            <button class="btn btn-sm btn-dark w-100 rounded-0 text-uppercase fw-bold" 
                                                    style="font-size: 0.65rem;"
                                                    onclick="openMgmtEdit('{{ $name }}', `{{ $project->{$info['notes']} }}`)">
                                                Update Phase
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Project Details Sidebar Card --}}
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

            {{-- Team Workload Distribution (Moved to Sidebar) --}}
            <div class="card border-3 border-dark rounded-0 mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">Team Workload</h5>
                </div>
                <div class="card-body">
                    <div style="height: 180px;">
                        <canvas id="workloadChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Project Health (Moved to Sidebar) --}}
            <div class="card border-3 border-dark rounded-0 mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">Project Health</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-white border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                <div class="extra-small fw-900 text-muted text-uppercase" style="font-size: 0.5rem;">Progress</div>
                                @php
                                    $totalTasksCount = $project->tasks->count();
                                    $doneTasksCount = $project->tasks->where('status', 'done')->count();
                                    $projectProgress = $totalTasksCount > 0 ? round(($doneTasksCount / $totalTasksCount) * 100) : 0;
                                @endphp
                                <div class="h5 fw-black mb-0">{{ $projectProgress }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                <div class="extra-small fw-900 text-muted text-uppercase" style="font-size: 0.5rem;">Overdue</div>
                                @php
                                    $overdueTasksCount = $project->tasks->where('status', '!=', 'done')->filter(function($t) {
                                        return $t->due_date && $t->due_date->isPast();
                                    })->count();
                                @endphp
                                <div class="h5 fw-black mb-0 {{ $overdueTasksCount > 0 ? 'text-danger' : 'text-success' }}">{{ $overdueTasksCount }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                <div class="extra-small fw-900 text-muted text-uppercase" style="font-size: 0.5rem;">Tickets</div>
                                <div class="h5 fw-black mb-0 text-primary">{{ $project->tickets->count() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                <div class="extra-small fw-900 text-muted text-uppercase" style="font-size: 0.5rem;">Team</div>
                                <div class="h5 fw-black mb-0 text-dark">{{ $department->members->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Visual Analytics Row --}}
            <div class="row g-4 mb-4">
                {{-- Timeline (Planning) --}}
                <div class="col-12">
                    <div class="card border-3 border-dark rounded-0 h-100" style="box-shadow: 8px 8px 0 #000;">
                        <div class="card-header bg-white border-bottom border-3 border-dark d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">Project Timeline (Gantt)</h5>
                            <div class="btn-group btn-group-sm border border-2 border-dark" role="group" style="box-shadow: 2px 2px 0 #000;">
                                <button type="button" class="btn btn-white fw-bold gantt-view-btn active" data-view="Day">Day</button>
                                <button type="button" class="btn btn-white fw-bold gantt-view-btn" data-view="Week">Week</button>
                                <button type="button" class="btn btn-white fw-bold gantt-view-btn" data-view="Month">Month</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="gantt-container">
                                <svg id="gantt"></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                                    $phaseColors = [
                                                        'Planning' => '#FF6B6B',
                                                        'Organizing' => '#4D96FF',
                                                        'Actuating' => '#6BCB77',
                                                        'Controlling' => '#FFD93D',
                                                    ];
                                                    $phaseColor = $phaseColors[$task->mgmt_phase] ?? '#6c757d';
                                                @endphp
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge {{ $statusClass }} border border-1 border-dark px-2 py-1 text-uppercase" style="font-size: 0.55rem; box-shadow: 2px 2px 0 #000;">
                                                        {{ str_replace('_', ' ', $task->status) }}
                                                    </span>
                                                    <span class="badge border border-1 border-dark px-2 py-1 text-uppercase text-dark {{ auth()->check() && $department->members->contains(auth()->user()) ? 'cursor-pointer' : '' }}" 
                                                          style="font-size: 0.55rem; background-color: {{ $phaseColor }}; box-shadow: 2px 2px 0 rgba(0,0,0,0.1);"
                                                          @if(auth()->check() && $department->members->contains(auth()->user()))
                                                            onclick="openTaskMgmtEdit({{ $task->id }}, '{{ $task->title }}', '{{ $task->mgmt_phase }}', `{{ $task->mgmt_notes }}`)"
                                                          @endif>
                                                        {{ $task->mgmt_phase }}
                                                        @if(auth()->check() && $department->members->contains(auth()->user()))
                                                            <i class="bi bi-pencil-square ms-1"></i>
                                                        @endif
                                                    </span>
                                                </div>
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
@if(auth()->check() && $department->members->contains(auth()->user()))
{{-- Project POAC Edit Modal --}}
<div class="modal fade" id="modalEditMgmt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-3 border-dark rounded-0">
            <div class="modal-header py-3 px-4 bg-dark text-white border-bottom border-3 border-dark">
                <h5 class="modal-title fw-black text-uppercase letter-spacing-1" id="mgmtModalTitle">UPDATE MANAGEMENT PHASE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department.project.mgmt-update', [$department->slug, $project->slug]) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="phase_name" id="inputPhaseName">
                    <div class="mb-4">
                        <label class="form-label fw-900 text-uppercase small text-muted">Management Pillars</label>
                        <select name="mgmt_phase" class="form-select border-2 border-dark rounded-0 shadow-none fw-bold">
                            <option value="Planning" {{ $project->mgmt_phase == 'Planning' ? 'selected' : '' }}>1. Planning (Perencanaan)</option>
                            <option value="Organizing" {{ $project->mgmt_phase == 'Organizing' ? 'selected' : '' }}>2. Organizing (Pengorganisasian)</option>
                            <option value="Actuating" {{ $project->mgmt_phase == 'Actuating' ? 'selected' : '' }}>3. Actuating (Pelaksanaan)</option>
                            <option value="Controlling" {{ $project->mgmt_phase == 'Controlling' ? 'selected' : '' }}>4. Controlling (Pengendalian)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-900 text-uppercase small text-muted" id="mgmtNotesLabel">Documentation</label>
                        <textarea name="notes" id="mgmtNotesArea" class="form-control border-2 border-dark rounded-0 shadow-none" rows="8" placeholder="Enter documentation for this phase..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top border-3 border-dark bg-white">
                    <button type="button" class="btn btn-outline-dark border-2 rounded-0 fw-bold px-4" data-bs-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn btn-primary border-2 border-dark rounded-0 fw-bold px-4 shadow-btn">SAVE CHANGES</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Task POAC Edit Modal --}}
<div class="modal fade" id="modalEditTaskMgmt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-3 border-dark rounded-0">
            <div class="modal-header py-3 px-4 bg-dark text-white border-bottom border-3 border-dark">
                <h5 class="modal-title fw-black text-uppercase letter-spacing-1 h6" id="taskMgmtTitle">UPDATE TASK MANAGEMENT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department.task.mgmt-update', [$department->slug, $project->slug]) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="task_id" id="inputTaskId">
                    <div class="mb-3">
                        <label class="form-label fw-900 text-uppercase small text-muted">Management Phase</label>
                        <select name="mgmt_phase" id="inputTaskPhase" class="form-select border-2 border-dark rounded-0 shadow-none fw-bold">
                            <option value="Planning">Planning</option>
                            <option value="Organizing">Organizing</option>
                            <option value="Actuating">Actuating</option>
                            <option value="Controlling">Controlling</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-900 text-uppercase small text-muted">Management Notes</label>
                        <textarea name="mgmt_notes" id="taskMgmtNotes" class="form-control border-2 border-dark rounded-0 shadow-none" rows="4" placeholder="Enter notes about this task's management..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top border-3 border-dark bg-white">
                    <button type="button" class="btn btn-outline-dark border-2 rounded-0 fw-bold px-4" data-bs-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn btn-primary border-2 border-dark rounded-0 fw-bold px-4 shadow-btn">SAVE TASK</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- GANTT CHART (Planning) ---
        const ganttTasks = [
            @foreach($project->tasks as $task)
            {
                id: '{{ $task->id }}',
                name: '{{ addslashes($task->title) }}',
                start: '{{ ($task->start_date ?? $project->start_date ?? now())->format('Y-m-d') }}',
                end: '{{ ($task->due_date ?? $task->start_date ?? $project->end_date ?? now()->addDay())->format('Y-m-d') }}',
                progress: {{ $task->status === 'done' ? 100 : ($task->status === 'in_progress' ? 50 : 0) }},
                custom_class: 'bar-{{ $task->status }}'
            },
            @endforeach
        ];

        if (ganttTasks.length > 0 && typeof Gantt !== 'undefined') {
            const gantt = new Gantt("#gantt", ganttTasks, {
                view_mode: 'Day',
                date_format: 'YYYY-MM-DD',
                bar_height: 25,
                padding: 18,
                custom_popup_html: function(task) {
                    return `
                        <div class="p-2 border border-2 border-dark bg-white shadow-sm" style="min-width: 150px;">
                            <div class="fw-bold small mb-1">${task.name}</div>
                            <div class="text-muted extra-small">${task.progress}% Complete</div>
                        </div>
                    `;
                }
            });
            document.querySelectorAll('.gantt-view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.gantt-view-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    gantt.change_view_mode(this.dataset.view);
                });
            });
        } else if (document.querySelector('.gantt-container')) {
            document.querySelector('.gantt-container').innerHTML = '<div class="text-center py-5 text-muted small italic">No tasks available to display on timeline.</div>';
        }

        @php
            $workload = [];
            foreach($project->tasks as $task) {
                foreach($task->assignees as $assignee) {
                    $workload[$assignee->name] = ($workload[$assignee->name] ?? 0) + 1;
                }
            }
            $labels = array_keys($workload);
            $data = array_values($workload);
        @endphp

        const ctx = document.getElementById('workloadChart');
        if (ctx) {
            const workloadLabels = {!! json_encode($labels) !!};
            const workloadData = {!! json_encode($data) !!};
            
            console.log('Workload Data:', { labels: workloadLabels, data: workloadData });

            if (workloadLabels.length > 0) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: workloadLabels,
                        datasets: [{
                            label: 'Tasks Assigned',
                            data: workloadData,
                            backgroundColor: '#4D96FF',
                            borderColor: '#000',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, color: '#000', font: { weight: 'bold' } },
                                grid: { color: '#ddd' }
                            },
                            x: {
                                ticks: { color: '#000', font: { weight: 'bold' } },
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            } else {
                ctx.parentNode.innerHTML = '<div class="text-center py-4 text-muted small italic">No workload data available (no assignees on tasks).</div>';
            }
        }
    });

    function openMgmtEdit(phase, currentNotes) {
        document.getElementById('inputPhaseName').value = phase;
        document.getElementById('mgmtModalTitle').innerText = 'UPDATE: ' + phase.toUpperCase();
        document.getElementById('mgmtNotesLabel').innerText = phase + ' Documentation';
        document.getElementById('mgmtNotesArea').value = currentNotes;
        
        var modal = new bootstrap.Modal(document.getElementById('modalEditMgmt'));
        modal.show();
    }

    function openTaskMgmtEdit(taskId, taskTitle, phase, notes) {
        document.getElementById('inputTaskId').value = taskId;
        document.getElementById('taskMgmtTitle').innerText = 'TASK MGMT: ' + taskTitle;
        document.getElementById('inputTaskPhase').value = phase;
        document.getElementById('taskMgmtNotes').value = notes;

        var modal = new bootstrap.Modal(document.getElementById('modalEditTaskMgmt'));
        modal.show();
    }
</script>
@endpush
@endsection
