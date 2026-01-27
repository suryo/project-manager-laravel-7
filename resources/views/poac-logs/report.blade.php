@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Generate POAC Report
                    </h1>
                    <p class="text-muted mb-0">Create text-based activity reports for your POAC logs</p>
                </div>
                <a href="{{ route('poac-logs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Logs
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Report Form -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Report Settings</h5>
                </div>
                <div class="card-body">
                    <form id="reportForm">
                        @csrf
                        
                        <!-- Quick Select Buttons -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Quick Select</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="selectReportType('today')">
                                    📅 Today
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectReportType('week')">
                                    📆 This Week
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectReportType('month')">
                                    📊 This Month
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectReportType('year')">
                                    📈 This Year
                                </button>
                            </div>
                        </div>

                        <hr>

                        <!-- Custom Date Range -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Custom Date Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100" onclick="selectReportType('custom')">
                                Apply Custom Range
                            </button>
                        </div>

                        <hr>

                        <!-- Specific Month -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Specific Month</label>
                            <div class="row g-2">
                                <div class="col-7">
                                    <select class="form-select" id="month" name="month">
                                        <option value="">Select Month</option>
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <input type="number" class="form-control" id="month_year" name="year" placeholder="Year" min="2020" max="2100" value="{{ date('Y') }}">
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100" onclick="selectReportType('month')">
                                Apply Month
                            </button>
                        </div>

                        <hr>

                        <!-- Specific Year -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Specific Year</label>
                            <input type="number" class="form-control" id="year_input" name="year" placeholder="Enter year" min="2020" max="2100" value="{{ date('Y') }}">
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100" onclick="selectReportType('year')">
                                Apply Year
                            </button>
                        </div>

                        <input type="hidden" id="report_type" name="report_type" value="today">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Report Output -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Generated Report</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyReport()" id="copyBtn" style="display: none;">
                        <i class="bi bi-clipboard me-1"></i>Copy to Clipboard
                    </button>
                </div>
                <div class="card-body">
                    <div id="reportPlaceholder" class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-text display-1 opacity-25"></i>
                        <p class="mt-3">Select report settings and click "Generate Report" to view your POAC activity report</p>
                    </div>
                    <textarea id="reportOutput" class="form-control font-monospace" rows="25" style="display: none; white-space: pre-wrap; font-size: 0.9rem;" readonly></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectReportType(type) {
        document.getElementById('report_type').value = type;
        
        // Highlight selected button
        document.querySelectorAll('.btn-outline-primary').forEach(btn => {
            btn.classList.remove('active');
        });
    }
    
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Show loading
        document.getElementById('reportPlaceholder').innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">Generating report...</p>';
        document.getElementById('reportPlaceholder').style.display = 'block';
        document.getElementById('reportOutput').style.display = 'none';
        document.getElementById('copyBtn').style.display = 'none';
        
        fetch('{{ route("poac-logs.generate-report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById('reportPlaceholder').style.display = 'none';
                document.getElementById('reportOutput').style.display = 'block';
                document.getElementById('reportOutput').value = result.report;
                document.getElementById('copyBtn').style.display = 'block';
                
                // Show success message
                if (result.count === 0) {
                    alert('Report generated successfully, but no logs found for the selected period.');
                }
            } else {
                alert('Error generating report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reportPlaceholder').innerHTML = '<i class="bi bi-exclamation-triangle text-danger display-4"></i><p class="mt-3 text-danger">Error generating report. Please try again.</p>';
        });
    });
    
    function copyReport() {
        const reportText = document.getElementById('reportOutput');
        reportText.select();
        document.execCommand('copy');
        
        // Change button text temporarily
        const btn = document.getElementById('copyBtn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }
</script>
@endpush
