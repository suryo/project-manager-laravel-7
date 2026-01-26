@extends('layouts.app')

@push('styles')
<style>
    /* Fix modal flickering */
    .modal-backdrop {
        opacity: 0.5;
    }
    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out;
    }
    .modal-content {
        border-radius: 0 !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-uppercase letter-spacing-1">User Management</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary border border-2 border-dark fw-bold text-uppercase" style="box-shadow: 4px 4px 0 #000;"><i class="bi bi-person-plus me-1"></i> ADD NEW USER</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-2 border-dark rounded-0 shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-2 border-dark rounded-0 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('users.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-2">SEARCH</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                   placeholder="Search by name or email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-2">ROLE</label>
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                            <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-2">DEPARTMENT</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 border border-2 border-dark fw-bold" style="box-shadow: 3px 3px 0 #000;">
                            <i class="bi bi-funnel me-1"></i>FILTER
                        </button>
                    </div>
                </div>
                @if(request('search') || request('role') || request('department'))
                    <div class="mt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3 border-0">Name</th>
                            <th class="py-3 border-0">Email</th>
                            <th class="py-3 border-0">Role</th>
                            <th class="py-3 border-0">Department</th>
                            <th class="py-3 border-0">Joined</th>
                            <th class="text-end pe-4 py-3 border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $user->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="badge rounded-pill bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'client' ? 'info' : 'secondary') }} border border-1 border-dark" style="box-shadow: 2px 2px 0 #000;">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @forelse($user->departments as $dept)
                                    <span class="badge bg-light text-dark border border-1 border-dark extra-small mb-1">
                                        {{ $dept->name }}
                                    </span>
                                @empty
                                    <span class="text-danger small fw-bold"><i class="bi bi-exclamation-circle me-1"></i>None</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($user->id !== auth()->id())
                                    <!-- Login As Button - Trigger Modal -->
                                    <button class="btn btn-sm btn-warning border border-2 border-dark px-2 py-0 fw-bold login-as-btn" 
                                            style="box-shadow: 2px 2px 0 #000; font-size: 0.75rem;" 
                                            title="Login as this user"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#loginAsModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role }}"
                                            data-user-initial="{{ substr($user->name, 0, 1) }}"
                                            data-login-url="{{ route('users.login-as', $user) }}">
                                        <i class="bi bi-box-arrow-in-right"></i> LOGIN AS
                                    </button>
                                    @endif
                                    
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-white border border-2 border-dark px-2 py-0 fw-bold" style="box-shadow: 2px 2px 0 #000; font-size: 0.75rem;">
                                        EDIT
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-white border border-2 border-dark px-2 py-0 fw-bold text-danger" style="box-shadow: 2px 2px 0 #000; font-size: 0.75rem;" onclick="return confirm('Delete user?')">
                                            DEL
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<!-- Single Login As Modal (Outside Loop) -->
<div class="modal" id="loginAsModal" tabindex="-1" aria-labelledby="loginAsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-3 border-dark" style="box-shadow: 8px 8px 0 #000;">
            <div class="modal-header bg-warning border-bottom border-3 border-dark">
                <h5 class="modal-title fw-bold text-dark" id="loginAsModalLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Login As
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Anda akan login sebagai:</p>
                <div class="alert alert-info border border-2 border-dark mb-3" style="box-shadow: 3px 3px 0 #000;">
                    <div class="d-flex align-items-center">
                        <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;" id="modal-user-initial">
                            
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" id="modal-user-name"></h6>
                            <small class="text-muted" id="modal-user-email"></small><br>
                            <span class="badge mt-1" id="modal-user-role"></span>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning border border-2 border-dark mb-0" style="box-shadow: 3px 3px 0 #000;">
                    <small>
                        <i class="bi bi-info-circle"></i> 
                        <strong>Perhatian:</strong> Anda akan melihat aplikasi dari perspektif user ini. Klik tombol <strong>"Leave Impersonation"</strong> untuk kembali.
                    </small>
                </div>
            </div>
            <div class="modal-footer border-top border-3 border-dark">
                <button type="button" class="btn btn-white border border-2 border-dark fw-bold" data-bs-dismiss="modal" style="box-shadow: 2px 2px 0 #000;">
                    Batal
                </button>
                <form id="login-as-form" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                        <i class="bi bi-box-arrow-in-right"></i> Ya, Login Sebagai User Ini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Login As button clicks
    document.querySelectorAll('.login-as-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userName = this.dataset.userName;
            const userEmail = this.dataset.userEmail;
            const userRole = this.dataset.userRole;
            const userInitial = this.dataset.userInitial;
            const loginUrl = this.dataset.loginUrl;
            
            // Update modal content
            document.getElementById('modal-user-name').textContent = userName;
            document.getElementById('modal-user-email').textContent = userEmail;
            document.getElementById('modal-user-initial').textContent = userInitial;
            
            // Update role badge
            const roleBadge = document.getElementById('modal-user-role');
            roleBadge.textContent = userRole.charAt(0).toUpperCase() + userRole.slice(1);
            roleBadge.className = 'badge mt-1 bg-' + (userRole === 'admin' ? 'danger' : (userRole === 'client' ? 'info' : 'secondary'));
            
            // Update form action
            document.getElementById('login-as-form').action = loginUrl;
        });
    });
});
</script>
@endpush

@endsection
