@extends('layouts.app')

@section('content')
<div class="container" style="background-color: #ffffff !important;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Projects</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">New Project</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('projects.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label small fw-bold text-muted">Search Title</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search projects..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="status_id" class="form-label small fw-bold text-muted">Status</label>
                    <select name="status_id" id="status_id" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="group" class="form-label small fw-bold text-muted">Group</label>
                    <select name="group" id="group" class="form-select">
                        <option value="">All Groups</option>
                        @foreach($groups as $groupName)
                            <option value="{{ $groupName }}" {{ request('group') == $groupName ? 'selected' : '' }}>
                                {{ $groupName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="limit" class="form-label small fw-bold text-muted">Show</label>
                    <select name="limit" id="limit" class="form-select">
                        @foreach([12, 24, 36, 48] as $limit)
                            <option value="{{ $limit }}" {{ request('limit', 12) == $limit ? 'selected' : '' }}>
                                {{ $limit }} Entries
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($projects as $project)
            <div class="col-md-4 mb-4">
                @php
                    $colorMap = [
                        'blue' => ['border' => '#0d6efd', 'bg' => 'rgba(13, 110, 253, 0.05)'],
                        'green' => ['border' => '#198754', 'bg' => 'rgba(25, 135, 84, 0.05)'],
                        'yellow' => ['border' => '#ffc107', 'bg' => 'rgba(255, 193, 7, 0.05)'],
                        'orange' => ['border' => '#FF9800', 'bg' => 'rgba(255, 152, 0, 0.05)'],
                        'pink' => ['border' => '#dc3545', 'bg' => 'rgba(220, 53, 69, 0.05)'],
                        'purple' => ['border' => '#9C27B0', 'bg' => 'rgba(156, 39, 176, 0.05)']
                    ];
                    $projectColors = $colorMap[$project->color ?? 'blue'] ?? $colorMap['blue'];
                @endphp
                <div class="card h-100 position-relative" style="border-left: 5px solid {{ $projectColors['border'] }}; background-color: {{ $projectColors['bg'] }};">
                    <div class="card-body">
                         <div class="d-flex justify-content-between align-items-start mb-2">
                              <div style="flex: 1; min-width: 0;">
                                  <div class="d-flex align-items-center gap-2 mb-1">
                                      <h5 class="card-title text-truncate mb-0" title="{{ $project->title }}">{{ $project->title }}</h5>
                                      @php
                                          $priorityColors = [
                                              1 => 'danger',   // P1 - Urgent (Red)
                                              2 => 'warning',  // P2 - High (Orange-ish)
                                              3 => 'info',     // P3 - Medium (Blue)
                                              4 => 'primary',  // P4 - Low (Blue)
                                              5 => 'secondary' // P5 - Very Low (Gray)
                                          ];
                                          $priorityLabel = "P" . $project->priority;
                                      @endphp
                                      <span class="badge bg-{{ $priorityColors[$project->priority] ?? 'secondary' }} rounded-pill" style="font-size: 0.7rem;">
                                          {{ $priorityLabel }}
                                      </span>
                                      
                                      <form action="{{ route('projects.toggle-pin', $project) }}" method="POST" class="d-inline">
                                          @csrf
                                          <button type="submit" class="btn btn-link p-0 border-0 align-baseline" title="{{ $project->is_pinned ? 'Unpin Project' : 'Pin Project' }}">
                                              @if($project->is_pinned)
                                                  <i class="bi bi-pin-angle-fill text-danger"></i>
                                              @else
                                                  <i class="bi bi-pin-angle text-muted" style="opacity: 0.3;"></i>
                                              @endif
                                          </button>
                                      </form>
                                  </div>
                              </div>
                      <span class="badge bg-{{ $project->status ? $project->status->color : 'secondary' }}">
                         {{ $project->status ? $project->status->name : 'No Status' }}
                     </span>
                 </div>
                         
                         @if($project->department)
                             <div class="my-1">
                                 <span class="fw-bold text-muted extra-small">
                                     <i class="bi bi-building me-1"></i>{{ $project->department->name }}
                                 </span>
                             </div>
                         @endif

                         <p class="card-text text-muted extra-small mb-2">Since {{ $project->created_at ? $project->created_at->format('M d, Y') : 'N/A' }}</p>

                         @if($project->group)
                             <p class="card-text mb-1"><small class="text-primary fw-bold">{{ $project->group }}</small></p>
                         @endif
                        <p class="card-text">{{ Str::limit(strip_tags($project->description), 100) }}</p>
                        
                        <div class="mt-2 pt-2 border-top border-1 border-dark-subtle">
                            @php
                                $actualCost = $project->tasks->sum('cost');
                            @endphp
                            <div class="d-flex justify-content-between extra-small fw-bold">
                                <span class="text-muted">BUDGET:</span>
                                <span>Rp{{ number_format($project->budget, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between extra-small fw-bold">
                                <span class="text-muted">ACTUAL:</span>
                                <span class="text-{{ $actualCost > $project->budget && $project->budget > 0 ? 'danger' : 'dark' }}">
                                    Rp{{ number_format($actualCost, 0) }}
                                </span>
                            </div>
                        </div>
                        
                        @if($project->pic)
                            <div class="mt-2 pt-2 border-top border-1 border-dark-subtle">
                                <p class="text-uppercase extra-small fw-900 mb-2 text-muted" style="letter-spacing: 1px;">PIC</p>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 border-dark me-2" 
                                         style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: 800; background-color: #4D96FF !important; color: white !important;">
                                        {{ strtoupper(substr($project->pic->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold small">{{ $project->pic->name }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <p class="text-uppercase extra-small fw-900 mb-2 text-muted" style="letter-spacing: 1px;">Assignees</p>
                            <div class="d-flex align-items-center flex-wrap gap-1">
                                @php
                                    $team = $project->tasks->flatMap->assignees->unique('id');
                                @endphp
                                @forelse($team as $member)
                                    <div class="rounded-circle d-flex align-items-center justify-content-center border border-1 border-dark shadow-sm" 
                                         style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: 800; margin-right: -10px; z-index: {{ 10 - $loop->index }}; background-color: #ff3131 !important; color: white !important;" 
                                         title="{{ $member->name }}">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                @empty
                                    <span class="text-muted extra-small italic">None</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-column gap-2 bg-light border-top border-2 border-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary fw-bold border-2" style="box-shadow: 2px 2px 0 #000;">VIEW DETAILS</a>
                            
                            <button type="button" class="btn btn-sm btn-warning fw-bold border-2 highlight-btn" 
                                    style="box-shadow: 2px 2px 0 #000;"
                                    data-project-data="{{ json_encode([
                                        'title' => $project->title,
                                        'description' => strip_tags($project->description),
                                        'status' => $project->status ? $project->status->name : 'N/A',
                                        'group' => $project->group ?? 'General',
                                        'total_tasks' => $project->tasks->count(),
                                        'done_tasks' => $project->tasks->where('status', 'done')->count(),
                                        'pending_tasks' => $project->tasks->where('status', '!=', 'done')->values()->map->only(['title', 'status'])->toArray(),
                                        'team' => $project->tasks->flatMap->assignees->unique('id')->values()->map->only(['name'])->toArray(),
                                        'budget' => $project->budget,
                                        'actual_cost' => $actualCost
                                    ]) }}">
                                <i class="bi bi-stars me-1"></i> HIGHLIGHT
                            </button>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-2 border-top border-1 border-dark-subtle">
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-light border border-1 border-dark px-2 py-0 fw-bold" style="font-size: 0.65rem;">EDIT</a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger border border-1 border-dark px-2 py-0 fw-bold" style="font-size: 0.65rem;" onclick="return confirm('Are you sure?')">DELETE</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No projects found. Create one to get started!</div>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $projects->links() }}
    </div>
</div>

<!-- Project Highlight Modal -->
<div class="modal fade" id="projectHighlightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-3 border-dark rounded-0" style="box-shadow: 15px 15px 0 #000;">
            <div class="modal-header border-bottom border-3 border-dark bg-warning">
                <h5 class="modal-title fw-900 text-uppercase letter-spacing-1"><i class="bi bi-stars"></i> PROJECT HIGHLIGHT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="highlightCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <!-- Slide 1: Overview -->
                        <div class="carousel-item active p-5">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <span id="hl_group" class="badge bg-danger border border-2 border-dark text-white mb-3 px-3 py-2 fw-900" style="box-shadow: 4px 4px 0 #000;">GROUP</span>
                                    <h1 id="hl_title" class="display-4 fw-900 mb-4 text-uppercase">PROJECT TITLE</h1>
                                    <p id="hl_description" class="lead fw-bold text-muted lh-base">Project description goes here...</p>
                                </div>
                                <div class="col-md-5 text-center">
                                    <div class="p-4 bg-white border border-3 border-dark d-inline-block" style="box-shadow: 8px 8px 0 #000;">
                                        <p class="mb-1 text-uppercase extra-small fw-900">Current Status</p>
                                        <h3 id="hl_status" class="mb-0 fw-900 text-uppercase">PENDING</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Progress Metrics -->
                        <div class="carousel-item p-5 bg-light">
                            <div class="text-center mb-5">
                                <h2 class="fw-900 text-uppercase letter-spacing-1">PERFORMANCE PROGRESS</h2>
                                <div class="mx-auto border-bottom border-4 border-dark" style="width: 100px;"></div>
                            </div>
                            <div class="row text-center">
                                <div class="col-md-6 mb-4">
                                    <div class="position-relative d-inline-block">
                                        <svg width="180" height="180" viewBox="0 0 180 180">
                                            <circle cx="90" cy="90" r="70" fill="none" stroke="#ddd" stroke-width="20" />
                                            <circle id="hl_progress_circle" cx="90" cy="90" r="70" fill="none" stroke="var(--neo-success)" stroke-width="20" 
                                                stroke-dasharray="440" stroke-dashoffset="440" transform="rotate(-90 90 90)" style="transition: stroke-dashoffset 1s ease-out;" />
                                        </svg>
                                        <div class="position-absolute top-50 start-50 translate-middle">
                                            <h1 id="hl_percentage" class="fw-900 mb-0" style="font-size: 3rem;">0%</h1>
                                        </div>
                                    </div>
                                    <p class="mt-3 fw-900 text-uppercase">Completion Rate</p>
                                </div>
                                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                    <div class="card border-3 border-dark p-3 mb-3 w-75 bg-white" style="box-shadow: 6px 6px 0 #000;">
                                        <h4 class="mb-0 fw-900"><span id="hl_done_tasks">0</span> / <span id="hl_total_tasks">0</span></h4>
                                        <p class="mb-0 text-muted extra-small fw-bold text-uppercase">Tasks Completed</p>
                                    </div>
                                    <div class="card border-3 border-dark p-3 w-75 bg-white" style="box-shadow: 6px 6px 0 #000;">
                                        <h4 id="hl_pending_count" class="mb-0 fw-900 text-danger">0</h4>
                                        <p class="mb-0 text-muted extra-small fw-bold text-uppercase">Remaining Tasks</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: Roadmap (Pending Tasks) -->
                        <div class="carousel-item p-5">
                            <h2 class="fw-900 text-uppercase mb-4"><i class="bi bi-list-task"></i> REMAINING ROADMAP</h2>
                            <div id="hl_pending_list" class="list-group border-3 border-dark rounded-0 overflow-auto" style="max-height: 300px; box-shadow: 8px 8px 0 #000;">
                                <!-- Tasks will be injected here -->
                            </div>
                        </div>

                        <!-- Slide 4: Team Presentation -->
                        <div class="carousel-item p-5 bg-info text-white">
                            <h2 class="fw-900 text-uppercase mb-5 text-center" style="text-shadow: 3px 3px 0 #000;">PROJECT SQUAD</h2>
                            <div id="hl_team_list" class="d-flex flex-wrap justify-content-center gap-4">
                                <!-- Team members will be injected here -->
                            </div>
                        </div>

                        <!-- Slide 5: Financial Overview -->
                        <div class="carousel-item p-5 bg-dark text-white">
                            <h2 class="fw-900 text-uppercase mb-5 text-center text-warning" style="text-shadow: 2px 2px 0 #000;">FINANCIAL REPORT</h2>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-4 border border-3 border-warning rounded-0 bg-dark" style="box-shadow: 6px 6px 0 var(--bs-warning);">
                                        <p class="text-uppercase extra-small fw-800 text-muted mb-1">Project Budget</p>
                                        <h2 id="hl_budget" class="fw-900 mb-0">Rp0</h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-4 border border-3 border-light rounded-0 bg-dark" style="box-shadow: 6px 6px 0 #fff;">
                                        <p class="text-uppercase extra-small fw-800 text-muted mb-1">Actual Expenditure</p>
                                        <h2 id="hl_actual" class="fw-900 mb-0">Rp0</h2>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-4 border border-3 border-info rounded-0 bg-dark" style="box-shadow: 6px 6px 0 var(--bs-info);">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <p class="text-uppercase extra-small fw-800 text-muted mb-0">Remaining Funds</p>
                                            <span id="hl_budget_status" class="badge bg-success border border-1 border-white extra-small">ON TRACK</span>
                                        </div>
                                        <h1 id="hl_remaining" class="fw-900 mb-0 text-info">Rp0</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#highlightCarousel" data-bs-slide="prev">
                        <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center border border-2 border-white" style="width: 40px; height: 40px;">
                            <i class="bi bi-chevron-left text-white"></i>
                        </span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#highlightCarousel" data-bs-slide="next">
                        <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center border border-2 border-white" style="width: 40px; height: 40px;">
                            <i class="bi bi-chevron-right text-white"></i>
                        </span>
                    </button>

                    <!-- Indicators -->
                    <div class="carousel-indicators mb-0 pb-3" style="position: relative;">
                        <button type="button" data-bs-target="#highlightCarousel" data-bs-slide-to="0" class="active bg-dark" aria-current="true"></button>
                        <button type="button" data-bs-target="#highlightCarousel" data-bs-slide-to="1" class="bg-dark"></button>
                        <button type="button" data-bs-target="#highlightCarousel" data-bs-slide-to="2" class="bg-dark"></button>
                        <button type="button" data-bs-target="#highlightCarousel" data-bs-slide-to="3" class="bg-dark"></button>
                        <button type="button" data-bs-target="#highlightCarousel" data-bs-slide-to="4" class="bg-dark"></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-3 border-dark bg-light px-5 py-3 d-flex justify-content-between">
                <p class="mb-0 extra-small fw-800 text-muted text-uppercase">Confidential Executive Report</p>
                <button type="button" class="btn btn-dark fw-900 rounded-0 px-4" data-bs-dismiss="modal">CLOSE REPORT</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .carousel-item { min-height: 450px; }
    .letter-spacing-1 { letter-spacing: 1.5px; }
    .fw-900 { font-weight: 900; }
    .fw-800 { font-weight: 800; }
    .extra-small { font-size: 0.7rem; }
    #projectHighlightModal .modal-content {
        transition: transform 0.3s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const highlightBtns = document.querySelectorAll('.highlight-btn');
        const modal = document.getElementById('projectHighlightModal');
        const carouselEl = document.getElementById('highlightCarousel');
        const carousel = new bootstrap.Carousel(carouselEl, {
            interval: false,
            touch: true
        });

        highlightBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.projectData);
                
                // Slide 1
                document.getElementById('hl_title').textContent = data.title;
                document.getElementById('hl_description').textContent = data.description || 'No description provided.';
                document.getElementById('hl_status').textContent = data.status;
                document.getElementById('hl_group').textContent = data.group;

                // Slide 2: Progress
                const total = data.total_tasks;
                const done = data.done_tasks;
                const pending = total - done;
                const percentage = total > 0 ? Math.round((done / total) * 100) : 0;
                
                document.getElementById('hl_percentage').textContent = percentage + '%';
                document.getElementById('hl_total_tasks').textContent = total;
                document.getElementById('hl_done_tasks').textContent = done;
                document.getElementById('hl_pending_count').textContent = pending;

                // Circle animation
                const circle = document.getElementById('hl_progress_circle');
                const circumference = 2 * Math.PI * 70;
                const offset = circumference - (percentage / 100 * circumference);
                circle.style.strokeDasharray = circumference;
                circle.style.strokeDashoffset = circumference; // jump to start
                setTimeout(() => {
                    circle.style.strokeDashoffset = offset;
                }, 100);

                // Slide 3: Pending Roadmap
                const listContainer = document.getElementById('hl_pending_list');
                listContainer.innerHTML = '';
                if (data.pending_tasks.length > 0) {
                    data.pending_tasks.forEach(task => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item border-start-0 border-end-0 border-dark py-3 fw-bold';
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-circle me-3"></i> ${task.title}</span>
                                <span class="badge bg-light text-dark border border-1 border-dark extra-small">${task.status.replace('_', ' ')}</span>
                            </div>
                        `;
                        listContainer.appendChild(item);
                    });
                } else {
                    listContainer.innerHTML = '<div class="p-5 text-center text-muted italic">All tasks are completed! Great job.</div>';
                }

                // Slide 4: Team
                const teamContainer = document.getElementById('hl_team_list');
                teamContainer.innerHTML = '';
                if (data.team.length > 0) {
                    data.team.forEach(member => {
                        const div = document.createElement('div');
                        div.className = 'text-center';
                        div.innerHTML = `
                            <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center border border-3 border-dark mx-auto mb-2" 
                                 style="width: 80px; height: 80px; box-shadow: 4px 4px 0 #000; font-size: 1.5rem; font-weight: 900;">
                                ${member.name.charAt(0).toUpperCase()}
                            </div>
                            <p class="mb-0 fw-900 text-uppercase" style="text-shadow: 2px 2px 0 #000;">${member.name}</p>
                            <small class="extra-small fw-bold opacity-75">Contributor</small>
                        `;
                        teamContainer.appendChild(div);
                    });
                } else {
                    teamContainer.innerHTML = '<p class="text-center w-100 opacity-75">No team members assigned yet.</p>';
                }

                // Slide 5: Financials
                const budget = parseFloat(data.budget || 0);
                const actual = parseFloat(data.actual_cost || 0);
                const remaining = budget - actual;
                
                document.getElementById('hl_budget').textContent = 'Rp' + budget.toLocaleString();
                document.getElementById('hl_actual').textContent = 'Rp' + actual.toLocaleString();
                document.getElementById('hl_remaining').textContent = 'Rp' + remaining.toLocaleString();
                
                const statusBadge = document.getElementById('hl_budget_status');
                if (remaining < 0) {
                    statusBadge.textContent = 'OVER BUDGET';
                    statusBadge.className = 'badge bg-danger border border-1 border-white extra-small';
                    document.getElementById('hl_remaining').className = 'fw-900 mb-0 text-danger';
                } else {
                    statusBadge.textContent = 'ON TRACK';
                    statusBadge.className = 'badge bg-success border border-1 border-white extra-small';
                    document.getElementById('hl_remaining').className = 'fw-900 mb-0 text-info';
                }

                // Show modal
                if (window.bootstrap) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
                    bsModal.show();
                }

                // Reset carousel to first slide
                carousel.to(0);
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    body {
        background-color: #ffffff !important;
    }
</style>
@endpush

@endsection
