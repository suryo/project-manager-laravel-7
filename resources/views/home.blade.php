@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}! You are logged in as <span class="badge bg-primary">{{ ucfirst(Auth::user()->role) }}</span>.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Project</a>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card h-100 bg-primary text-white" style="box-shadow: 6px 6px 0 #000; border: 2px solid #000;">
                <div class="card-body">
                    <p class="text-uppercase extra-small fw-800 opacity-75 mb-1">Total Projects</p>
                    <h2 class="fw-900 mb-0">{{ $projectsCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100" style="border: none;">
                <div class="card-body">
                    <p class="text-uppercase extra-small fw-800 text-muted mb-1">Total Budget</p>
                    <h2 class="fw-900 mb-0">Rp{{ number_format($totalBudget, 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100" style="border: none;">
                <div class="card-body">
                    <p class="text-uppercase extra-small fw-800 text-muted mb-1">Total Expended</p>
                    <h2 class="fw-900 mb-0 text-{{ $totalActualCost > $totalBudget && $totalBudget > 0 ? 'danger' : 'dark' }}">
                        Rp{{ number_format($totalActualCost, 0) }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100" style="border: none;">
                <div class="card-body">
                    @php $remaining = $totalBudget - $totalActualCost; @endphp
                    <p class="text-uppercase extra-small fw-800 text-muted mb-1">Remaining Funds</p>
                    <h2 class="fw-900 mb-0 text-{{ $remaining < 0 ? 'danger' : 'success' }}">
                        Rp{{ number_format($remaining, 0) }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Project Overview -->
        <div class="col-md-9 mb-4">
            <div class="card h-100">
                <div class="card-header pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Project Overview</h5>
                    <span class="badge bg-info">{{ count($allProjects) }} Projects</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4">Title</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>Actual</th>
                                    <th>Deadline</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allProjects as $project)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ $project->title }}</div>
                                            @if($project->group)
                                                <div class="extra-small text-primary fw-bold">{{ $project->group }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($project->department)
                                                <span class="badge bg-light text-secondary border border-1 border-secondary extra-small">
                                                    {{ $project->department->name }}
                                                </span>
                                            @else
                                                <span class="text-muted extra-small italic">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusName = strtolower($project->status->name ?? '');
                                                
                                                // Direct color mapping
                                                if (stripos($statusName, 'pending') !== false || stripos($statusName, 'planning') !== false) {
                                                    $color = '#ffc107';
                                                } elseif (stripos($statusName, 'completed') !== false || stripos($statusName, 'done') !== false) {
                                                    $color = '#198754';
                                                } elseif (stripos($statusName, 'progress') !== false || stripos($statusName, 'active') !== false) {
                                                    $color = '#0dcaf0';
                                                } elseif (stripos($statusName, 'cancelled') !== false || stripos($statusName, 'rejected') !== false) {
                                                    $color = '#dc3545';
                                                } else {
                                                    $color = '#6c757d';
                                                }
                                            @endphp
                                            <span style="color: {{ $color }} !important; font-weight: 600;">
                                                {{ $project->status->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="small fw-bold text-muted">Rp{{ number_format($project->budget, 0) }}</td>
                                        <td class="small fw-bold text-{{ ($cost = $project->tasks->sum('cost')) > $project->budget && $project->budget > 0 ? 'danger' : 'dark' }}">
                                            Rp{{ number_format($cost, 0) }}
                                        </td>
                                        <td>
                                            @if($project->end_date)
                                                <span class="{{ $project->end_date->isPast() ? 'text-danger fw-bold' : '' }}">
                                                    {{ $project->end_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted italic">No Deadline</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($allProjects->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted italic">No projects found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Alerts / Overloaded Staff -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header pt-4 px-4 bg-white border-0">
                    <h5 class="fw-bold mb-0 text-danger">⚠️ Team Alerts</h5>
                </div>
                <div class="card-body px-4">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Overloaded Staff (>3 Active Tasks)</h6>
                    @forelse($overloadedStaff as $staff)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border border-2 border-dark" style="background-color: var(--neo-warning)">
                            <div class="fw-bold">{{ $staff->name }}</div>
                            <span class="badge bg-danger">{{ $staff->assigned_tasks_count }} Tasks</span>
                        </div>
                    @empty
                        <p class="text-muted italic py-3 text-center">All staff workload is balanced. ✅</p>
                    @endforelse

                    <hr class="border-2 border-dark">
                    
                    <div class="d-grid gap-2 mt-4">
                        <h6 class="fw-bold small text-muted text-uppercase">Quick Links</h6>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm text-start">📂 All Projects</a>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm text-start">✅ My Tasks</a>
                    </div>
                </div>
            </div>

            <!-- Energy Monitor -->
            <div class="card shadow-sm">
                <div class="card-header pt-4 px-4 bg-white border-0">
                    <h5 class="fw-bold mb-0 text-primary">⚡ Energy Monitor</h5>
                </div>
                <div class="card-body px-4">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Monthly Capacity (176 Units)</h6>
                    
                    @foreach($staffMembers as $staff)
                        @php
                            $used = $staff->used_energy;
                            $limit = $staff->monthly_energy_limit;
                            $percentage = ($limit > 0) ? ($used / $limit) * 100 : 0;
                            $color = $percentage > 100 ? 'danger' : ($percentage > 80 ? 'warning' : 'success');
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">{{ $staff->name }}</span>
                                <span class="small fw-bold text-{{ $color }}">{{ $used }} / {{ $limit }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ min(100, $percentage) }}%" aria-valuenow="{{ $used }}" aria-valuemin="0" aria-valuemax="{{ $limit }}"></div>
                            </div>
                            @if($percentage > 100)
                                <small class="text-danger extra-small fw-bold">Over Capacity!</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
