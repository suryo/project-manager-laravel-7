@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $department->name }}</h1>
        <div>
            <a href="{{ route('departments.members', $department) }}" class="btn btn-secondary">
                <i class="bi bi-people me-2"></i>Manage Members
            </a>
            <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Department Info -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                </div>
                <div class="card-body">
                    @if($department->parent)
                        <div class="mb-3">
                            <strong>Parent Department:</strong>
                            <span class="badge bg-secondary">{{ $department->parent->name }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="ql-snow">
                            <div class="ql-editor" style="padding: 0; min-height: auto;">
                                {!! $department->description !!}
                            </div>
                        </div>
                    </div>

                    @if($department->slug)
                        <div class="mb-3">
                            <strong>Public Landing Page:</strong>
                            <a href="{{ route('department.landing', $department->slug) }}" target="_blank">
                                {{ route('department.landing', $department->slug) }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sub-departments -->
            @if($department->children->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Sub-Departments</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($department->children as $child)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-arrow-return-right text-muted me-2"></i>
                                        {{ $child->name }}
                                    </span>
                                    <a href="{{ route('departments.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Team Members -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Team Members</h6>
                    <span class="badge bg-info">{{ $department->members->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($department->members as $member)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $member->name }}</strong>
                                @if($member->pivot->role)
                                    <br><small class="text-muted">{{ $member->pivot->role }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No members yet</p>
                    @endforelse
                    <a href="{{ route('departments.members', $department) }}" class="btn btn-sm btn-outline-secondary mt-3 w-100">
                        Manage Members
                    </a>
                </div>
            </div>

            <!-- Recent Meetings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Meetings</h6>
                </div>
                <div class="card-body">
                    @forelse($department->meetings as $meeting)
                        <div class="mb-3">
                            <a href="{{ route('meetings.show', $meeting) }}" class="fw-bold">{{ $meeting->title }}</a>
                            <br><small class="text-muted">{{ $meeting->meeting_date->format('M d, Y') }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No meetings yet</p>
                    @endforelse
                    <a href="{{ route('meetings.create', ['department_id' => $department->id]) }}" class="btn btn-sm btn-primary mt-3 w-100">
                        <i class="bi bi-plus-circle me-1"></i>Create Meeting
                    </a>
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
