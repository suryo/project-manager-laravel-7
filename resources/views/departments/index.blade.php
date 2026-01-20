@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Departments</h1>
        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>New Department
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Parent Department</th>
                                <th>Description</th>
                                <th>Members</th>
                                <th>Public Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">
                                    @if($department->parent_id)
                                        <i class="bi bi-arrow-return-right text-muted me-1"></i>
                                    @endif
                                    {{ $department->name }}
                                </td>
                                <td>
                                    @if($department->parent)
                                        <span class="badge bg-secondary">{{ $department->parent->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit(strip_tags($department->description), 100) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $department->members->count() }} members</span>
                                </td>
                                <td>
                                    @if($department->slug)
                                        <a href="{{ route('department.landing', $department->slug) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Visit Page
                                        </a>
                                    @else
                                        <span class="text-muted small">No link</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('departments.members', $department) }}" class="btn btn-sm btn-secondary" title="Manage Members">
                                            <i class="bi bi-people"></i>
                                        </a>
                                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
