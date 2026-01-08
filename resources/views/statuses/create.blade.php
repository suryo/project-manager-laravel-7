@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold text-primary mb-0">Create New Status</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('statuses.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label text-muted small fw-bold">Status Name</label>
                            <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="e.g. On Hold">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Color Badge</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'] as $color)
                                    <div>
                                        <input type="radio" class="btn-check" name="color" id="color-{{ $color }}" value="{{ $color }}" {{ old('color') == $color ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-{{ $color }} rounded-pill px-3" for="color-{{ $color }}">{{ ucfirst($color) }}</label>
                                    </div>
                                @endforeach
                            </div>
                             @error('color')
                                <span class="text-danger small mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                             <a href="{{ route('statuses.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                Create Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
