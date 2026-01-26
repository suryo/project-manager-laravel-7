@extends('layouts.app')

@push('styles')
<style>
    .modal-content {
        border: 4px solid #000 !important;
        border-radius: 0 !important;
        box-shadow: 10px 10px 0 #000;
        background-color: var(--neo-card-bg);
    }
    .modal-header {
        border-bottom: 4px solid #000 !important;
        background-color: var(--neo-primary);
        color: #fff;
        text-shadow: 1px 1px 0 #000;
    }
    .modal-footer {
        border-top: 4px solid #000 !important;
    }
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    .hover-link:hover {
        text-decoration: underline !important;
        color: var(--neo-primary) !important;
    }
    /* Minimal Gantt Container Styling Only */
    .gantt-container {
        border: 1px solid #ddd;
        background: #fff;
        overflow: auto;
        min-height: 350px;
        cursor: grab;
        user-select: none;
    }
    .gantt-container:active {
        cursor: grabbing;
    }
    #gantt-today-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        display: none;
    }
    
    /* Status Colors - Maximum Specificity */
    svg.gantt g.bar-wrapper.bar-todo rect.bar,
    svg.gantt .bar-wrapper.bar-todo .bar,
    .gantt-container svg g.bar-wrapper.bar-todo rect {
        fill: #6c757d !important; /* Gray - To Do */
    }
    svg.gantt g.bar-wrapper.bar-in_progress rect.bar,
    svg.gantt .bar-wrapper.bar-in_progress .bar,
    .gantt-container svg g.bar-wrapper.bar-in_progress rect {
        fill: #0dcaf0 !important; /* Blue - In Progress */
    }
    svg.gantt g.bar-wrapper.bar-done rect.bar,
    svg.gantt .bar-wrapper.bar-done .bar,
    .gantt-container svg g.bar-wrapper.bar-done rect {
        fill: #198754 !important; /* Green - Done */
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header border-bottom border-3">
                    <h1 class="fw-bold mb-2 h3">{{ $project->title }}</h1>
                    @if($project->group || $project->department)
                        <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                            @if($project->group)
                                <span class="text-primary fw-bold small text-uppercase letter-spacing-1">{{ $project->group }}</span>
                            @endif
                            @if($project->department)
                                <span class="badge bg-light text-secondary border border-1 border-secondary extra-small">
                                    <i class="bi bi-building me-1"></i>{{ $project->department->name }}
                                </span>
                            @endif
                        </div>
                    @endif
                    <span class="badge bg-{{ $project->status ? $project->status->color : 'secondary' }} border border-2 border-dark">
                        {{ $project->status ? $project->status->name : 'No Status' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-muted mb-3 description-content small">
                        {!! $project->description !!}
                    </div>
                    <hr class="border-2 border-dark opacity-100 mb-3">
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="text-uppercase extra-small fw-900 d-block mb-0 text-muted">Project Owner</label>
                            @if(Auth::user()->role === 'admin')
                                <button type="button" class="btn btn-sm btn-link p-0 text-dark" data-bs-toggle="modal" data-bs-target="#editOwnerModal" title="Edit Project Owner">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            @endif
                        </div>
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
                    
                    @if($project->pic)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="text-uppercase extra-small fw-900 d-block mb-0 text-muted">PIC (Person In Charge)</label>
                                @if(Auth::user()->role === 'admin')
                                    <button type="button" class="btn btn-sm btn-link p-0 text-dark" data-bs-toggle="modal" data-bs-target="#editPicModal" title="Edit PIC">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex align-items-center bg-white border border-2 border-dark p-2" style="box-shadow: 3px 3px 0 #000;">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3 border border-2 border-dark" style="width: 36px; height: 36px; font-size: 0.9rem; font-weight: 800; box-shadow: 2px 2px 0 rgba(0,0,0,0.2); background-color: #4D96FF;">
                                    {{ strtoupper(substr($project->pic->name, 0, 1)) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-900 text-dark" style="font-size: 0.95rem; line-height: 1.1;">{{ $project->pic->name }}</span>
                                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Person In Charge</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="text-uppercase extra-small fw-900 d-block mb-0 text-muted">Assigned Team</label>
                            @if(Auth::user()->role === 'admin')
                                <button type="button" class="btn btn-sm btn-link p-0 text-dark" data-bs-toggle="modal" data-bs-target="#editTeamModal" title="Edit Team">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $assignees = $project->tasks->flatMap->assignees->unique('id');
                            @endphp
                            @forelse($assignees as $member)
                                <div class="d-flex align-items-center bg-white border border-2 border-dark py-1 px-2" style="box-shadow: 2px 2px 0 #000; transition: transform 0.1s ease;" onmouseover="this.style.transform='translate(-1px, -1px)'; this.style.boxShadow='3px 3px 0 #000';" onmouseout="this.style.transform='translate(0,0)'; this.style.boxShadow='2px 2px 0 #000';">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 border border-1 border-dark" style="width: 20px; height: 20px; font-size: 0.65rem; font-weight: 800;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $member->name }}</span>
                                </div>
                            @empty
                                <div class="p-2 border border-2 border-dark bg-light w-100 text-center">
                                    <span class="text-muted italic small">No team members assigned.</span>
                                </div>
                            @endforelse
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

                    <div class="mb-3 p-3 border border-2 border-dark bg-light rounded-0" style="box-shadow: 4px 4px 0 #000;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-uppercase extra-small fw-800 text-muted">Project Budget</span>
                            <span class="fw-900 small">Rp{{ number_format($project->budget, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-uppercase extra-small fw-800 text-muted">Actual Cost</span>
                            @php
                                $actualCost = $project->tasks->sum('cost');
                            @endphp
                            <span class="fw-900 small text-{{ $actualCost > $project->budget && $project->budget > 0 ? 'danger' : 'dark' }}">
                                Rp{{ number_format($actualCost, 2) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-dark border-1 pt-1 mt-1">
                            <span class="text-uppercase extra-small fw-900 text-dark">Remaining</span>
                            @php
                                $remaining = $project->budget - $actualCost;
                            @endphp
                            <span class="fw-900 small text-{{ $remaining < 0 ? 'danger' : 'success' }}">
                                Rp{{ number_format($remaining, 2) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-white border-2 border-dark fw-bold text-uppercase" style="box-shadow: 4px 4px 0 #000;">
                            EDIT PROJECT
                        </a>
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
            @php
                $totalTasksCount = $project->tasks->count();
                $doneTasksCount = $project->tasks->where('status', 'done')->count();
                $projectProgress = $totalTasksCount > 0 ? round(($doneTasksCount / $totalTasksCount) * 100) : 0;
                
                $overdueTasksCount = $project->tasks->where('status', '!=', 'done')->filter(function($t) {
                    return $t->due_date && $t->due_date->isPast();
                })->count();
            @endphp
            <div class="card border-3 border-dark rounded-0 mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">Project Health</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-white border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                <div class="extra-small fw-900 text-muted text-uppercase" style="font-size: 0.5rem;">Progress</div>
                                <div class="h5 fw-black mb-0 text-success">{{ $projectProgress }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                <div class="extra-small fw-900 text-muted text-uppercase" style="font-size: 0.5rem;">Overdue</div>
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
                                <div class="h5 fw-black mb-0 text-dark">{{ $department ? $department->members->count() : $assignees->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- POAC Management Pillars --}}
            <div class="card border-3 border-dark rounded-0 mb-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header py-3 px-4 bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">
                        <i class="bi bi-grid-3x3-gap me-2"></i> Management Framework (POAC)
                    </h5>
                    <div class="badge bg-warning text-dark border border-1 border-dark px-3 py-2 text-uppercase fw-900" style="font-size: 0.7rem;">
                        Current Phase: {{ $project->mgmt_phase ?? 'Planning' }}
                    </div>
                </div>
                <div class="card-body p-4 bg-light">
                    <div class="row g-4">
                        @php
                            $phases = [
                                'Planning' => ['icon' => 'bi-journal-check', 'color' => '#FF6B6B'],
                                'Organizing' => ['icon' => 'bi-diagram-3', 'color' => '#4D96FF'],
                                'Actuating' => ['icon' => 'bi-play-circle', 'color' => '#6BCB77'],
                                'Controlling' => ['icon' => 'bi-shield-check', 'color' => '#FFD93D'],
                            ];
                        @endphp
                        @foreach($phases as $name => $info)
                            <div class="col-lg-3 col-md-6">
                                <div class="h-100 bg-white border border-3 border-dark p-3 d-flex flex-column" 
                                     style="box-shadow: 4px 4px 0 #000; position: relative;">
                                    <div style="position: absolute; top: 0; left: 0; width: 6px; height: 100%; background-color: {{ $info['color'] }};"></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-0 d-flex align-items-center justify-content-center text-white border border-2 border-dark" 
                                             style="width: 28px; height: 28px; background-color: {{ $info['color'] }}; box-shadow: 2px 2px 0 #000;">
                                            <i class="bi {{ $info['icon'] }} small"></i>
                                        </div>
                                        <h6 class="fw-900 mb-0 ms-2 text-uppercase" style="font-size: 0.75rem;">{{ $name }}</h6>
                                    </div>
                                    <div class="poac-logs-list mb-3" style="min-height: 120px; max-height: 150px; overflow-y: auto; font-size: 0.7rem;">
                                        @php
                                            $logs = $project->poacLogs->where('phase', $name);
                                        @endphp
                                        @forelse($logs as $log)
                                            <div class="mb-2 pb-1 border-bottom border-dashed">
                                                <a href="javascript:void(0)" 
                                                   class="text-decoration-none text-dark hover-link fw-bold d-block"
                                                   onclick="viewPoacDetail('{{ addslashes($log->title) }}', `{!! addslashes(nl2br(e($log->description))) !!}`, '{{ $log->created_at->format('d/m/Y H:i') }}', '{{ addslashes($log->user ? $log->user->name : 'System') }}')">
                                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                                </a>
                                                <span class="extra-small text-muted">{{ Str::limit($log->title, 30) }}</span>
                                            </div>
                                        @empty
                                            <span class="fst-italic opacity-50 small">No logs recorded.</span>
                                        @endforelse
                                    </div>
                                    <div class="mt-auto pt-2 border-top border-1 border-dark">
                                        <button class="btn btn-xs btn-dark w-100 rounded-0 text-uppercase fw-bold" 
                                                style="font-size: 0.6rem;"
                                                onclick="openMgmtEdit('{{ $name }}')">
                                            + Log Action
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header d-flex justify-content-between align-items-center py-3 px-4 bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h4">TASKS LIST</h5>
                    <button type="button" class="btn btn-primary btn-sm border-2 border-dark" style="box-shadow: 4px 4px 0 #000;" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bi bi-plus-lg me-1"></i> ADD TASK
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="p-4 bg-light border-bottom border-3 border-dark">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-900 text-uppercase mb-0">Project Timeline (Gantt)</h6>
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group border border-2 border-dark" role="group" style="box-shadow: 2px 2px 0 #000;">
                                    <button type="button" class="btn btn-xs btn-white fw-bold py-1 px-2" onclick="zoomGantt(-1)" title="Zoom In"><i class="bi bi-zoom-in"></i></button>
                                    <button type="button" class="btn btn-xs btn-white fw-bold py-1 px-2 border-start border-2 border-dark" onclick="zoomGantt(1)" title="Zoom Out"><i class="bi bi-zoom-out"></i></button>
                                </div>
                                <div class="btn-group border border-2 border-dark" role="group" style="box-shadow: 2px 2px 0 #000;">
                                    <button type="button" class="btn btn-xs btn-white fw-bold py-1 px-2 gantt-view-btn" data-view="Day" onclick="changeGanttView('Day')">Day</button>
                                    <button type="button" class="btn btn-xs btn-white fw-bold py-1 px-2 border-start border-2 border-dark gantt-view-btn" data-view="Week" onclick="changeGanttView('Week')">Week</button>
                                    <button type="button" class="btn btn-xs btn-white fw-bold py-1 px-2 border-start border-2 border-dark gantt-view-btn" data-view="Month" onclick="changeGanttView('Month')">Month</button>
                                </div>
                            </div>
                        </div>
                        <div class="gantt-container position-relative">
                            <button id="gantt-today-btn" class="btn btn-xs btn-warning fw-900 border-2 border-dark text-uppercase">
                                <i class="bi bi-geo-alt-fill me-1"></i> Today
                            </button>
                            <svg id="gantt">
                                <text x="50%" y="50%" text-anchor="middle" font-family="Outfit" font-weight="900" font-size="20">INITIALIZING ENGINE...</text>
                            </svg>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success m-3 border-2 border-dark rounded-0 shadow-none">{{ session('success') }}</div>
                    @endif

                    @if($project->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase small fw-900 border-0" style="width: 25%;">Title</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 10%;">Status</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 10%;">POAC Logs</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 12%;">Assignee</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 10%;">Cost</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 8%;">Start</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 8%;">Due</th>
                                        <th class="text-end pe-4 py-3 text-uppercase small fw-900 border-0" style="width: 12%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->tasks as $task)
                                        <tr class="border-bottom border-1 border-dark">
                                            <td class="ps-4">
                                                <button type="button" class="btn btn-link p-0 fw-bold text-dark text-decoration-none hover-link text-start open-task-details" 
                                                    data-bs-toggle="modal" data-bs-target="#taskDetailsModal"
                                                    data-task-id="{{ $task->id }}">
                                                    {{ $task->title }}
                                                </button>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = $task->status === 'done' ? 'bg-success' : ($task->status === 'in_progress' ? 'bg-info text-dark' : 'bg-secondary');
                                                @endphp
                                                <span class="badge {{ $statusClass }} border border-1 border-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem; box-shadow: 2px 2px 0 #000;">
                                                    {{ str_replace('_', ' ', $task->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $phaseColors = [
                                                        'Planning' => '#FF6B6B',
                                                        'Organizing' => '#4D96FF',
                                                        'Actuating' => '#6BCB77',
                                                        'Controlling' => '#FFD93D',
                                                    ];
                                                    $phaseColor = $phaseColors[$task->mgmt_phase] ?? '#6c757d';
                                                @endphp
                                                <div class="d-flex flex-column gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-dark border-2 rounded-0 px-2 py-1 fw-bold text-uppercase" 
                                                            style="font-size: 0.6rem; box-shadow: 2px 2px 0 #000;"
                                                            onclick="viewTaskPoacHistory({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                                        <i class="bi bi-clock-history me-1"></i> History
                                                    </button>
                                                    <button type="button" class="btn btn-sm border-2 border-dark rounded-0 px-2 py-1 fw-bold text-uppercase text-dark" 
                                                            style="font-size: 0.6rem; background-color: {{ $phaseColor }}; box-shadow: 2px 2px 0 rgba(0,0,0,0.1);"
                                                            onclick="openTaskMgmtEdit({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ $task->mgmt_phase }}')">
                                                        <i class="bi bi-plus-circle me-1"></i> Add Log
                                                    </button>
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
                                            <td>
                                                <span class="fw-bold extra-small text-dark">
                                                    {{ $task->cost > 0 ? 'Rp' . number_format($task->cost, 0) : '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->start_date)
                                                    <span class="extra-small text-muted fw-600">
                                                        {{ $task->start_date->format('M d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted extra-small">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    <span class="extra-small {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger fw-800' : 'text-muted fw-600' }}">
                                                        {{ $task->due_date->format('M d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted extra-small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-light bg-white border border-2 border-dark px-2 py-1 fw-bold edit-task-btn" 
                                                        data-bs-toggle="modal" data-bs-target="#editTaskModal"
                                                        style="box-shadow: 2px 2px 0 #000; font-size: 0.7rem;"
                                                        data-task='@json($task)'>
                                                        EDIT
                                                    </button>
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-light bg-white border border-2 border-dark px-2 py-1 fw-bold text-danger delete-task-btn" style="box-shadow: 2px 2px 0 #000; font-size: 0.7rem;" onclick="return confirm('Delete this task?')">
                                                            DEL
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x display-4 text-muted mb-3 d-block"></i>
                            <p class="text-muted italic">No tasks found. Click "Add Task" to create one.</p>
                        </div>
                    @endif
                </div>
            {{-- TICKETS LIST --}}
            <div class="card border-3 border-dark rounded-0 mt-4" style="box-shadow: 8px 8px 0 #000;">
                <div class="card-header d-flex justify-content-between align-items-center py-3 px-4 bg-white border-bottom border-3 border-dark">
                    <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h4">TICKETS LIST</h5>
                </div>
                <div class="card-body p-0">
                    @if($project->tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase small fw-900 border-0" style="width: 12%;">Ticket No</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 30%;">Title</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 10%;">Type</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 10%;">Status</th>
                                        <th class="py-3 text-uppercase small fw-900 border-0" style="width: 13%;">POAC Logs</th>
                                        <th class="text-end pe-4 py-3 text-uppercase small fw-900 border-0" style="width: 10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->tickets as $ticket)
                                        <tr class="border-bottom border-1 border-dark">
                                            <td class="ps-4 fw-bold text-dark">{{ $ticket->ticket_number }}</td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $ticket->title }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border border-1 border-dark small">{{ $ticket->type }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    // PHP 7 compatible status class assignment
                                                    switch($ticket->status) {
                                                        case 'open':
                                                            $statusClass = 'bg-warning';
                                                            break;
                                                        case 'in_progress':
                                                            $statusClass = 'bg-info';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'bg-success';
                                                            break;
                                                        case 'closed':
                                                            $statusClass = 'bg-secondary';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-secondary';
                                                    }
                                                @endphp
                                                <span class="badge {{ $statusClass }} border border-1 border-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem; box-shadow: 2px 2px 0 #000;">
                                                    {{ str_replace('_', ' ', $ticket->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $phaseColors = [
                                                        'Planning' => '#FF6B6B',
                                                        'Organizing' => '#4D96FF',
                                                        'Actuating' => '#6BCB77',
                                                        'Controlling' => '#FFD93D',
                                                    ];
                                                    $phaseColor = $phaseColors[$ticket->mgmt_phase ?? 'Planning'] ?? '#6c757d';
                                                @endphp
                                                <div class="d-flex flex-column gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-dark border-2 rounded-0 px-2 py-1 fw-bold text-uppercase" 
                                                            style="font-size: 0.6rem; box-shadow: 2px 2px 0 #000;"
                                                            onclick="viewTicketPoacHistory({{ $ticket->id }}, '{{ addslashes($ticket->title) }}')">
                                                        <i class="bi bi-clock-history me-1"></i> History
                                                    </button>
                                                    <button type="button" class="btn btn-sm border-2 border-dark rounded-0 px-2 py-1 fw-bold text-uppercase text-dark" 
                                                            style="font-size: 0.6rem; background-color: {{ $phaseColor }}; box-shadow: 2px 2px 0 rgba(0,0,0,0.1);"
                                                            onclick="openTicketMgmtEdit({{ $ticket->id }}, '{{ addslashes($ticket->title) }}', '{{ $ticket->mgmt_phase ?? 'Planning' }}')">
                                                        <i class="bi bi-plus-circle me-1"></i> Add Log
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-dark rounded-0 px-3 py-1 fw-bold text-uppercase" style="box-shadow: 2px 2px 0 #000; font-size: 0.65rem;">
                                                    VIEW
                                                </a>
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
        </div>
    </div>
</div> {{-- This closes the container --}}

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-900 text-uppercase">Add New Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST" id="addTaskForm">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="mb-3">
                        <label for="add_title" class="form-label text-uppercase small fw-900">Task Title</label>
                        <input type="text" class="form-control border-2 border-dark rounded-0" id="add_title" name="title" required placeholder="What needs to be done?">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-uppercase small fw-900">Description</label>
                        <div id="add-task-editor" style="height: 150px;"></div>
                        <input type="hidden" name="description" id="add_description_input">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_status" class="form-label text-uppercase small fw-900">Status</label>
                            <select class="form-select border-2 border-dark rounded-0" id="add_status" name="status" required>
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_assigned_to" class="form-label text-uppercase small fw-900">Assign To (Multiple)</label>
                            <select class="form-select border-2 border-dark rounded-0" id="add_assigned_to" name="assigned_to[]" multiple style="height: 100px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="add_start_date" class="form-label text-uppercase small fw-900">Start Date</label>
                            <input type="date" class="form-control border-2 border-dark rounded-0" id="add_start_date" name="start_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="add_due_date" class="form-label text-uppercase small fw-900">Due Date</label>
                            <input type="date" class="form-control border-2 border-dark rounded-0" id="add_due_date" name="due_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="add_cost" class="form-label text-uppercase small fw-900">Cost (Rp)</label>
                            <input type="number" step="0.01" class="form-control border-2 border-dark rounded-0" id="add_cost" name="cost" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light bg-white border-2 border-dark px-4" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary border-2 border-dark px-4" style="box-shadow: 4px 4px 0 #000;">CREATE TASK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-900 text-uppercase">Edit Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editTaskForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label text-uppercase small fw-900">Task Title</label>
                        <input type="text" class="form-control border-2 border-dark rounded-0" id="edit_title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-uppercase small fw-900">Description</label>
                        <div id="edit-task-editor" style="height: 150px;"></div>
                        <input type="hidden" name="description" id="edit_description_input">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label text-uppercase small fw-900">Status</label>
                            <select class="form-select border-2 border-dark rounded-0" id="edit_status" name="status" required>
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_assigned_to" class="form-label text-uppercase small fw-900">Assign To (Multiple)</label>
                            <select class="form-select border-2 border-dark rounded-0" id="edit_assigned_to" name="assigned_to[]" multiple style="height: 100px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_start_date" class="form-label text-uppercase small fw-900">Start Date</label>
                            <input type="date" class="form-control border-2 border-dark rounded-0" id="edit_start_date" name="start_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_due_date" class="form-label text-uppercase small fw-900">Due Date</label>
                            <input type="date" class="form-control border-2 border-dark rounded-0" id="edit_due_date" name="due_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_cost" class="form-label text-uppercase small fw-900">Cost (Rp)</label>
                            <input type="number" step="0.01" class="form-control border-2 border-dark rounded-0" id="edit_cost" name="cost">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light bg-white border-2 border-dark px-4" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary border-2 border-dark px-4" style="box-shadow: 4px 4px 0 #000;">UPDATE TASK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-900 text-uppercase" id="details_modal_title">Task Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="details_modal_body">
                <div class="text-center py-5" id="details_loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="details_content" class="d-none">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="fw-bold mb-0" id="view_title"></h3>
                        <span id="view_status_badge" class="badge border border-2 border-dark"></span>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <p class="mb-1 text-uppercase extra-small fw-800 text-muted">Assigned To</p>
                            <div id="view_assignees" class="d-flex flex-wrap gap-2"></div>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-uppercase extra-small fw-800 text-muted">Start Date</p>
                            <p class="fw-bold text-dark" id="view_start_date"></p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-uppercase extra-small fw-800 text-muted">Due Date</p>
                            <p class="fw-bold text-danger" id="view_due_date"></p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-uppercase extra-small fw-800 text-muted">Cost</p>
                            <p class="fw-bold text-dark" id="view_cost"></p>
                        </div>
                    </div>

                    <div class="p-3 bg-light border border-2 border-dark mb-4" style="box-shadow: 4px 4px 0 #000;">
                        <label class="text-uppercase small fw-900 d-block mb-2">Description</label>
                        <div id="view_description" class="ql-editor p-0"></div>
                    </div>

                    <hr class="border-2 border-dark opacity-100 my-4">

                    <div id="comments_section">
                        <h5 class="fw-900 text-uppercase border-bottom border-2 border-dark inline-block mb-3">Comments</h5>
                        <div id="comments_list" class="mb-4"></div>

                        <div class="card border-2 border-dark shadow-none p-0">
                            <div class="card-header border-bottom border-2 border-dark bg-warning py-2">
                                <h6 class="mb-0 fw-bold text-uppercase small">Add a Comment</h6>
                            </div>
                            <div class="card-body p-3">
                                <form id="addCommentForm" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <div id="modal-comment-editor" style="height: 100px;"></div>
                                        <input type="hidden" name="content" id="modal_comment_content">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary btn-sm border-2 border-dark px-3 mt-1" style="box-shadow: 3px 3px 0 #000;">POST COMMENT</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.5.1/snap.svg-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- QUINLL INITIALIZATION ---
        const quillConfig = {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        };

        const addTaskQuill = new Quill('#add-task-editor', quillConfig);
        const editTaskQuill = new Quill('#edit-task-editor', quillConfig);
        const modalCommentQuill = new Quill('#modal-comment-editor', quillConfig);

        // --- ADD TASK ---
        const addTaskModalEl = document.getElementById('addTaskModal');
        const addTaskForm = document.getElementById('addTaskForm');
        
        document.querySelectorAll('[data-bs-target="#addTaskModal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (window.bootstrap) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(addTaskModalEl);
                    bsModal.show();
                }
            });
        });

        addTaskForm.onsubmit = function() {
            document.getElementById('add_description_input').value = addTaskQuill.root.innerHTML;
        };

        // --- EDIT TASK ---
        const editTaskModalEl = document.getElementById('editTaskModal');
        const editTaskForm = document.getElementById('editTaskForm');
        
        document.querySelectorAll('.edit-task-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const task = JSON.parse(this.dataset.task);
                
                editTaskForm.action = `/tasks/${task.id}`;
                document.getElementById('edit_title').value = task.title || '';
                document.getElementById('edit_status').value = task.status || 'todo';
                document.getElementById('edit_start_date').value = task.start_date ? task.start_date.split('T')[0] : '';
                document.getElementById('edit_due_date').value = task.due_date ? task.due_date.split('T')[0] : '';
                document.getElementById('edit_cost').value = task.cost || 0;
                
                // Set multiple assignees
                const assignToSelect = document.getElementById('edit_assigned_to');
                Array.from(assignToSelect.options).forEach(option => {
                    option.selected = task.assignees.some(a => a.id == option.value);
                });

                editTaskQuill.root.innerHTML = task.description || '';

                if (window.bootstrap) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(editTaskModalEl);
                    bsModal.show();
                }
            });
        });

        editTaskForm.onsubmit = function() {
            document.getElementById('edit_description_input').value = editTaskQuill.root.innerHTML;
        };

        // --- TASK DETAILS & COMMENTS ---
        const taskDetailsModalEl = document.getElementById('taskDetailsModal');
        const detailsLoading = document.getElementById('details_loading');
        const detailsContent = document.getElementById('details_content');
        const addCommentForm = document.getElementById('addCommentForm');

        document.querySelectorAll('.open-task-details').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.dataset.taskId;
                
                // Show modal first
                if (window.bootstrap) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(taskDetailsModalEl);
                    bsModal.show();
                }

                // Reset Modal Content
                detailsLoading.classList.remove('d-none');
                detailsContent.classList.add('d-none');
                modalCommentQuill.setContents([]);

                fetch(`/tasks/${taskId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        const task = data.task;
                        
                        document.getElementById('view_title').textContent = task.title;
                        
                        // Render Multiple Assignees
                        const assigneesContainer = document.getElementById('view_assignees');
                        assigneesContainer.innerHTML = '';
                        if (task.assignees && task.assignees.length > 0) {
                            task.assignees.forEach(user => {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-white text-dark border border-1 border-dark extra-small';
                                badge.style.boxShadow = '2px 2px 0 #000';
                                badge.textContent = user.name;
                                assigneesContainer.appendChild(badge);
                            });
                        } else {
                            assigneesContainer.innerHTML = '<span class="text-muted italic small">Unassigned</span>';
                        }

                        document.getElementById('view_start_date').textContent = task.start_date ? new Date(task.start_date).toLocaleDateString() : 'N/A';
                        document.getElementById('view_due_date').textContent = task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A';
                        document.getElementById('view_cost').textContent = task.cost > 0 ? 'Rp' + parseFloat(task.cost).toLocaleString() : '-';
                        document.getElementById('view_description').innerHTML = task.description || '<p class="text-muted italic">No description provided.</p>';
                        
                        // Status Badge
                        const badge = document.getElementById('view_status_badge');
                        badge.textContent = task.status.replace('_', ' ').toUpperCase();
                        badge.className = 'badge border border-2 border-dark px-3 ';
                        if (task.status === 'done') badge.classList.add('bg-success');
                        else if (task.status === 'in_progress') badge.classList.add('bg-info', 'text-dark');
                        else badge.classList.add('bg-secondary');
                        badge.style.boxShadow = '3px 3px 0 #000';

                        // Set action for comment form
                        addCommentForm.action = `/tasks/${task.id}/comments`;

                        // Render Comments
                        const commentsList = document.getElementById('comments_list');
                        commentsList.innerHTML = '';
                        
                        if (task.comments && task.comments.length > 0) {
                            task.comments.forEach(comment => {
                                const card = document.createElement('div');
                                card.className = 'card mb-3 border-2 border-dark shadow-none rounded-0';
                                card.innerHTML = `
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-uppercase extra-small border-bottom border-1 border-dark">${comment.user.name}</span>
                                            <small class="text-muted extra-small">${new Date(comment.created_at).toLocaleString()}</small>
                                        </div>
                                        <div class="ql-editor p-0 small">${comment.content}</div>
                                    </div>
                                `;
                                commentsList.appendChild(card);
                            });
                        } else {
                            commentsList.innerHTML = '<p class="text-muted italic small py-3">No comments yet. Be the first!</p>';
                        }

                        detailsLoading.classList.add('d-none');
                        detailsContent.classList.remove('d-none');
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error loading task details.');
                    });
            });
        });

        addCommentForm.onsubmit = function() {
            const content = modalCommentQuill.root.innerHTML;
            if (modalCommentQuill.getText().trim().length === 0) {
                alert('Please enter a comment.');
                return false;
            }
            document.getElementById('modal_comment_content').value = content;
        };

        @if($project->tasks->count() > 0)
        setTimeout(function() {
            try {
                const ganttTasks = [
                    @foreach($project->tasks as $task)
                    {
                        id: '{{ $task->id }}',
                        name: '{{ addslashes($task->title) }}',
                        start: '{{ ($task->start_date ?? $project->start_date ?? now())->format('Y-m-d') }}',
                        end: '{{ ($task->due_date ?? $task->start_date ?? $project->end_date ?? now()->addDay())->format('Y-m-d') }}',
                        progress: {{ $task->status === 'done' ? 100 : ($task->status === 'in_progress' ? 50 : 0) }},
                        custom_class: 'bar-{{ $task->status }}',
                        assignees: [
                            @foreach($task->assignees as $a)
                            { name: '{{ addslashes($a->name) }}', initial: '{{ strtoupper(substr($a->name, 0, 1)) }}' },
                            @endforeach
                        ]
                    },
                    @endforeach
                ];

                if (typeof Gantt !== 'undefined') {
                    // Declare variables in outer scope for button access
                    const views = ['Day', 'Week', 'Month'];
                    let currentViewIndex = 1; // Start at Week view
                    let gantt = null;

                    try {
                        gantt = new Gantt("#gantt", ganttTasks, {
                            header_height: 50,
                            column_width: 35,
                            step: 24,
                            view_modes: views,
                            bar_height: 36,
                            bar_corner_radius: 3,
                            arrow_curve: 5,
                            padding: 18,
                            view_mode: 'Week',
                            date_format: 'YYYY-MM-DD',
                            on_click: function (task) {
                                const btn = document.querySelector(`.open-task-details[data-task-id="${task.id}"]`);
                                if (btn) btn.click();
                            },
                            on_date_change: function(task, start, end) {
                                updateTaskDates(task.id, start, end);
                            },
                            custom_popup_html: function(task) {
                                const statusLabel = task.custom_class.replace('bar-', '').replace('_', ' ').toUpperCase();
                                const statusColors = {
                                    'bar-todo': '#6c757d',
                                    'bar-in_progress': '#0dcaf0',
                                    'bar-done': '#198754'
                                };
                                const statusColor = statusColors[task.custom_class] || '#999';
                                
                                return `
                                    <div class="p-3 border border-2 border-dark bg-white shadow" style="border-radius: 4px; min-width: 220px;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0 text-uppercase" style="font-size: 12px;">${task.name}</h6>
                                            <span class="badge text-white px-2 py-1" style="font-size: 9px; background: ${statusColor}; border-radius: 3px;">${statusLabel}</span>
                                        </div>
                                        <div class="mb-2">
                                            <div class="progress" style="height: 8px; border-radius: 4px; background: #eee;">
                                                <div class="progress-bar" style="width: ${task.progress}%; background: ${statusColor}; border-radius: 4px;"></div>
                                            </div>
                                            <small class="text-muted" style="font-size: 10px;">${task.progress}% Complete</small>
                                        </div>
                                        <p class="mb-0 text-dark" style="font-size: 11px;">
                                            <i class="bi bi-calendar-event me-1"></i>${new Date(task.start).toLocaleDateString('id-ID', {day:'numeric', month:'short'})} - ${new Date(task.end).toLocaleDateString('id-ID', {day:'numeric', month:'short'})}
                                        </p>
                                    </div>
                                `;
                            }
                        });

                        // --- Scroll to Today (Simplified) ---
                        const todayBtn = document.getElementById('gantt-today-btn');
                        if (todayBtn) {
                            todayBtn.style.display = 'inline-block';
                            
                            todayBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const container = document.querySelector('.gantt-container');
                                if (!container) return;
                                
                                // Simple approach: scroll to beginning (where current tasks usually are)
                                container.scrollTo({
                                    left: 0,
                                    behavior: 'smooth'
                                });
                                
                                // Alternative: scroll to middle
                                // setTimeout(() => {
                                //     const scrollWidth = container.scrollWidth;
                                //     const clientWidth = container.clientWidth;
                                //     container.scrollTo({
                                //         left: (scrollWidth - clientWidth) / 2,
                                //         behavior: 'smooth'
                                //     });
                                // }, 100);
                            });
                        }

                        // Initial scroll to beginning
                        setTimeout(() => {
                            const container = document.querySelector('.gantt-container');
                            if (container) {
                                container.scrollLeft = 0;
                            }
                        }, 400);

                        function updateTaskDates(taskId, start, end) {
                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            
                            fetch(`/tasks/${taskId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    start_date: start,
                                    due_date: end
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Task updated:', data);
                                const row = document.querySelector(`button[data-task-id="${taskId}"]`)?.closest('tr');
                                if (row) {
                                    const startCell = row.cells[4];
                                    const dueCell = row.cells[5];
                                    startCell.innerHTML = `<span class="extra-small text-muted fw-600">${new Date(start).toLocaleDateString('en-US', {month: 'short', day: '2-digit'})}</span>`;
                                    dueCell.innerHTML = `<span class="extra-small text-muted fw-600">${new Date(end).toLocaleDateString('en-US', {month: 'short', day: '2-digit'})}</span>`;
                                }
                            })
                            .catch(error => {
                                console.error('Error updating task:', error);
                            });
                        }

                        // Expose controls to window
                        window.changeGanttView = function(view) {
                            if (gantt && typeof gantt.change_view_mode === 'function') {
                                gantt.change_view_mode(view);
                                currentViewIndex = views.indexOf(view);
                                updateViewButtons(view);
                            }
                        };

                        window.zoomGantt = function(direction) {
                            let newIndex = currentViewIndex + direction;
                            if (newIndex >= 0 && newIndex < views.length) {
                                window.changeGanttView(views[newIndex]);
                            }
                        };

                        function updateViewButtons(activeView) {
                            document.querySelectorAll('.gantt-view-btn').forEach(btn => {
                                if (btn.dataset.view === activeView) {
                                    btn.classList.remove('btn-white');
                                    btn.classList.add('btn-primary');
                                } else {
                                    btn.classList.add('btn-white');
                                    btn.classList.remove('btn-primary');
                                }
                            });
                        }

                        // Set initial view button state
                        updateViewButtons('Week');

                    } catch (error) {
                        console.error('Gantt initialization error:', error);
                        document.getElementById('gantt').innerHTML = '<text x="50%" y="50%" text-anchor="middle" fill="red" font-size="14">Chart failed: ' + error.message + '</text>';
                    }

                    // --- DRAG TO SCROLL (Enhanced for Timeline Navigation) ---
                    const container = document.querySelector('.gantt-container');
                    let isDragging = false;
                    let startX = 0;
                    let scrollLeft = 0;
                    let hasMoved = false;

                    container.addEventListener('mousedown', (e) => {
                        // Check if clicking on actual task bar
                        const isBar = e.target.classList.contains('bar') || 
                                     e.target.closest('.bar-wrapper')?.querySelector('.bar') === e.target;
                        
                        // Allow drag everywhere except when clicking directly on bar for editing
                        if (isBar && e.target.classList.contains('bar')) return;
                        
                        isDragging = true;
                        hasMoved = false;
                        container.style.cursor = 'grabbing';
                        container.style.userSelect = 'none';
                        
                        startX = e.pageX;
                        scrollLeft = container.scrollLeft;
                    });

                    container.addEventListener('mouseleave', () => {
                        isDragging = false;
                        container.style.cursor = 'grab';
                    });

                    container.addEventListener('mouseup', (e) => {
                        isDragging = false;
                        container.style.cursor = 'grab';
                        container.style.userSelect = '';
                    });

                    document.addEventListener('mouseup', () => {
                        if (isDragging) {
                            isDragging = false;
                            container.style.cursor = 'grab';
                        }
                    });

                    container.addEventListener('mousemove', (e) => {
                        if (!isDragging) return;
                        
                        e.preventDefault();
                        hasMoved = true;
                        
                        const x = e.pageX;
                        const walk = (x - startX) * 2.5; // Faster scroll for timeline
                        
                        container.scrollLeft = scrollLeft - walk;
                    });

                    // Prevent click events if dragged
                    container.addEventListener('click', (e) => {
                        if (hasMoved) {
                            e.stopPropagation();
                            e.preventDefault();
                        }
                    }, true);

                } else {
                    document.getElementById('gantt').innerHTML = '<text x="50%" y="50%" text-anchor="middle" fill="red">Gantt library failed to load.</text>';
                }
            } catch (e) {
                console.error('Error initializing Gantt:', e);
                document.getElementById('gantt').innerHTML = '<text x="50%" y="50%" text-anchor="middle" fill="red">Gantt init error.</text>';
            }
        }, 500);
        @endif

        // --- WORKLOAD CHART (Organizing) ---
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
</script>

{{-- POAC Management Edit Modal --}}
<div class="modal fade" id="modalEditMgmt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-3 border-dark rounded-0">
            <div class="modal-header py-3 px-4 bg-dark text-white border-bottom border-3 border-dark">
                <h5 class="modal-title fw-black text-uppercase letter-spacing-1 h6" id="mgmtModalTitle">UPDATE PHASE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.mgmt-update', $project) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="phase_name" id="inputPhaseName">
                    <div class="mb-3">
                        <label class="form-label fw-900 text-uppercase small text-muted">Current Project Phase</label>
                        <select name="mgmt_phase" class="form-select border-2 border-dark rounded-0 shadow-none fw-bold">
                            <option value="Planning" {{ ($project->mgmt_phase ?? 'Planning') == 'Planning' ? 'selected' : '' }}>Planning</option>
                            <option value="Organizing" {{ ($project->mgmt_phase ?? 'Planning') == 'Organizing' ? 'selected' : '' }}>Organizing</option>
                            <option value="Actuating" {{ ($project->mgmt_phase ?? 'Planning') == 'Actuating' ? 'selected' : '' }}>Actuating</option>
                            <option value="Controlling" {{ ($project->mgmt_phase ?? 'Planning') == 'Controlling' ? 'selected' : '' }}>Controlling</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-900 text-uppercase small text-muted">Action Title</label>
                        <input type="text" name="title" id="mgmtTitle" class="form-control border-2 border-dark rounded-0 shadow-none fw-bold" placeholder="E.g. Kick-off Meeting, Resource Allocation...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-900 text-uppercase small text-muted" id="mgmtNotesLabel">Description/Notes</label>
                        <div id="mgmtQuillEditor" class="bg-white border-2 border-dark" style="height: 200px;"></div>
                        <input type="hidden" name="notes" id="mgmtNotesArea">
                    </div>
                </div>
                <div class="modal-footer p-4 border-top border-3 border-dark bg-white">
                    <button type="button" class="btn btn-outline-dark border-2 rounded-0 fw-bold px-4" data-bs-dismiss="modal">CANCEL</button>
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
            <form action="{{ route('projects.task-mgmt-update', $project) }}" method="POST">
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
                    <div class="mb-3">
                        <label class="form-label fw-900 text-uppercase small text-muted">Action Title</label>
                        <input type="text" name="title" id="taskMgmtTitleInput" class="form-control border-2 border-dark rounded-0 shadow-none fw-bold" placeholder="E.g. Task Defined, Code Review Done...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-900 text-uppercase small text-muted">Description/Notes</label>
                        <div id="taskQuillEditor" class="bg-white border-2 border-dark" style="height: 150px;"></div>
                        <input type="hidden" name="mgmt_notes" id="taskMgmtNotes">
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

{{-- Ticket POAC Edit Modal --}}
<div class="modal fade" id="modalEditTicketMgmt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-3 border-dark rounded-0">
            <div class="modal-header py-3 px-4 bg-dark text-white border-bottom border-3 border-dark">
                <h5 class="modal-title fw-black text-uppercase letter-spacing-1 h6" id="ticketMgmtTitle">UPDATE TICKET MANAGEMENT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.ticket-mgmt-update', $project) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="ticket_id" id="inputTicketId">
                    <div class="mb-3">
                        <label class="form-label fw-900 text-uppercase small text-muted">Management Phase</label>
                        <select name="mgmt_phase" id="inputTicketPhase" class="form-select border-2 border-dark rounded-0 shadow-none fw-bold">
                            <option value="Planning">Planning</option>
                            <option value="Organizing">Organizing</option>
                            <option value="Actuating">Actuating</option>
                            <option value="Controlling">Controlling</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-900 text-uppercase small text-muted">Action Title</label>
                        <input type="text" name="title" id="ticketMgmtTitleInput" class="form-control border-2 border-dark rounded-0 shadow-none fw-bold" placeholder="E.g. Requirements Gathered, Testing Complete...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-900 text-uppercase small text-muted">Description/Notes</label>
                        <div id="ticketQuillEditor" class="bg-white border-2 border-dark" style="height: 150px;"></div>
                        <input type="hidden" name="mgmt_notes" id="ticketMgmtNotes">
                    </div>
                </div>
                <div class="modal-footer p-4 border-top border-3 border-dark bg-white">
                    <button type="button" class="btn btn-outline-dark border-2 rounded-0 fw-bold px-4" data-bs-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn btn-primary border-2 border-dark rounded-0 fw-bold px-4 shadow-btn">SAVE TICKET</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- POAC History Modal --}}
<div class="modal fade" id="modalPoacHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-3 border-dark rounded-0">
            <div class="modal-header py-3 px-4 bg-dark text-white border-bottom border-3 border-dark">
                <h5 class="modal-title fw-black text-uppercase letter-spacing-1 h6" id="poacHistoryTitle">POAC LOGS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-3" id="poacHistoryContent">
                    <!-- POAC history columns will be loaded here -->
                </div>
            </div>
            <div class="modal-footer p-3 border-top border-3 border-dark bg-white">
                <button type="button" class="btn btn-dark border-2 rounded-0 fw-bold px-4" data-bs-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

{{-- POAC Log View Modal --}}
<div class="modal fade" id="modalViewPoac" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-3 border-dark rounded-0">
            <div class="modal-header py-3 px-4 bg-white border-bottom border-3 border-dark">
                <h5 class="modal-title fw-black text-uppercase letter-spacing-1 h6" id="viewPoacTitle">ACTION DETAIL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <span class="badge bg-dark rounded-0 extra-small" id="viewPoacDate">DATE</span>
                </div>
                <div id="viewPoacDescription" class="bg-white border border-2 border-dark p-3 small" style="box-shadow: 4px 4px 0 rgba(0,0,0,0.1); white-space: pre-wrap;">
                </div>
            </div>
            <div class="modal-footer p-3 border-top border-3 border-dark bg-white">
                <button type="button" class="btn btn-dark border-2 rounded-0 fw-bold px-4" data-bs-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<script>
    let mgmtQuill, taskQuill, ticketQuill;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Project MGMT Quill
        if (document.getElementById('mgmtQuillEditor')) {
            mgmtQuill = new Quill('#mgmtQuillEditor', {
                theme: 'snow',
                placeholder: 'Enter details for this management action...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
        }

        // Initialize Task MGMT Quill
        if (document.getElementById('taskQuillEditor')) {
            taskQuill = new Quill('#taskQuillEditor', {
                theme: 'snow',
                placeholder: 'Enter notes about this task\'s management...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
        }

        // Initialize Ticket MGMT Quill
        if (document.getElementById('ticketQuillEditor')) {
            ticketQuill = new Quill('#ticketQuillEditor', {
                theme: 'snow',
                placeholder: 'Enter notes about this ticket\'s management...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
        }

        // Sync Quill to hidden input on form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                if (mgmtQuill && this.contains(document.getElementById('mgmtQuillEditor'))) {
                    document.getElementById('mgmtNotesArea').value = mgmtQuill.root.innerHTML;
                }
                if (taskQuill && this.contains(document.getElementById('taskQuillEditor'))) {
                    document.getElementById('taskMgmtNotes').value = taskQuill.root.innerHTML;
                }
                if (ticketQuill && this.contains(document.getElementById('ticketQuillEditor'))) {
                    document.getElementById('ticketMgmtNotes').value = ticketQuill.root.innerHTML;
                }
            });
        });
    });

    function viewPoacDetail(title, description, date, user) {
        document.getElementById('viewPoacTitle').innerText = title;
        document.getElementById('viewPoacDate').innerText = date + ' | By: ' + user;
        document.getElementById('viewPoacDescription').innerHTML = description;
        var modal = new bootstrap.Modal(document.getElementById('modalViewPoac'));
        modal.show();
    }

    function openMgmtEdit(phase) {
        document.getElementById('inputPhaseName').value = phase;
        document.getElementById('mgmtModalTitle').innerText = 'UPDATE: ' + phase.toUpperCase();
        document.getElementById('mgmtNotesLabel').innerText = phase + ' Description';
        // Clear previous values
        document.getElementById('mgmtTitle').value = '';
        if (mgmtQuill) mgmtQuill.setContents([]);
        
        var modal = new bootstrap.Modal(document.getElementById('modalEditMgmt'));
        modal.show();
    }

    function openTaskMgmtEdit(taskId, taskTitle, phase) {
        document.getElementById('inputTaskId').value = taskId;
        document.getElementById('taskMgmtTitle').innerText = 'TASK MGMT: ' + taskTitle;
        document.getElementById('inputTaskPhase').value = phase;
        document.getElementById('taskMgmtTitleInput').value = '';
        if (taskQuill) taskQuill.setContents([]);

        var modal = new bootstrap.Modal(document.getElementById('modalEditTaskMgmt'));
        modal.show();
    }

    function openTicketMgmtEdit(ticketId, ticketTitle, phase) {
        document.getElementById('inputTicketId').value = ticketId;
        document.getElementById('ticketMgmtTitle').innerText = 'TICKET MGMT: ' + ticketTitle;
        document.getElementById('inputTicketPhase').value = phase;
        document.getElementById('ticketMgmtTitleInput').value = '';
        if (ticketQuill) ticketQuill.setContents([]);

        var modal = new bootstrap.Modal(document.getElementById('modalEditTicketMgmt'));
        modal.show();
    }


    function viewTaskPoacHistory(taskId, taskTitle) {
        document.getElementById('poacHistoryTitle').innerText = 'POAC LOGS: ' + taskTitle;
        
        // Fetch POAC logs for this task
        fetch(`/tasks/${taskId}/poac-logs`)
            .then(response => response.json())
            .then(data => {
                const historyContent = document.getElementById('poacHistoryContent');
                
                const phaseConfig = {
                    'Planning': { color: '#FF6B6B', icon: 'bi-clipboard-check', borderColor: '#dc3545' },
                    'Organizing': { color: '#4D96FF', icon: 'bi-diagram-3', borderColor: '#0d6efd' },
                    'Actuating': { color: '#6BCB77', icon: 'bi-play-circle', borderColor: '#198754' },
                    'Controlling': { color: '#FFD93D', icon: 'bi-speedometer2', borderColor: '#ffc107' }
                };
                
                if (data.logs && data.logs.length > 0) {
                    // Group logs by phase
                    const logsByPhase = {
                        'Planning': [],
                        'Organizing': [],
                        'Actuating': [],
                        'Controlling': []
                    };
                    
                    data.logs.forEach(log => {
                        if (logsByPhase[log.phase]) {
                            logsByPhase[log.phase].push(log);
                        }
                    });
                    
                    let html = '';
                    
                    // Create a column for each phase
                    Object.keys(phaseConfig).forEach(phase => {
                        const config = phaseConfig[phase];
                        const logs = logsByPhase[phase];
                        
                        html += `
                            <div class="col-md-3">
                                <div class="card border-3 border-dark rounded-0 h-100" style="box-shadow: 4px 4px 0 #000;">
                                    <div class="card-header text-white border-bottom border-3 border-dark py-2 px-3" style="background-color: ${config.color};">
                                        <div class="d-flex align-items-center">
                                            <i class="bi ${config.icon} me-2"></i>
                                            <span class="fw-bold text-uppercase small">${phase}</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-2 bg-white" style="max-height: 400px; overflow-y: auto;">
                        `;
                        
                        if (logs.length > 0) {
                            logs.forEach(log => {
                                html += `
                                    <div class="border border-2 border-dark rounded-0 p-2 mb-2 bg-light" style="box-shadow: 2px 2px 0 rgba(0,0,0,0.1);">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">${log.created_at}</small>
                                            <i class="bi bi-caret-up-fill text-muted" style="font-size: 0.6rem;"></i>
                                        </div>
                                        <div class="small fw-bold text-dark mb-1" style="font-size: 0.75rem;">${log.title}</div>
                                        <div class="extra-small text-muted" style="font-size: 0.7rem;">${log.description ? log.description.substring(0, 50) + '...' : ''}</div>
                                    </div>
                                `;
                            });
                        } else {
                            html += `
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    <small>No logs</small>
                                </div>
                            `;
                        }
                        
                        html += `
                                    </div>
                                    <div class="card-footer border-top border-3 border-dark bg-dark p-2">
                                        <button type="button" class="btn btn-sm btn-light w-100 border-2 border-dark rounded-0 fw-bold text-uppercase" 
                                                style="font-size: 0.65rem; box-shadow: 2px 2px 0 rgba(0,0,0,0.3);"
                                                onclick="closeHistoryAndOpenEdit('task', ${taskId}, '${taskTitle.replace(/'/g, "\\'")}',' ${phase}')">
                                            + LOG ACTION
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    historyContent.innerHTML = html;
                } else {
                    historyContent.innerHTML = '<div class="col-12 text-center py-4 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i><p>No POAC logs found for this task.</p></div>';
                }
                
                var modal = new bootstrap.Modal(document.getElementById('modalPoacHistory'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching POAC logs:', error);
                document.getElementById('poacHistoryContent').innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading POAC logs.</div></div>';
            });
    }

    function viewTicketPoacHistory(ticketId, ticketTitle) {
        document.getElementById('poacHistoryTitle').innerText = 'POAC LOGS: ' + ticketTitle;
        
        // Fetch POAC logs for this ticket
        fetch(`/tickets/${ticketId}/poac-logs`)
            .then(response => response.json())
            .then(data => {
                const historyContent = document.getElementById('poacHistoryContent');
                
                const phaseConfig = {
                    'Planning': { color: '#FF6B6B', icon: 'bi-clipboard-check', borderColor: '#dc3545' },
                    'Organizing': { color: '#4D96FF', icon: 'bi-diagram-3', borderColor: '#0d6efd' },
                    'Actuating': { color: '#6BCB77', icon: 'bi-play-circle', borderColor: '#198754' },
                    'Controlling': { color: '#FFD93D', icon: 'bi-speedometer2', borderColor: '#ffc107' }
                };
                
                if (data.logs && data.logs.length > 0) {
                    // Group logs by phase
                    const logsByPhase = {
                        'Planning': [],
                        'Organizing': [],
                        'Actuating': [],
                        'Controlling': []
                    };
                    
                    data.logs.forEach(log => {
                        if (logsByPhase[log.phase]) {
                            logsByPhase[log.phase].push(log);
                        }
                    });
                    
                    let html = '';
                    
                    // Create a column for each phase
                    Object.keys(phaseConfig).forEach(phase => {
                        const config = phaseConfig[phase];
                        const logs = logsByPhase[phase];
                        
                        html += `
                            <div class="col-md-3">
                                <div class="card border-3 border-dark rounded-0 h-100" style="box-shadow: 4px 4px 0 #000;">
                                    <div class="card-header text-white border-bottom border-3 border-dark py-2 px-3" style="background-color: ${config.color};">
                                        <div class="d-flex align-items-center">
                                            <i class="bi ${config.icon} me-2"></i>
                                            <span class="fw-bold text-uppercase small">${phase}</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-2 bg-white" style="max-height: 400px; overflow-y: auto;">
                        `;
                        
                        if (logs.length > 0) {
                            logs.forEach(log => {
                                html += `
                                    <div class="border border-2 border-dark rounded-0 p-2 mb-2 bg-light" style="box-shadow: 2px 2px 0 rgba(0,0,0,0.1);">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">${log.created_at}</small>
                                            <i class="bi bi-caret-up-fill text-muted" style="font-size: 0.6rem;"></i>
                                        </div>
                                        <div class="small fw-bold text-dark mb-1" style="font-size: 0.75rem;">${log.title}</div>
                                        <div class="extra-small text-muted" style="font-size: 0.7rem;">${log.description ? log.description.substring(0, 50) + '...' : ''}</div>
                                    </div>
                                `;
                            });
                        } else {
                            html += `
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    <small>No logs</small>
                                </div>
                            `;
                        }
                        
                        html += `
                                    </div>
                                    <div class="card-footer border-top border-3 border-dark bg-dark p-2">
                                        <button type="button" class="btn btn-sm btn-light w-100 border-2 border-dark rounded-0 fw-bold text-uppercase" 
                                                style="font-size: 0.65rem; box-shadow: 2px 2px 0 rgba(0,0,0,0.3);"
                                                onclick="closeHistoryAndOpenEdit('ticket', ${ticketId}, '${ticketTitle.replace(/'/g, "\\'")}',' ${phase}')">
                                            + LOG ACTION
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    historyContent.innerHTML = html;
                } else {
                    historyContent.innerHTML = '<div class="col-12 text-center py-4 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i><p>No POAC logs found for this ticket.</p></div>';
                }
                
                var modal = new bootstrap.Modal(document.getElementById('modalPoacHistory'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching POAC logs:', error);
                document.getElementById('poacHistoryContent').innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading POAC logs.</div></div>';
            });
    }

    function closeHistoryAndOpenEdit(type, id, title, phase) {
        // Close the history modal first
        var historyModal = bootstrap.Modal.getInstance(document.getElementById('modalPoacHistory'));
        if (historyModal) {
            historyModal.hide();
        }
        
        // Wait for modal to close, then open edit modal
        setTimeout(function() {
            if (type === 'task') {
                openTaskMgmtEdit(id, title, phase);
            } else if (type === 'ticket') {
                openTicketMgmtEdit(id, title, phase);
            }
        }, 300);
    }
</script>

<!-- Edit Project Owner Modal -->
<div class="modal fade" id="editOwnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-900 text-uppercase">Edit Project Owner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-bold">Project Owner</label>
                        <select id="user_id" class="form-select" name="user_id" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $project->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Owner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit PIC Modal -->
<div class="modal fade" id="editPicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-900 text-uppercase">Edit PIC (Person In Charge)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pic_id_modal" class="form-label fw-bold">PIC</label>
                        <select id="pic_id_modal" class="form-select" name="pic_id">
                            <option value="">None</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $project->pic_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update PIC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Assigned Team Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-900 text-uppercase">Edit Assigned Team</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> Team members are assigned through individual tasks. To modify the team, please edit the task assignments.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Team Members</label>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $assignees = $project->tasks->flatMap->assignees->unique('id');
                        @endphp
                        @forelse($assignees as $member)
                            <div class="d-flex align-items-center bg-light border border-2 border-dark py-1 px-2" style="box-shadow: 2px 2px 0 #000;">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 border border-1 border-dark" style="width: 24px; height: 24px; font-size: 0.7rem; font-weight: 800;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <span class="fw-bold text-dark small">{{ $member->name }}</span>
                            </div>
                        @empty
                            <span class="text-muted">No team members assigned yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('projects.show', $project) }}#tasks" class="btn btn-primary" data-bs-dismiss="modal">Manage Tasks</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Select2 on modal user dropdowns - wait for full page load
    window.addEventListener('load', function() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            // Re-initialize when modals are shown
            jQuery('#editOwnerModal, #editPicModal').on('shown.bs.modal', function() {
                jQuery(this).find('select').select2({
                    placeholder: 'Search and select user...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: jQuery(this).find('.modal-content')
                });
            });
            
            // Destroy Select2 when modal is hidden to prevent duplicates
            jQuery('#editOwnerModal, #editPicModal').on('hidden.bs.modal', function() {
                jQuery(this).find('select').select2('destroy');
            });
        } else {
            console.error('Select2 or jQuery not loaded for modals');
        }
    });
</script>

@endpush
