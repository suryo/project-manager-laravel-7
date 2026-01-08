@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Manage Statuses</h2>
        <a href="{{ route('statuses.create') }}" class="btn btn-primary rounded-pill shadow-sm"><i class="bi bi-plus-lg"></i> Add New Status</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">Name</th>
                            <th class="px-4 py-3 border-0">Color (Preview)</th>
                            <th class="px-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statuses as $status)
                        <tr>
                            <td class="px-4 py-3 fw-bold">{{ $status->name }}</td>
                            <td class="px-4 py-3">
                                <span class="badge rounded-pill bg-{{ $status->color }}">{{ $status->name }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('statuses.edit', $status) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                <form action="{{ route('statuses.destroy', $status) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? Projects using this status will have no status.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">No statuses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
