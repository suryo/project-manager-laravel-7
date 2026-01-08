@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-uppercase">
                    <i class="bi bi-file-earmark-text"></i> {{ $config['name'] }}
                </h2>
                <p class="text-muted mb-0">Ticket: {{ $ticket->ticket_number }} - {{ $ticket->title }}</p>
            </div>
            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-white border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-dark text-white border-bottom border-3 border-dark">
                    <h5 class="mb-0">Complete the Form</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('tickets.documents.store-form', $ticket) }}" method="POST">
                        @csrf
                        <input type="hidden" name="document_type" value="{{ $documentType }}">

                        @foreach($config['form_fields'] ?? [] as $fieldName => $fieldConfig)
                            <div class="mb-4">
                                <label for="{{ $fieldName }}" class="form-label fw-bold">
                                    {{ $fieldConfig['label'] }}
                                    @if($fieldConfig['required'])
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if($fieldConfig['type'] === 'text')
                                    <input 
                                        type="text" 
                                        class="form-control @error($fieldName) is-invalid @enderror" 
                                        id="{{ $fieldName }}" 
                                        name="{{ $fieldName }}" 
                                        value="{{ old($fieldName) }}"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}
                                    >
                                
                                @elseif($fieldConfig['type'] === 'date')
                                    <input 
                                        type="date" 
                                        class="form-control @error($fieldName) is-invalid @enderror" 
                                        id="{{ $fieldName }}" 
                                        name="{{ $fieldName }}" 
                                        value="{{ old($fieldName, date('Y-m-d')) }}"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}
                                    >
                                
                                @elseif($fieldConfig['type'] === 'select')
                                    <select 
                                        class="form-select @error($fieldName) is-invalid @enderror" 
                                        id="{{ $fieldName }}" 
                                        name="{{ $fieldName }}"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}
                                    >
                                        <option value="">Select...</option>
                                        @foreach($fieldConfig['options'] ?? [] as $option)
                                            <option value="{{ $option }}" {{ old($fieldName) == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                
                                @elseif($fieldConfig['type'] === 'textarea')
                                    <textarea 
                                        class="form-control @error($fieldName) is-invalid @enderror" 
                                        id="{{ $fieldName }}" 
                                        name="{{ $fieldName }}" 
                                        rows="5"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}
                                    >{{ old($fieldName) }}</textarea>
                                @endif

                                @error($fieldName)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-white border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary border border-2 border-dark fw-bold" style="box-shadow: 2px 2px 0 #000;">
                                <i class="bi bi-save"></i> Generate PDF & Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-info text-white border-bottom border-3 border-dark">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Information</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">
                        <strong>Document Type:</strong><br>
                        {{ $config['name'] }}
                    </p>
                    <p class="small mb-3">
                        <strong>Input Method:</strong><br>
                        <span class="badge bg-primary">Form Input</span>
                    </p>
                    <p class="small mb-0">
                        <i class="bi bi-lightbulb text-warning"></i> 
                        After submission, a PDF will be automatically generated from your input and will be pending approval.
                    </p>
                </div>
            </div>

            @if($config['has_template'] ?? false)
            <div class="card card-custom border-0 shadow-sm mt-3">
                <div class="card-header bg-success text-white border-bottom border-3 border-dark">
                    <h6 class="mb-0"><i class="bi bi-download"></i> Template</h6>
                </div>
                <div class="card-body text-center">
                    <p class="small mb-3">Download empty template as reference</p>
                    <a href="{{ route('tickets.documents.template', $documentType) }}" class="btn btn-success border border-2 border-dark fw-bold w-100" style="box-shadow: 2px 2px 0 #000;">
                        <i class="bi bi-file-pdf"></i> Download Template
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
