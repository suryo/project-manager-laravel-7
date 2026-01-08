@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h2 class="fw-bold text-uppercase letter-spacing-1">
            <i class="bi bi-pencil"></i> Edit Ticket
        </h2>
        <p class="text-muted">{{ $ticket->ticket_number }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="new_feature" {{ old('type', $ticket->type) == 'new_feature' ? 'selected' : '' }}>New Feature</option>
                                    <option value="update" {{ old('type', $ticket->type) == 'update' ? 'selected' : '' }}>Update/Modification</option>
                                    <option value="bug_fix" {{ old('type', $ticket->type) == 'bug_fix' ? 'selected' : '' }}>Bug Fix</option>
                                    <option value="enhancement" {{ old('type', $ticket->type) == 'enhancement' ? 'selected' : '' }}>Enhancement</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $ticket->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="project_id" class="form-label fw-bold">Related Project (Optional)</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                <option value="">None - General Request</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $ticket->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-white border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                                <i class="bi bi-save"></i> Update Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-info text-white border-bottom border-3 border-dark">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle"></i> Note</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-0">
                        <i class="bi bi-lightbulb"></i> Only basic information can be edited here. Document management and workflow progression are available in the ticket detail view.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
