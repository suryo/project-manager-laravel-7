@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $meeting->title }}</h1>
        <div>
            <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Meeting Details -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Meeting Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Department:</strong>
                            <span class="badge bg-secondary">{{ $meeting->department->name }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Date & Time:</strong>
                            {{ $meeting->meeting_date->format('l, F d, Y - H:i') }}
                        </div>
                    </div>

                    @if($meeting->location)
                        <div class="mb-3">
                            <strong>Location:</strong>
                            {{ $meeting->location }}
                        </div>
                    @endif

                    @if($meeting->description)
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p>{{ $meeting->description }}</p>
                        </div>
                    @endif

                    @if($meeting->notes)
                        <div class="mb-3">
                            <strong>Notulensi (Meeting Notes):</strong>
                            <div class="ql-snow">
                                <div class="ql-editor" style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                                    {!! $meeting->notes !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-3">
                        <small class="text-muted">Created by: {{ $meeting->creator->name }} on {{ $meeting->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance ({{ $meeting->attendances->count() }})</h6>
                </div>
                <div class="card-body">
                    @forelse($meeting->attendances as $attendance)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <strong>{{ $attendance->user->name }}</strong>
                                @if($attendance->status == 'present')
                                    <br><span class="badge bg-success">Present</span>
                                @elseif($attendance->status == 'absent')
                                    <br><span class="badge bg-danger">Absent</span>
                                @else
                                    <br><span class="badge bg-warning">Excused</span>
                                @endif
                                @if($attendance->notes)
                                    <br><small class="text-muted">{{ $attendance->notes }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No attendance records yet</p>
                    @endforelse

                    <!-- Quick Attendance Form -->
                    <button class="btn btn-sm btn-primary w-100 mt-3" data-bs-toggle="collapse" data-bs-target="#attendanceForm">
                        <i class="bi bi-plus-circle me-1"></i>Mark Attendance
                    </button>

                    <div class="collapse mt-3" id="attendanceForm">
                        <form action="{{ route('meetings.attendance', $meeting) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label fw-bold">Add Attendees</label>
                                @foreach($meeting->department->members as $member)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attendances[{{ $loop->index }}][user_id]" value="{{ $member->id }}" id="user{{ $member->id }}">
                                        <label class="form-check-label" for="user{{ $member->id }}">
                                            {{ $member->name }}
                                        </label>
                                        <select class="form-select form-select-sm mt-1" name="attendances[{{ $loop->index }}][status]">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="excused">Excused</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-sm btn-success w-100">Save Attendance</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { font-family: inherit; }
    .ql-editor p { margin-bottom: 1rem; }
</style>
@endpush
@endsection
