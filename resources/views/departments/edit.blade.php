@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Department</h1>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Details: {{ $department->name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('departments.update', $department) }}" method="POST" id="department-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="name" class="form-label fw-bold">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="parent_id" class="form-label fw-bold">Parent Department <span class="text-muted">(Optional)</span></label>
                            <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">-- None (Root Department) --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('parent_id', $department->parent_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Cannot select self or descendant departments</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <!-- Quill Editor Container -->
                            <div id="editor" style="height: 200px; background: #fff;">{!! old('description', $department->description) !!}</div>
                            <input type="hidden" name="description" id="description" value="{{ old('description', $department->description) }}">
                            
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('departments.index') }}" class="btn btn-light me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['clean']
                ]
            }
        });

        // Initialize editor content handled by Blade directly inside div

        var form = document.getElementById('department-form');
        var descriptionInput = document.getElementById('description');

        // Update hidden input on every change (more robust)
        quill.on('text-change', function() {
            descriptionInput.value = quill.root.innerHTML;
        });

        // Also ensure it's synced on submit just in case
        form.addEventListener('submit', function(e) {
            descriptionInput.value = quill.root.innerHTML;
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Ensure paragraphs have spacing in editor */
    #editor .ql-editor p { margin-bottom: 1em !important; }
    #editor .ql-editor ul, #editor .ql-editor ol { padding-left: 1.5em; }
    #editor .ql-editor { font-family: inherit; line-height: 1.6; }
</style>
@endpush
@endsection
