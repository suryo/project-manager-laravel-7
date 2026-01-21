@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Project: {{ $project->title }}</div>

                <div class="card-body">
                    <form id="edit-project-form" method="POST" action="{{ route('projects.update', $project) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $project->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label">Group</label>
                            <input type="text" class="form-control @error('group') is-invalid @enderror" id="group" name="group" value="{{ old('group', $project->group) }}" placeholder="e.g., Marketing, Internal, Client X">
                            @error('group')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="budget" class="form-label">Project Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01">
                            </div>
                            @error('budget')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <input type="hidden" id="description" name="description" value="{{ old('description', $project->description) }}">
                            <div id="description-editor" style="height: 200px;">
                                {!! old('description', $project->description) !!}
                            </div>
                            @error('description')
                                <span class="text-danger small" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="project_status_id" class="form-label text-muted small fw-bold">Status</label>
                            <select id="project_status_id" class="form-select @error('project_status_id') is-invalid @enderror" name="project_status_id" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('project_status_id', $project->project_status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                            @error('project_status_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="mgmt_phase" class="form-label text-muted small fw-bold">Management Phase (POAC)</label>
                            <select id="mgmt_phase" class="form-select @error('mgmt_phase') is-invalid @enderror" name="mgmt_phase">
                                <option value="Planning" {{ old('mgmt_phase', $project->mgmt_phase) == 'Planning' ? 'selected' : '' }}>Planning</option>
                                <option value="Organizing" {{ old('mgmt_phase', $project->mgmt_phase) == 'Organizing' ? 'selected' : '' }}>Organizing</option>
                                <option value="Actuating" {{ old('mgmt_phase', $project->mgmt_phase) == 'Actuating' ? 'selected' : '' }}>Actuating</option>
                                <option value="Controlling" {{ old('mgmt_phase', $project->mgmt_phase) == 'Controlling' ? 'selected' : '' }}>Controlling</option>
                            </select>
                            @error('mgmt_phase')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}">
                                @error('start_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}">
                                @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Update Project</button>
                        <a href="{{ route('projects.index') }}" class="btn btn-link">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-container {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    .ql-toolbar {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#description-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    });

    // Update hidden input before form submit
    var form = document.getElementById('edit-project-form');
    form.onsubmit = function() {
        var description = document.getElementById('description');
        description.value = quill.root.innerHTML;
    };
</script>
@endpush
