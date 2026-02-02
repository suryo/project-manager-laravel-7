<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>My POAC Activity</h5>
    </div>
    <div class="card-body p-0">
        @if($poacLogs->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($poacLogs as $log)
                    <div class="list-group-item border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
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
                            <span class="badge bg-{{ $phaseColors[$log->phase] ?? 'secondary' }} rounded-pill">
                                {{ $phaseIcons[$log->phase] ?? '' }} {{ $log->phase }}
                            </span>
                            <small class="text-muted x-small">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        <h6 class="mb-1 fw-bold small">{{ $log->title }}</h6>
                        @if($log->poacable)
                            <small class="text-muted d-block mb-2 font-xs">
                                <i class="bi bi-check-circle-fill text-success small"></i> Task: {{ $log->poacable->title }}
                            </small>
                        @endif
                        <p class="mb-0 small text-muted lh-sm">{{ Str::limit(strip_tags($log->description), 80) }}</p>
                    </div>
                @endforeach
            </div>
            @if(Auth::user()->role === 'admin')
                <div class="card-footer bg-white text-center py-2">
                    <a href="{{ route('poac-logs.index') }}" class="btn btn-sm btn-link text-decoration-none">
                        View All Logs <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif
        @else
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox display-4 opacity-25"></i>
                <p class="mt-3 mb-0">No POAC activity yet</p>
            </div>
        @endif
    </div>
</div>
