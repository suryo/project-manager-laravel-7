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

            {{-- Meeting Details Card --}}
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden glass-card mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">{{ $meeting->title }}</h4>
                    <small>{{ $department->name }}</small>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="bi bi-calendar3 me-2"></i>Date & Time:</strong>
                            <p class="mb-0">{{ $meeting->meeting_date->format('l, F d, Y - H:i') }}</p>
                        </div>
                        @if($meeting->location)
                        <div class="col-md-6">
                            <strong><i class="bi bi-geo-alt me-2"></i>Location:</strong>
                            <p class="mb-0">{{ $meeting->location }}</p>
                        </div>
                        @endif
                    </div>

                    @if($meeting->description)
                    <div class="mb-4">
                        <strong>Description:</strong>
                        <p class="text-muted">{{ $meeting->description }}</p>
                    </div>
                    @endif

                    @if($meeting->notes)
                    <hr class="my-4">
                    <div class="mb-3">
                        <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2"></i>Notulensi (Meeting Notes)</h5>
                        <div class="ql-snow">
                            <div class="ql-editor" style="padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background: #f9f9f9; min-height: 200px;">
                                {!! $meeting->notes !!}
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>No meeting notes available yet.
                    </div>
                    @endif

                    <hr class="my-4">

                    <div class="text-muted small">
                        <i class="bi bi-person me-1"></i>Created by: {{ $meeting->creator->name }} on {{ $meeting->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>

            {{-- Attendance Card --}}
            @if($meeting->attendances && $meeting->attendances->count() > 0)
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden glass-card">
                <div class="card-header bg-secondary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Attendance ({{ $meeting->attendances->count() }})</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach($meeting->attendances as $attendance)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($attendance->status == 'present')
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                    @elseif($attendance->status == 'absent')
                                        <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>
                                    @endif
                                </div>
                                <div>
                                    <strong>{{ $attendance->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        @if($attendance->status == 'present')
                                            Present
                                        @elseif($attendance->status == 'absent')
                                            Absent
                                        @else
                                            Excused
                                        @endif
                                    </small>
                                    @if($attendance->notes)
                                        <br><small class="text-muted fst-italic">{{ $attendance->notes }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
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
    .ql-editor { font-family: inherit; }
    .ql-editor p { margin-bottom: 1rem; }
    .ql-editor ul, .ql-editor ol { padding-left: 1.5rem; }
</style>
@endpush
@endsection
