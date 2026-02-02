@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">My Assigned Tasks</h1>
    </div>

    <div class="row">
        <!-- Tasks Column -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Task List</h5>
                </div>
                <div class="card-body p-0">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Task</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold">{{ $task->title }}</div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                                    {{ Str::limit(strip_tags($task->description), 60) }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ $task->project->title }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }} rounded-pill px-3">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ optional($task->due_date)->format('d M Y') ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm">
                                                    <i class="bi bi-eye-fill me-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-3 border-top">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-inbox display-1 opacity-25"></i>
                            <p class="mt-3 fs-5">You have no tasks assigned to you.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- POAC Logs Column -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>My POAC Activity</h5>
                </div>
                <div class="card-body p-0">
                    @if($poacLogs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($poacLogs as $log)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        @php
                                            $phaseColors = [
                                                'Planning' => 'primary',
                                                'Organizing' => 'info',
                                                'Actuating' => 'warning',
                                                'Controlling' => 'success'
                                            ];
                                            $phaseIcons = [
                                                'Planning' => '📋',
                                                'Organizing' => '🗂️',
                                                'Actuating' => '⚡',
                                                'Controlling' => '📊'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $phaseColors[$log->phase] ?? 'secondary' }}">
                                            {{ $phaseIcons[$log->phase] ?? '' }} {{ $log->phase }}
                                        </span>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <h6 class="mb-1 fw-bold">{{ $log->title }}</h6>
                                    @if($log->poacable)
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-check-circle"></i> Task: {{ $log->poacable->title }}
                                        </small>
                                    @endif
                                    <p class="mb-0 small text-muted">{{ Str::limit(strip_tags($log->description), 80) }}</p>
                                </div>
                            @endforeach
                        </div>
                        @if(Auth::user()->role === 'admin')
                            <div class="card-footer text-center">
                                <a href="{{ route('poac-logs.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Logs <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox display-4 opacity-50"></i>
                            <p class="mt-3 mb-0">No POAC activity yet</p>
                            <small>Your task logs will appear here</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
