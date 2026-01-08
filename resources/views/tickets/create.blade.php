@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h2 class="fw-bold text-uppercase letter-spacing-1">
            <i class="bi bi-ticket-perforated"></i> Create New Ticket
        </h2>
        <p class="text-muted">Submit a new request for system update or new feature</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berikan judul yang jelas dan deskriptif untuk permintaan Anda</small>
                        </div>

                        <div class="mb-3">
                            <label for "description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan detail kebutuhan, tujuan, dan ruang lingkup permintaan Anda</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type...</option>
                                    <option value="new_feature" {{ old('type') == 'new_feature' ? 'selected' : '' }}>New Feature</option>
                                    <option value="update" {{ old('type') == 'update' ? 'selected' : '' }}>Update/Modification</option>
                                    <option value="bug_fix" {{ old('type') == 'bug_fix' ? 'selected' : '' }}>Bug Fix</option>
                                    <option value="enhancement" {{ old('type') == 'enhancement' ? 'selected' : '' }}>Enhancement</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="">Select Priority...</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
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
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih project terkait jika permintaan ini berkaitan dengan project yang sudah ada</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tickets.index') }}" class="btn btn-white border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                                <i class="bi bi-send"></i> Create Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-warning border-bottom border-3 border-dark">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle"></i> Information</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Workflow Stages:</h6>
                    <ol class="small mb-3">
                        <li>Penerimaan Permintaan</li>
                        <li>Analisis Kebutuhan</li>
                        <li>Perencanaan</li>
                        <li>Pengembangan & Pengujian</li>
                        <li>Persetujuan & Serah Terima</li>
                        <li>Dokumentasi</li>
                    </ol>

                    <hr>

                    <h6 class="fw-bold">Document Requirements:</h6>
                    <p class="small mb-2"><strong>Mandatory Documents:</strong></p>
                    <ul class="small mb-3">
                        <li>Formulir Permintaan</li>
                        <li>User Requirements Document</li>
                        <li>Spesifikasi Fungsional & Teknis</li>
                        <li>Project Plan & Jadwal</li>
                        <li>User Manual</li>
                        <li>Berita Acara Serah Terima (BAST)</li>
                    </ul>

                    <p class="small text-muted mb-0">
                        <i class="bi bi-lightbulb"></i> <strong>Tip:</strong> After creating the ticket, you can upload documents in the ticket detail page.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
