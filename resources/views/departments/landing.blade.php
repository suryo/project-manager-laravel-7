@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header/Hero Section for Department --}}
            <div class="text-center mb-5">
                <div class="mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
                        <i class="bi bi-building me-1"></i> Department Portal
                    </span>
                </div>
                <h1 class="display-4 fw-bold text-dark mb-3">{{ $department->name }}</h1>
                <div class="d-flex justify-content-center">
                    <div style="width: 60px; height: 4px; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></div>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden glass-card">
                <div class="card-body p-5">
                    
                    {{-- Description --}}
                    <div class="mb-5">
                        <h4 class="fw-bold mb-3 text-secondary">
                            <i class="bi bi-info-circle me-2"></i>About Us
                        </h4>
                        <div class="ql-snow">
                            <div class="ql-editor" style="padding: 0; min-height: auto;">
                                @if($department->description)
                                    {!! $department->description !!}
                                @else
                                    <em class="text-muted">No description currently available for this department.</em>
                                @endif
                            </div>
                        </div>
                    </div>

                    @auth
                    {{-- Team Members Section (Authenticated Only) --}}
                    <hr class="my-5 border-light">
                    
                    <div class="mb-5">
                        <h4 class="fw-bold mb-3 text-secondary">
                            <i class="bi bi-people me-2"></i>Team Members
                        </h4>
                        @if($department->members && $department->members->count() > 0)
                            <div class="row">
                                @foreach($department->members as $member)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-1">{{ $member->name }}</h6>
                                                @if($member->pivot->role)
                                                    <span class="badge bg-info">{{ $member->pivot->role }}</span>
                                                @endif
                                                <p class="text-muted small mb-0 mt-2">{{ $member->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No team members listed yet.</p>
                        @endif
                    </div>

                    {{-- Meetings Section (Authenticated Only) --}}
                    <hr class="my-5 border-light">
                    
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0 text-secondary">
                                <i class="bi bi-calendar-event me-2"></i>Recent Meetings
                            </h4>
                            <a href="{{ route('department.meeting.create', $department->slug) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-plus-circle me-1"></i>Create Meeting
                            </a>
                        </div>
                        
                        @if($department->meetings && $department->meetings->count() > 0)
                            <div class="list-group">
                                @foreach($department->meetings as $meeting)
                                    <a href="{{ route('department.meeting.show', [$department->slug, $meeting->id]) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 fw-bold">{{ $meeting->title }}</h6>
                                            <small class="text-muted">{{ $meeting->meeting_date->format('M d, Y') }}</small>
                                        </div>
                                        @if($meeting->description)
                                            <p class="mb-1 text-muted small">{{ Str::limit($meeting->description, 100) }}</p>
                                        @endif
                                        @if($meeting->location)
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $meeting->location }}
                                            </small>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('meetings.index', ['department_id' => $department->id]) }}" class="btn btn-sm btn-outline-secondary">
                                    View All Meetings
                                </a>
                            </div>
                        @else
                            <p class="text-muted">No meetings scheduled yet.</p>
                        @endif
                    </div>

                    {{-- Projects Section (Authenticated Only) --}}
                    <hr class="my-5 border-light">
                    
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0 text-secondary">
                                <i class="bi bi-kanban me-2"></i>Recent Projects
                            </h4>
                            {{-- Optional: Link to create project if needed, or view all --}}
                        </div>
                        
                        @if(isset($department->recent_projects) && $department->recent_projects->count() > 0)
                            <div class="row">
                                @foreach($department->recent_projects as $project)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="fw-bold mb-0 text-primary">
                                                        <a href="{{ route('department.projects.show', ['slug' => $department->slug, 'projectSlug' => $project->slug]) }}" class="text-decoration-none stretched-link">{{ $project->title }}</a>
                                                    </h6>
                                                    @if($project->status)
                                                        <span class="badge rounded-pill" style="background-color: {{ $project->status->color }}; color: #fff;">
                                                            {{ $project->status->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($project->description)
                                                    <p class="text-muted small mb-3 text-truncate-2">{{ Str::limit($project->description, 80) }}</p>
                                                @endif
                                                
                                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>{{ $project->user->name ?? 'Unknown' }}
                                                    </small>
                                                    @if($project->end_date)
                                                        <small class="{{ $project->end_date->isPast() ? 'text-danger' : 'text-success' }}">
                                                            <i class="bi bi-calendar-check me-1"></i>Due: {{ $project->end_date->format('M d') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border-0 shadow-sm">
                                <i class="bi bi-info-circle me-2"></i>No active projects found for this department's team.
                            </div>
                        @endif
                    </div>
                    @endauth

                    <hr class="my-5 border-light">

                    {{-- Actions / Ticket Request --}}
                    <div class="text-center">
                        <h4 class="fw-bold mb-4">Need Assistance?</h4>
                        <p class="text-muted mb-4">Submit a ticket directly to {{ $department->name }} team.</p>
                        
                        <a href="{{ route('public.ticket-request', ['department_id' => $department->id]) }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm hover-scale">
                            <i class="bi bi-ticket-perforated me-2"></i>Submit Ticket
                        </a>
                        
                        @guest
                        <div class="mt-4">
                            <p class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                <a href="{{ route('login') }}" class="text-decoration-none">Login</a> to view team members and meetings
                            </p>
                        </div>
                        @endguest
                        
                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="text-decoration-none text-muted small">
                                <i class="bi bi-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Specific styles for this landing page if needed, normally inherited from guest */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-3px);
    }
</style>
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Override ql-editor default fonts to match theme */
    .ql-editor { font-family: inherit; }
    .ql-editor p { margin-bottom: 1rem; }
    .ql-editor ol, .ql-editor ul { padding-left: 1.5rem; }
</style>
@endpush
@endsection
