@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meetings</h1>
        <a href="{{ route('meetings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Meeting
        </a>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('meetings.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="department_id" class="form-label">Filter by Department</label>
                    <select class="form-control" id="department_id" name="department_id" onchange="this.form.submit()">
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Meetings List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Meetings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Attendees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetings as $meeting)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $meeting->title }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $meeting->department->name }}</span>
                                </td>
                                <td>{{ $meeting->meeting_date->format('M d, Y H:i') }}</td>
                                <td>{{ $meeting->location ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $meeting->attendances->count() }} attendees</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this meeting?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No meetings found. Create your first meeting!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $meetings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
