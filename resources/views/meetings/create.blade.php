@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Meeting</h1>
        <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Meeting Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('meetings.store') }}" method="POST" id="meeting-form">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="department_id" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                            <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', request('department_id')) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="title" class="form-label fw-bold">Meeting Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="meeting_date" class="form-label fw-bold">Meeting Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('meeting_date') is-invalid @enderror" id="meeting_date" name="meeting_date" value="{{ old('meeting_date') }}" required>
                                    @error('meeting_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="location" class="form-label fw-bold">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" placeholder="e.g., Meeting Room A">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="notes" class="form-label fw-bold">Notulensi (Meeting Notes)</label>
                            <div id="editor" style="height: 200px; background: #fff;">{!! old('notes') !!}</div>
                            <input type="hidden" name="notes" id="notes" value="{{ old('notes') }}">
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('meetings.index') }}" class="btn btn-light me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-2"></i>Create Meeting
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
            placeholder: 'Write meeting notes...',
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

        var form = document.getElementById('meeting-form');
        var notesInput = document.getElementById('notes');

        quill.on('text-change', function() {
            notesInput.value = quill.root.innerHTML;
        });

        form.addEventListener('submit', function() {
            notesInput.value = quill.root.innerHTML;
        });
    });
</script>
@endpush

@push('styles')
<style>
    #editor .ql-editor p { margin-bottom: 1em !important; }
    #editor .ql-editor ul, #editor .ql-editor ol { padding-left: 1.5em; }
    #editor .ql-editor { font-family: inherit; line-height: 1.6; }
</style>
@endpush
@endsection
