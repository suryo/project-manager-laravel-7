@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Task Details</span>
                    <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-sm btn-outline-secondary">Back to Project</a>
                </div>
                <div class="card-body">
                    <h3>{{ $task->title }}</h3>
                    <div class="mb-3">
                        <span class="badge bg-{{ $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                        <span class="text-muted ms-2">Project: <strong>{{ $task->project->title }}</strong></span>
                    </div>

                    <div class="mb-3 task-description-content">
                        {!! $task->description !!}
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Assigned To:</strong> 
                                @forelse($task->assignees as $member)
                                    <span class="badge bg-light text-dark border border-1 border-dark me-1">{{ $member->name }}</span>
                                @empty
                                    <span class="text-muted">Unassigned</span>
                                @endforelse
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Due Date:</strong> {{ optional($task->due_date)->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">Edit Task</a>
                        
                        @can('delete', $task)
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete Task</button>
                        </form>
                        @endcan
                    </div>

                    <hr class="my-5">

                    <div class="comments-section">
                        <h4 class="mb-4">Comments</h4>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="mb-4">
                            @foreach($task->comments as $comment)
                                <div class="card mb-3 border-light shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">{{ $comment->user->name }}</h6>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="comment-content ql-editor px-0">
                                            {!! $comment->content !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($task->comments->isEmpty())
                                <p class="text-muted italic">No comments yet.</p>
                            @endif
                        </div>

                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Add a Comment</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('tasks.comments.store', $task) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <div id="comment-editor" style="height: 150px;"></div>
                                        <input type="hidden" name="content" id="comment-content">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#comment-editor', {
            theme: 'snow',
            placeholder: 'Write your comment here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        var form = document.querySelector('form[action*="comments"]');
        form.onsubmit = function() {
            var content = document.querySelector('#comment-content');
            content.value = quill.root.innerHTML;
            
            if (quill.getText().trim().length === 0 && quill.root.innerHTML.indexOf('<img') === -1) {
                alert('Please enter a comment.');
                return false;
            }
        };
    });
</script>
@endpush
@endsection
