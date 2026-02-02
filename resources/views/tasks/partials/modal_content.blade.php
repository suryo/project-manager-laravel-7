<div class="modal-header border-bottom-0 pb-0">
    <h5 class="modal-title font-small text-muted" id="taskModalLabel">Task Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body pt-0 px-4 pb-4">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h3 class="fw-bold mb-0 text-dark">{{ $task->title }}</h3>
        <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">Back to Project</a>
    </div>
    <div class="mb-4 d-flex align-items-center">
        <span class="badge bg-{{ in_array($task->status, ['done', 'check']) ? 'success' : (in_array($task->status, ['in_progress', 'test']) ? 'info' : ($task->status === 'review' ? 'warning' : 'secondary')) }} rounded-pill px-3">
            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
        </span>
        <span class="text-muted ms-3 small">Project: <strong class="text-dark">{{ $task->project->title }}</strong></span>
    </div>

    <div class="p-3 bg-light rounded shadow-none border mb-4 task-description-content">
        {!! $task->description !!}
    </div>

    <hr class="opacity-10">

    <div class="row mb-4 bg-light p-3 rounded mx-0 border">
        <div class="col-md-6 border-end">
            <p class="mb-0 small text-muted">Assigned To</p>
            <div class="mt-1">
                @forelse($task->assignees as $member)
                    <span class="badge bg-white text-dark border me-1 rounded-pill">{{ $member->name }}</span>
                @empty
                    <span class="text-muted small italic">Unassigned</span>
                @endforelse
            </div>
        </div>
        <div class="col-md-6 ps-4">
            <p class="mb-0 small text-muted">Due Date</p>
            <p class="fw-bold mb-0 mt-1">
                <i class="bi bi-calendar-event me-1"></i>
                {{ optional($task->due_date)->format('M d, Y') ?? 'N/A' }}
            </p>
        </div>
    </div>

    <div class="mb-4 d-flex gap-2">
        @can('update', $task)
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-pencil-square me-1"></i> Edit Task
        </a>
        @endcan
        
        @can('delete', $task)
        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger rounded-pill px-4" onclick="return confirm('Are you sure you want to delete this task?')">Delete Task</button>
        </form>
        @endcan
    </div>

    {{-- Quick Status Update for Assignees --}}
    @if($task->assignees->contains(auth()->id()) && $task->status !== 'done')
    @php
        $isSpv = auth()->user()->departments()
            ->where('departments.id', $task->project->department_id)
            ->where('department_members.role', 'SPV')
            ->exists();
    @endphp
    <div class="mb-5 p-4 bg-yellow-50 border border-warning rounded shadow-sm">
        <h6 class="mb-3 fw-bold d-flex align-items-center text-warning">
            <i class="bi bi-lightning-charge-fill me-2"></i> Quick Status Update
        </h6>
        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="ajax-status-form">
            @csrf
            @method('PATCH')
            <div class="d-flex gap-2">
                <select name="status" class="form-select rounded-pill px-4 shadow-sm" required>
                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Todo</option>
                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>Review</option>
                    <option value="test" {{ $task->status === 'test' ? 'selected' : '' }}>Test</option>
                    <option value="check" {{ $task->status === 'check' ? 'selected' : '' }}>Check</option>
                    @if(auth()->user()->role === 'admin' || $isSpv)
                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                    @endif
                </select>
                <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">
                    Update Status
                </button>
            </div>
        </form>
    </div>
    @endif

    <hr class="my-5 opacity-10">

    <div class="comments-section px-2">
        <h4 class="mb-4 d-flex align-items-center fw-bold">
            Comments
            <span class="badge bg-secondary rounded-pill ms-3 font-small px-3">{{ $task->comments->count() }}</span>
        </h4>

        <div class="mb-4">
            @foreach($task->comments as $comment)
                <div class="card mb-3 border-0 shadow-sm bg-light-subtle rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 small">{{ $comment->user->name }}</h6>
                            <small class="text-muted x-small">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="comment-content ql-editor px-0 py-0 small bg-transparent overflow-hidden">
                            {!! $comment->content !!}
                        </div>
                    </div>
                </div>
            @endforeach

            @if($task->comments->isEmpty())
                <div class="text-center py-5 text-muted opacity-50">
                    <i class="bi bi-chat-dots display-4 d-block mb-3"></i>
                    <p class="mb-0">No comments yet. Be the first to start the conversation!</p>
                </div>
            @endif
        </div>

        <div class="card border-primary rounded-4 shadow-sm overflow-hidden mt-5">
            <div class="card-header bg-primary text-white py-2 px-3 fw-bold small">
                <i class="bi bi-pencil me-1"></i> Add a Comment
            </div>
            <div class="card-body p-3 bg-white">
                <form action="{{ route('tasks.comments.store', $task) }}" method="POST" id="modal-comment-form" class="ajax-comment-form">
                    @csrf
                    <div class="mb-3">
                        <div id="modal-comment-editor" class="bg-white" style="height: 120px; border-radius: 0 0 10px 10px;"></div>
                        <input type="hidden" name="content" id="modal-comment-content">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm float-end">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-yellow-50 { background-color: #fffdf0; }
    .font-xs { font-size: 0.75rem; }
    .x-small { font-size: 0.8rem; }
    .ql-editor { font-family: inherit !important; }
</style>
