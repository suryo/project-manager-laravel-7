@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="bi bi-sticky-fill text-primary me-2"></i>All My Notes
                    </h1>
                    <p class="text-muted mb-0">Manage notes from all your departments in one place</p>
                </div>
                <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createNoteModal">
                    <i class="bi bi-plus-circle me-2"></i>New Note
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <form action="{{ route('notes.all') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-2">SEARCH</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                   placeholder="Search by title or content..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-2">DEPARTMENT</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-2">COLOR</label>
                        <select name="color" class="form-select">
                            <option value="">All Colors</option>
                            <option value="yellow" {{ request('color') == 'yellow' ? 'selected' : '' }}>🟡 Yellow</option>
                            <option value="blue" {{ request('color') == 'blue' ? 'selected' : '' }}>🔵 Blue</option>
                            <option value="green" {{ request('color') == 'green' ? 'selected' : '' }}>🟢 Green</option>
                            <option value="pink" {{ request('color') == 'pink' ? 'selected' : '' }}>🩷 Pink</option>
                            <option value="purple" {{ request('color') == 'purple' ? 'selected' : '' }}>🟣 Purple</option>
                            <option value="orange" {{ request('color') == 'orange' ? 'selected' : '' }}>🟠 Orange</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Department Quick Links -->
    @if($departments->count() > 0)
    <div class="mb-4">
        <h6 class="text-muted fw-semibold mb-3 small">QUICK ACCESS BY DEPARTMENT</h6>
        <div class="d-flex flex-wrap gap-2">
            @foreach($departments as $dept)
                <a href="{{ route('departments.notes.index', $dept) }}" 
                   class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-building me-1"></i>{{ $dept->name }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Sticky Notes Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-3">
        @forelse($notes as $note)
            <div class="col">
                <div class="sticky-note sticky-note-{{ $note->color }}" data-note-id="{{ $note->id }}" onclick="viewNoteDetail({{ $note->id }})" style="cursor: pointer;">
                    <div class="sticky-note-header">
                        <h5 class="sticky-note-title">{{ $note->title }}</h5>
                        <div class="sticky-note-actions" onclick="event.stopPropagation();">
                            <button type="button" class="btn btn-sm btn-link p-0 me-2" onclick="viewNoteDetail({{ $note->id }})" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if(Auth::user()->role === 'admin' || Auth::id() === $note->user_id)
                                <form action="{{ route('departments.notes.toggle-pin', [$note->department, $note]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 me-2" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                                        <i class="bi bi-pin{{ $note->is_pinned ? '-fill' : '' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-link p-0 me-2" onclick="editNote({{ $note->id }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('departments.notes.destroy', [$note->department, $note]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this note?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="sticky-note-content">
                        {!! Str::limit($note->content, 200) !!}
                    </div>
                    
                    <div class="sticky-note-footer">
                        <small class="text-muted">
                            <i class="bi bi-person-circle"></i> {{ $note->user->name }}
                        </small>
                        <small class="text-muted">
                            {{ $note->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-sticky display-1 text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold mb-2">No Notes Yet</h4>
                        <p class="text-muted mb-4">Start creating sticky notes to organize your thoughts and ideas!</p>
                        
                        <button type="button" class="btn btn-primary btn-lg mb-3" data-bs-toggle="modal" data-bs-target="#createNoteModal">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Note
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
    .sticky-note {
        position: relative;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        min-height: 180px; /* Reduced from 280px for landscape */
        max-height: 200px; /* Add max height for consistency */
        display: flex;
        flex-direction: column;
    }
    
    .sticky-note:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08);
    }
    
    .sticky-note-yellow { background: linear-gradient(135deg, #FFF9C4 0%, #FFF59D 100%); }
    .sticky-note-blue { background: linear-gradient(135deg, #BBDEFB 0%, #90CAF9 100%); }
    .sticky-note-green { background: linear-gradient(135deg, #C8E6C9 0%, #A5D6A7 100%); }
    .sticky-note-pink { background: linear-gradient(135deg, #F8BBD0 0%, #F48FB1 100%); }
    .sticky-note-purple { background: linear-gradient(135deg, #E1BEE7 0%, #CE93D8 100%); }
    .sticky-note-orange { background: linear-gradient(135deg, #FFE0B2 0%, #FFCC80 100%); }
    
    .sticky-note-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .sticky-note-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        flex: 1;
        color: #333;
    }
    
    .sticky-note-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .sticky-note:hover .sticky-note-actions {
        opacity: 1;
    }
    
    .sticky-note-actions form {
        display: inline-flex;
        margin: 0;
    }
    
    .sticky-note-actions .btn {
        color: #555;
        font-size: 0.95rem;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }
    
    .sticky-note-actions .btn:hover {
        background-color: rgba(0,0,0,0.1);
        transform: scale(1.1);
    }
    
    .sticky-note-actions .btn.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545 !important;
    }
    
    .sticky-note-content {
        flex: 1;
        color: #555;
        line-height: 1.5;
        margin-bottom: 15px;
        overflow: hidden;
        word-wrap: break-word;
        max-height: 60px; /* Reduced from 120px for landscape */
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Reduced from 6 to 3 lines */
        -webkit-box-orient: vertical;
        font-size: 0.9rem;
    }
    
    .sticky-note-content p {
        margin-bottom: 0.5rem;
    }
    
    .sticky-note-content p:last-child {
        margin-bottom: 0;
    }
    
    .sticky-note-content ul,
    .sticky-note-content ol {
        margin: 0.5rem 0;
        padding-left: 1.5rem;
    }
    
    .sticky-note-content li {
        margin-bottom: 0.25rem;
    }
    
    .sticky-note-content strong {
        font-weight: 600;
    }
    
    .sticky-note-content em {
        font-style: italic;
    }
    
    .sticky-note-content u {
        text-decoration: underline;
    }
    
    .sticky-note-content a {
        color: #4e73df;
        text-decoration: underline;
    }
    
    .sticky-note-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        margin-top: auto;
    }
    
    .sticky-note-footer small {
        font-size: 0.7rem;
    }
</style>
@endpush

<!-- Create Note Modal -->
<div class="modal fade" id="createNoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createNoteForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="department_id" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                        <select id="department_id" class="form-select" name="department_id" required {{ Auth::user()->role !== 'admin' && $departments->count() === 1 ? 'readonly' : '' }}>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ Auth::user()->role !== 'admin' && $departments->count() === 1 ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @if(Auth::user()->role !== 'admin' && $departments->count() === 1)
                            <small class="text-muted">Auto-selected based on your department</small>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">Content</label>
                        <input type="hidden" id="content" name="content">
                        <div id="content-editor" style="height: 200px;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Color</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <input type="radio" class="btn-check" name="color" id="color-yellow" value="yellow" checked>
                            <label class="btn btn-outline-warning" for="color-yellow">🟡 Yellow</label>
                            
                            <input type="radio" class="btn-check" name="color" id="color-blue" value="blue">
                            <label class="btn btn-outline-primary" for="color-blue">🔵 Blue</label>
                            
                            <input type="radio" class="btn-check" name="color" id="color-green" value="green">
                            <label class="btn btn-outline-success" for="color-green">🟢 Green</label>
                            
                            <input type="radio" class="btn-check" name="color" id="color-pink" value="pink">
                            <label class="btn btn-outline-danger" for="color-pink">🩷 Pink</label>
                            
                            <input type="radio" class="btn-check" name="color" id="color-purple" value="purple">
                            <label class="btn" style="border: 1px solid #9C27B0; color: #9C27B0;" for="color-purple">🟣 Purple</label>
                            
                            <input type="radio" class="btn-check" name="color" id="color-orange" value="orange">
                            <label class="btn" style="border: 1px solid #FF9800; color: #FF9800;" for="color-orange">🟠 Orange</label>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1">
                        <label class="form-check-label" for="is_pinned">
                            📌 Pin this note
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Note Modal -->
<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editNoteForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_content" class="form-label fw-bold">Content</label>
                        <input type="hidden" id="edit_content" name="content">
                        <div id="edit-content-editor" style="height: 200px;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Color</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <input type="radio" class="btn-check" name="color" id="edit-color-yellow" value="yellow">
                            <label class="btn btn-outline-warning" for="edit-color-yellow">🟡 Yellow</label>
                            
                            <input type="radio" class="btn-check" name="color" id="edit-color-blue" value="blue">
                            <label class="btn btn-outline-primary" for="edit-color-blue">🔵 Blue</label>
                            
                            <input type="radio" class="btn-check" name="color" id="edit-color-green" value="green">
                            <label class="btn btn-outline-success" for="edit-color-green">🟢 Green</label>
                            
                            <input type="radio" class="btn-check" name="color" id="edit-color-pink" value="pink">
                            <label class="btn btn-outline-danger" for="edit-color-pink">🩷 Pink</label>
                            
                            <input type="radio" class="btn-check" name="color" id="edit-color-purple" value="purple">
                            <label class="btn" style="border: 1px solid #9C27B0; color: #9C27B0;" for="edit-color-purple">🟣 Purple</label>
                            
                            <input type="radio" class="btn-check" name="color" id="edit-color-orange" value="orange">
                            <label class="btn" style="border: 1px solid #FF9800; color: #FF9800;" for="edit-color-orange">🟠 Orange</label>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_pinned" id="edit_is_pinned" value="1">
                        <label class="form-check-label" for="edit_is_pinned">
                            📌 Pin this note
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Note Detail Modal -->
<div class="modal fade" id="viewNoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewNoteTitle">Note Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">DEPARTMENT</label>
                    <p id="viewNoteDepartment" class="mb-0"></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">CONTENT</label>
                    <div id="viewNoteContent" class="border rounded p-3 bg-light"></div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">COLOR</label>
                        <p id="viewNoteColor" class="mb-0"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">STATUS</label>
                        <p id="viewNotePinned" class="mb-0"></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">CREATED BY</label>
                        <p id="viewNoteUser" class="mb-0"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">CREATED AT</label>
                        <p id="viewNoteCreated" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    // Initialize Quill editors
    var createQuill = new Quill('#content-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link']
            ]
        }
    });
    
    var editQuill = new Quill('#edit-content-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link']
            ]
        }
    });
    
    // Handle create form submission
    document.getElementById('createNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get selected department
        var departmentId = document.getElementById('department_id').value;
        if (!departmentId) {
            alert('Please select a department');
            return;
        }
        
        // Set content from Quill editor
        document.getElementById('content').value = createQuill.root.innerHTML;
        
        // Set form action dynamically based on selected department
        this.action = `/departments/${departmentId}/notes`;
        
        // Submit form
        this.submit();
    });
    
    // Handle edit form submission
    document.getElementById('editNoteForm').addEventListener('submit', function(e) {
        // Set content from Quill editor
        document.getElementById('edit_content').value = editQuill.root.innerHTML;
    });
    
    // Notes data for editing
    var notesData = @json($notes->keyBy('id'));
    
    // Edit note function
    function editNote(noteId) {
        var note = notesData[noteId];
        if (!note) return;
        
        // Set form action
        document.getElementById('editNoteForm').action = `/departments/${note.department_id}/notes/${noteId}`;
        
        // Fill form fields
        document.getElementById('edit_title').value = note.title;
        editQuill.root.innerHTML = note.content || '';
        document.getElementById('edit-color-' + note.color).checked = true;
        document.getElementById('edit_is_pinned').checked = note.is_pinned;
        
        // Show modal
        new bootstrap.Modal(document.getElementById('editNoteModal')).show();
    }
    
    // View note detail function
    function viewNoteDetail(noteId) {
        var note = notesData[noteId];
        if (!note) return;
        
        // Color emoji mapping
        var colorEmojis = {
            'yellow': '🟡 Yellow',
            'blue': '🔵 Blue',
            'green': '🟢 Green',
            'pink': '🩷 Pink',
            'purple': '🟣 Purple',
            'orange': '🟠 Orange'
        };
        
        // Fill modal with note details
        document.getElementById('viewNoteTitle').textContent = note.title;
        document.getElementById('viewNoteDepartment').textContent = note.department.name;
        document.getElementById('viewNoteContent').innerHTML = note.content || '<em class="text-muted">No content</em>';
        document.getElementById('viewNoteColor').textContent = colorEmojis[note.color] || note.color;
        document.getElementById('viewNotePinned').innerHTML = note.is_pinned 
            ? '<span class="badge bg-danger">📌 Pinned</span>' 
            : '<span class="badge bg-secondary">Not Pinned</span>';
        document.getElementById('viewNoteUser').textContent = note.user.name;
        document.getElementById('viewNoteCreated').textContent = new Date(note.created_at).toLocaleString();
        
        // Show modal
        new bootstrap.Modal(document.getElementById('viewNoteModal')).show();
    }
</script>
@endpush
