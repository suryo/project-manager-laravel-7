@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">📝 All My Notes</h1>
            <p class="text-muted small">Notes from all your departments</p>
        </div>
        @if(Auth::user()->role === 'admin')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoteModal">
                <i class="bi bi-plus-circle me-2"></i>New Note
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('notes.all') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search notes..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
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
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Departments Quick Links -->
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            @foreach($departments as $dept)
                <a href="{{ route('departments.notes.index', $dept) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-building me-1"></i>{{ $dept->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Sticky Notes Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @forelse($notes as $note)
            <div class="col">
                <div class="sticky-note sticky-note-{{ $note->color }}" data-note-id="{{ $note->id }}">
                    @if($note->is_pinned)
                        <div class="pin-badge">
                            <i class="bi bi-pin-angle-fill"></i>
                        </div>
                    @endif
                    
                    <div class="sticky-note-header">
                        <h5 class="sticky-note-title">{{ $note->title }}</h5>
                        <div class="sticky-note-actions">
                            <form action="{{ route('departments.notes.toggle-pin', [$note->department, $note]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link p-0 me-2" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                                    <i class="bi bi-pin{{ $note->is_pinned ? '-fill' : '' }}"></i>
                                </button>
                            </form>
                            <a href="{{ route('departments.notes.index', $note->department) }}" class="btn btn-sm btn-link p-0 me-2" title="View in Department">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <form action="{{ route('departments.notes.destroy', [$note->department, $note]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this note?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link p-0 text-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="sticky-note-content">
                        {!! Str::limit(strip_tags($note->content), 150) !!}
                    </div>
                    
                    <div class="sticky-note-footer">
                        <small class="text-muted">
                            <i class="bi bi-building"></i> {{ $note->department->name }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-person-circle"></i> {{ $note->user->name }}
                        </small>
                    </div>
                    <div class="sticky-note-footer border-top-0 pt-0">
                        <small class="text-muted">
                            {{ $note->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-sticky display-1 text-muted"></i>
                    <p class="text-muted mt-3">No notes yet. Go to a department to create your first sticky note!</p>
                    <div class="mt-3">
                        @foreach($departments as $dept)
                            <a href="{{ route('departments.notes.index', $dept) }}" class="btn btn-primary me-2">
                                <i class="bi bi-plus-circle me-1"></i>Create Note in {{ $dept->name }}
                            </a>
                        @endforeach
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
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        min-height: 280px;
        display: flex;
        flex-direction: column;
    }
    
    .sticky-note:hover {
        transform: translateY(-5px) rotate(1deg);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15), 0 3px 6px rgba(0,0,0,0.1);
    }
    
    .sticky-note-yellow { background: linear-gradient(135deg, #FFF9C4 0%, #FFF59D 100%); }
    .sticky-note-blue { background: linear-gradient(135deg, #BBDEFB 0%, #90CAF9 100%); }
    .sticky-note-green { background: linear-gradient(135deg, #C8E6C9 0%, #A5D6A7 100%); }
    .sticky-note-pink { background: linear-gradient(135deg, #F8BBD0 0%, #F48FB1 100%); }
    .sticky-note-purple { background: linear-gradient(135deg, #E1BEE7 0%, #CE93D8 100%); }
    .sticky-note-orange { background: linear-gradient(135deg, #FFE0B2 0%, #FFCC80 100%); }
    
    .pin-badge {
        position: absolute;
        top: -10px;
        right: 20px;
        background: #FF3131;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transform: rotate(45deg);
    }
    
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
        gap: 5px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .sticky-note:hover .sticky-note-actions {
        opacity: 1;
    }
    
    .sticky-note-content {
        flex: 1;
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
        overflow: hidden;
    }
    
    .sticky-note-footer {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid rgba(0,0,0,0.1);
    }
    
    .sticky-note-footer small {
        font-size: 0.7rem;
    }
</style>
@endpush

<!-- Create Note Modal for Admin -->
@if(Auth::user()->role === 'admin')
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
                        <select id="department_id" class="form-select" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
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

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    // Initialize Quill editor
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
    
    // Handle form submission
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
</script>
@endpush
@endif
