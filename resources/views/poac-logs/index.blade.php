@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="bi bi-journal-text text-primary me-2"></i>POAC Logs
                    </h1>
                    <p class="text-muted mb-0">View all POAC activity logs from the system</p>
                </div>
                <a href="{{ route('poac-logs.report') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <form action="{{ route('poac-logs.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-2">SEARCH</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                   placeholder="Search title or description..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted mb-2">PHASE</label>
                        <select name="phase" class="form-select">
                            <option value="">All Phases</option>
                            <option value="Planning" {{ request('phase') == 'Planning' ? 'selected' : '' }}>📋 Planning</option>
                            <option value="Organizing" {{ request('phase') == 'Organizing' ? 'selected' : '' }}>🗂️ Organizing</option>
                            <option value="Actuating" {{ request('phase') == 'Actuating' ? 'selected' : '' }}>⚡ Actuating</option>
                            <option value="Controlling" {{ request('phase') == 'Controlling' ? 'selected' : '' }}>📊 Controlling</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted mb-2">TYPE</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="App\Models\Project" {{ request('type') == 'App\Models\Project' ? 'selected' : '' }}>📁 Project</option>
                            <option value="App\Models\Task" {{ request('type') == 'App\Models\Task' ? 'selected' : '' }}>✓ Task</option>
                            <option value="App\Models\Ticket" {{ request('type') == 'App\Models\Ticket' ? 'selected' : '' }}>🎫 Ticket</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted mb-2">USER</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Phase</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 py-3">
                                    @php
                                        $phaseColors = [
                                            'Planning' => 'primary',
                                            'Organizing' => 'info',
                                            'Actuating' => 'warning',
                                            'Controlling' => 'success'
                                        ];
                                        $phaseIcons = [
                                            'Planning' => '📋',
                                            'Organizing' => '🗂️',
                                            'Actuating' => '⚡',
                                            'Controlling' => '📊'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $phaseColors[$log->phase] ?? 'secondary' }}">
                                        {{ $phaseIcons[$log->phase] ?? '' }} {{ $log->phase }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <strong>{{ $log->title }}</strong>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeLabel = '';
                                        $typeIcon = '';
                                        if (str_contains($log->poacable_type, 'Project')) {
                                            $typeLabel = 'Project';
                                            $typeIcon = '📁';
                                        } elseif (str_contains($log->poacable_type, 'Task')) {
                                            $typeLabel = 'Task';
                                            $typeIcon = '✓';
                                        } elseif (str_contains($log->poacable_type, 'Ticket')) {
                                            $typeLabel = 'Ticket';
                                            $typeIcon = '🎫';
                                        }
                                    @endphp
                                    <span class="badge bg-secondary">{{ $typeIcon }} {{ $typeLabel }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->poacable)
                                        {{ $log->poacable->title ?? $log->poacable->name ?? 'N/A' }}
                                    @else
                                        <em class="text-muted">Deleted</em>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->user)
                                        <i class="bi bi-person-circle"></i> {{ $log->user->name }}
                                    @else
                                        <em class="text-muted">Unknown</em>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewLogDetail({{ $log->id }})" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted opacity-50"></i>
                                    <p class="text-muted mt-3">No POAC logs found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- View Log Detail Modal -->
<div class="modal fade" id="viewLogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLogTitle">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">PHASE</label>
                    <p id="viewLogPhase"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">TYPE & ITEM</label>
                    <p id="viewLogType"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">DESCRIPTION</label>
                    <div id="viewLogDescription" class="border rounded p-3 bg-light"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">USER</label>
                        <p id="viewLogUser"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">DATE</label>
                        <p id="viewLogDate"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var logsData = @json($logs->keyBy('id'));
    
    function viewLogDetail(logId) {
        var log = logsData[logId];
        if (!log) return;
        
        var phaseIcons = {
            'Planning': '📋',
            'Organizing': '🗂️',
            'Actuating': '⚡',
            'Controlling': '📊'
        };
        
        var typeLabel = '';
        var typeIcon = '';
        if (log.poacable_type.includes('Project')) {
            typeLabel = 'Project';
            typeIcon = '📁';
        } else if (log.poacable_type.includes('Task')) {
            typeLabel = 'Task';
            typeIcon = '✓';
        } else if (log.poacable_type.includes('Ticket')) {
            typeLabel = 'Ticket';
            typeIcon = '🎫';
        }
        
        var itemName = log.poacable ? (log.poacable.title || log.poacable.name || 'N/A') : 'Deleted';
        
        document.getElementById('viewLogTitle').textContent = log.title;
        document.getElementById('viewLogPhase').innerHTML = '<span class="badge bg-primary">' + phaseIcons[log.phase] + ' ' + log.phase + '</span>';
        document.getElementById('viewLogType').textContent = typeIcon + ' ' + typeLabel + ': ' + itemName;
        document.getElementById('viewLogDescription').innerHTML = log.description || '<em class="text-muted">No description</em>';
        document.getElementById('viewLogUser').textContent = log.user ? log.user.name : 'Unknown';
        document.getElementById('viewLogDate').textContent = new Date(log.created_at).toLocaleString();
        
        new bootstrap.Modal(document.getElementById('viewLogModal')).show();
    }
</script>
@endpush
