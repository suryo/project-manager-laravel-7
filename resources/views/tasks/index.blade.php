@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">My Assigned Tasks</h1>
    </div>

    <div class="row">
        <!-- Tasks Column -->
        <div class="col-lg-8 mb-4">
            @if($tasks->count() > 0)
                <div class="row">
                    @foreach($tasks as $task)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">{{ $task->title }}</h5>
                                        <span class="badge bg-{{ $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </div>
                                    <h6 class="card-subtitle mb-2 text-muted">Project: {{ $task->project->title }}</h6>
                                    <p class="card-text">{{ Str::limit(strip_tags($task->description), 100) }}</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted">Due: {{ optional($task->due_date)->format('M d, Y') ?? 'N/A' }}</small>
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $tasks->links() }}
                </div>
            @else
                <div class="alert alert-info">You have no tasks assigned to you.</div>
            @endif
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
