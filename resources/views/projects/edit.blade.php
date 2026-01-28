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
                            <label for="department_id" class="form-label text-muted small fw-bold">Department (Optional)</label>
                            @php
                                $isAdmin = Auth::user()->role === 'admin';
                            @endphp
                            <select id="department_id_select" class="form-select @error('department_id') is-invalid @enderror" {{ $isAdmin ? 'name=department_id' : 'disabled' }}>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $project->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>

                            @if(!$isAdmin)
                                <input type="hidden" name="department_id" value="{{ old('department_id', $project->department_id) }}">
                            @endif

                            @error('department_id')
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

                        <div class="mb-3">
                            <label for="pic_id" class="form-label text-muted small fw-bold">PIC (Person In Charge) - Optional</label>
                            <select id="pic_id" class="form-select @error('pic_id') is-invalid @enderror" name="pic_id">
                                <option value="">Select PIC</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('pic_id', $project->pic_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('pic_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label fw-bold">Project Color</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="radio" class="btn-check" name="color" id="edit-color-blue" value="blue" {{ old('color', $project->color) == 'blue' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="edit-color-blue">🔵 Blue</label>
                                
                                <input type="radio" class="btn-check" name="color" id="edit-color-green" value="green" {{ old('color', $project->color) == 'green' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success" for="edit-color-green">🟢 Green</label>
                                
                                <input type="radio" class="btn-check" name="color" id="edit-color-yellow" value="yellow" {{ old('color', $project->color) == 'yellow' ? 'checked' : '' }}>
                                <label class="btn btn-outline-warning" for="edit-color-yellow">🟡 Yellow</label>
                                
                                <input type="radio" class="btn-check" name="color" id="edit-color-orange" value="orange" {{ old('color', $project->color) == 'orange' ? 'checked' : '' }}>
                                <label class="btn" style="border: 1px solid #FF9800; color: #FF9800;" for="edit-color-orange">🟠 Orange</label>
                                
                                <input type="radio" class="btn-check" name="color" id="edit-color-pink" value="pink" {{ old('color', $project->color) == 'pink' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger" for="edit-color-pink">🩷 Pink</label>
                                
                                <input type="radio" class="btn-check" name="color" id="edit-color-purple" value="purple" {{ old('color', $project->color) == 'purple' ? 'checked' : '' }}>
                                <label class="btn" style="border: 1px solid #9C27B0; color: #9C27B0;" for="edit-color-purple">🟣 Purple</label>
                            </div>
                        </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-bold">Priority</label>
                                <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
                                    <option value="1" {{ old('priority', $project->priority) == '1' ? 'selected' : '' }}>🔴 P1 - Urgent</option>
                                    <option value="2" {{ old('priority', $project->priority) == '2' ? 'selected' : '' }}>🟠 P2 - High</option>
                                    <option value="3" {{ old('priority', $project->priority) == '3' ? 'selected' : '' }}>🟡 P3 - Medium</option>
                                    <option value="4" {{ old('priority', $project->priority) == '4' ? 'selected' : '' }}>🔵 P4 - Low</option>
                                    <option value="5" {{ old('priority', $project->priority) == '5' ? 'selected' : '' }}>⚪ P5 - Very Low</option>
                                </select>
                                @error('priority')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold d-block">Visibility</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned', $project->is_pinned) ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-bold ms-2 pt-1" for="is_pinned">
                                        📌 Pin Project (Show on Top)
                                    </label>
                                </div>
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
    
    // Initialize Select2 for PIC dropdown - wait for full page load
    window.addEventListener('load', function() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('#pic_id').select2({
                placeholder: 'Search and select PIC...',
                allowClear: true,
                width: '100%'
            });
        } else {
            console.error('Select2 or jQuery not loaded');
        }
    });
</script>
@endpush
