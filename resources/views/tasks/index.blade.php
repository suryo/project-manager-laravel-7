@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">My Assigned Tasks</h1>

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
@endsection
