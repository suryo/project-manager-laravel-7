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
                                        <tr id="task-row-{{ $task->id }}">
                                            <td class="ps-4">
                                                <div class="fw-bold">{{ $task->title }}</div>
                                                <small class="text-muted d-block text-truncate task-description-list" style="max-width: 250px;">
                                                    {{ Str::limit(strip_tags($task->description), 60) }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ $task->project->title }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-status bg-{{ $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }} rounded-pill px-3">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ optional($task->due_date)->format('d M Y') ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm view-task-btn" 
                                                        data-task-id="{{ $task->id }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#taskDetailModal">
                                                    <i class="bi bi-eye-fill me-1"></i> View
                                                </button>
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
            {{-- ... existing POAC logs code ... --}}
@include('tasks.partials.poac_logs_card') {{-- Refactoring this to a partial might be cleaner but let's keep it simple for now if it's already there --}}
        </div>
    </div>
</div>

<!-- Task Detail Modal -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4" id="taskModalContent">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading task details...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalContent = document.getElementById('taskModalContent');

        // Helper function to initialize Quill
        function initQuill() {
            const editorElem = document.getElementById('modal-comment-editor');
            if (editorElem && !editorElem.classList.contains('ql-container')) {
                var quill = new Quill('#modal-comment-editor', {
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

                var form = document.getElementById('modal-comment-form');
                if (form) {
                    form.onsubmit = function() {
                        var content = document.getElementById('modal-comment-content');
                        content.value = quill.root.innerHTML;
                        
                        if (quill.getText().trim().length === 0 && quill.root.innerHTML.indexOf('<img') === -1) {
                            alert('Please enter a comment.');
                            return false;
                        }
                    };
                }
            }
        }

        // Global function to update background list
        function updateBackgroundList(taskId, statusLabel, statusClass) {
            const row = document.getElementById(`task-row-${taskId}`);
            if (row) {
                const badge = row.querySelector('.badge-status');
                if (badge) {
                    badge.textContent = statusLabel;
                    badge.className = `badge badge-status bg-${statusClass} rounded-pill px-3`;
                }
            }
        }

        // Handle Task Detail Loading
        document.querySelectorAll('.view-task-btn').forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                modalContent.innerHTML = `
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading task details...</p>
                    </div>
                `;

                fetch(`/tasks/${taskId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html;
                    initQuill();
                })
                .catch(error => {
                    modalContent.innerHTML = `<div class="p-4 text-center alert alert-danger">Failed to load task.</div>`;
                    console.error('Error:', error);
                });
            });
        });

        // Handle Form Submissions (Event Delegation)
        modalContent.addEventListener('submit', function(e) {
            // Case 1: Status Updates
            if (e.target.classList.contains('ajax-status-form')) {
                e.preventDefault();
                handleAjaxSubmit(e.target, (data, taskId) => {
                    updateBackgroundList(taskId, data.status_label, data.status_class);
                });
            }

            // Case 2: Comment Posting
            if (e.target.classList.contains('ajax-comment-form')) {
                e.preventDefault();
                handleAjaxSubmit(e.target);
            }
        });

        // Universal AJAX Submit Handler
        function handleAjaxSubmit(form, onSuccess = null) {
            const formData = new FormData(form);
            const url = form.getAttribute('action');
            const urlParts = url.split('/');
            // Extract task ID from URL (handling both /tasks/ID/status and /tasks/ID/comments)
            const taskId = urlParts.find(p => !isNaN(p) && p !== '');

            const btn = form.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (onSuccess) onSuccess(data, taskId);
                    
                    // Refresh modal content for both cases
                    modalContent.innerHTML = data.modal_html;
                    initQuill();
                    
                    // Smooth scroll to bottom if it was a comment
                    if (form.classList.contains('ajax-comment-form')) {
                        const scrollElem = document.querySelector('.modal-dialog-scrollable .modal-content');
                        if (scrollElem) scrollElem.scrollTop = scrollElem.scrollHeight;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                alert('An error occurred. Please try again.');
            });
        }
    });
</script>
@endpush
@endsection
