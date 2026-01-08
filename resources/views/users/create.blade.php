@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 border-bottom border-2 border-dark pt-4 px-4 pb-3">
                    <h4 class="fw-bold mb-0 text-uppercase letter-spacing-1">Add New User</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label text-uppercase small fw-900">Name</label>
                            <input id="name" type="text" class="form-control form-control-lg border-2 border-dark rounded-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-uppercase small fw-900">Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg border-2 border-dark rounded-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label text-uppercase small fw-900">Role</label>
                            <select id="role" class="form-select form-select-lg border-2 border-dark rounded-0 @error('role') is-invalid @enderror" name="role" required>
                                <option value="" disabled selected>Select Role</option>
                                <option value="user">User</option>
                                <option value="client">Client</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-uppercase small fw-900">Password</label>
                            <input id="password" type="password" class="form-control form-control-lg border-2 border-dark rounded-0 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label text-uppercase small fw-900">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control form-control-lg border-2 border-dark rounded-0" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="d-flex justify-content-end gap-3 align-items-center">
                             <a href="{{ route('users.index') }}" class="text-dark fw-bold text-decoration-none text-uppercase small">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 border-2 border-dark fw-bold text-uppercase" style="box-shadow: 4px 4px 0 #000;">
                                CREATE USER
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
