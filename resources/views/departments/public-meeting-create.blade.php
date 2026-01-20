@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="mb-4">
                <a href="{{ route('department.landing', $department->slug) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-2"></i>Back to {{ $department->name }}
                </a>
            </div>

            <div class="card shadow-lg border-0 rounded-lg overflow-hidden glass-card">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Create Meeting</h4>
                    <small>{{ $department->name }}</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('meetings.store') }}" method="POST" id="meeting-form">
                        @csrf
                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                        
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
                                    <label for="meeting_date" class="form-label fw-bold">Date & Time <span class="text-danger">*</span></label>
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
                            <div id="editor" style="height: 250px; background: #fff;">{!! old('notes') !!}</div>
                            <input type="hidden" name="notes" id="notes" value="{{ old('notes') }}">
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg me-2"></i>Create Meeting
                            </button>
                            <a href="{{ route('department.landing', $department->slug) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
</style>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { font-family: inherit; line-height: 1.6; }
    .ql-editor p { margin-bottom: 1em !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write meeting notes...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
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
@endsection
