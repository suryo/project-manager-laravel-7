<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Public Ticket Request - Project Manager</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        
        .request-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .request-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .request-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .request-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .request-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            padding: 30px;
            background: #f8f9fa;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 40px;
            right: 40px;
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s;
        }
        
        .step.active .step-circle {
            background: #667eea;
            border-color: #667eea;
            color: white;
            transform: scale(1.1);
        }
        
        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .step.active .step-label {
            color: #667eea;
            font-weight: 700;
        }
        
        /* Form Sections */
        .form-section {
            display: none;
            padding: 40px;
        }
        
        .form-section.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }
        
        .section-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .method-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .method-option {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .method-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .method-option.active {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .method-icon {
            font-size: 36px;
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .file-list {
            margin-top: 20px;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            padding: 20px 40px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .required-mark {
            color: #dc3545;
            font-weight: bold;
        }

        /* Step Indicator Styles */
        .steps-container {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-top: 30px;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .steps-container::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 4px;
            background: #e9ecef;
            z-index: 0;
            transform: translateY(-50%);
        }

        .step {
            position: relative;
            z-index: 1;
            text-align: center;
            background: white;
            padding: 0 10px;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #e9ecef;
            color: #6c757d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 10px;
            transition: all 0.3s;
            border: 3px solid white;
        }

        .step.active .step-number {
            background: #667eea;
            color: white;
            box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.2);
        }

        .step.completed .step-number {
            background: #198754;
            color: white;
        }

        .step-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #667eea;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .step-label { display: none; }
            .steps-container::before { left: 20px; right: 20px; }
        }
    </style>
</head>
<body>
    <div class="request-container">
        <div class="request-card">
            <!-- Header -->
            <div class="request-header">
                <h1><i class="bi bi-ticket-perforated"></i> Public Ticket Request</h1>
                <p>Submit your IT request - No login required</p>
            </div>

            <!-- Global Error/Success Messages -->
            @if($errors->any())
                <div class="alert alert-danger m-4">
                    <h5><i class="bi bi-exclamation-triangle"></i> Validation Errors</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success m-4">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger m-4">
                    <i class="bi bi-x-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Steps Indicator -->
            <div class="steps-container">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Contact Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Ticket Details</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Documents</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Review</div>
                </div>
            </div>

            <!-- Form -->
            <form id="publicRequestForm" method="POST" action="{{ route('public.ticket-request.submit') }}" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- STEP 1: Contact Information -->
                <div class="form-section active" data-section="1">
                    <h2 class="section-title">Contact Information</h2>
                    <p class="section-subtitle">Please provide your contact details</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="required-mark">*</span></label>
                            <input type="text" class="form-control" name="guest_name" required value="{{ old('guest_name') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address <span class="required-mark">*</span></label>
                            <input type="email" class="form-control" name="guest_email" required value="{{ old('guest_email') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department <span class="required-mark">*</span></label>
                            <input type="text" class="form-control" name="guest_department" required value="{{ old('guest_department') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="guest_phone" value="{{ old('guest_phone') }}">
                        </div>
                    </div>

                <!-- Approvers Moved to Step 4 -->
                </div>

                <!-- STEP 2: Ticket Details -->
                <div class="form-section" data-section="2">
                    <h2 class="section-title">Ticket Details</h2>
                    <p class="section-subtitle">Describe your request</p>

                    <div class="mb-3">
                        <label class="form-label">Request Title <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="title" required value="{{ old('title') }}" placeholder="e.g., Error on Login Page or Need New Report for Sales">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Request Type <span class="required-mark">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="">Select type...</option>
                                <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>Bug Fix</option>
                                <option value="feature" {{ old('type') == 'feature' ? 'selected' : '' }}>New Feature</option>
                                <option value="support" {{ old('type') == 'support' ? 'selected' : '' }}>Support</option>
                                <option value="data_fix" {{ old('type') == 'data_fix' ? 'selected' : '' }}>Data Fix</option>
                                <option value="optimation" {{ old('type') == 'optimation' ? 'selected' : '' }}>Optimation</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority <span class="required-mark">*</span></label>
                            <select class="form-select" name="priority" required>
                                <option value="">Select Priority (Choose Type first)...</option>
                                <!-- Populated dynamically by JS -->
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project/System Name <span class="required-mark">*</span></label>
                            <select class="form-select" name="project_id" required onchange="toggleProjectInput(this)">
                                <option value="">Select project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                                @endforeach
                                <option value="other" {{ old('project_id') == 'other' ? 'selected' : '' }}>Other / Not Listed</option>
                            </select>
                            <input type="text" class="form-control mt-2" name="project_name" id="project_name_input" value="{{ old('project_name') }}" placeholder="Enter Project Name" style="display: {{ old('project_id') == 'other' ? 'block' : 'none' }};">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Deadline <span class="required-mark">*</span></label>
                            <input type="date" class="form-control" name="target_deadline" required value="{{ old('target_deadline') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="required-mark">*</span></label>
                        <textarea class="form-control" name="description" id="descriptionEditor">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- STEP 3: Functional Specifications & Assets -->
                <div class="form-section" data-section="3">
                    <h2 class="section-title">Functional Specifications & Assets</h2>
                    <p class="section-subtitle">Upload supporting documents (mockups, diagrams, references)</p>

                    <div class="file-upload-area" onclick="document.getElementById('functional_spec_files').click()">
                        <i class="bi bi-cloud-upload" style="font-size: 48px; color: #667eea;"></i>
                        <h5 class="mt-3">Click to upload files</h5>
                        <p class="text-muted">PDF, Images, or Documents (Max 10 files, 10MB each)</p>
                        <input type="file" id="functional_spec_files" name="functional_spec_files[]" multiple accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" style="display: none;" onchange="displayFiles(this)">
                    </div>

                    <div id="file-list-container" class="file-list"></div>
                </div>

                <!-- STEP 4: Review & Submit -->
                <div class="form-section" data-section="4">
                    <h2 class="section-title">Review Your Request</h2>
                    <p class="section-subtitle">Please review all information before submitting</p>

                    <div id="review-summary" class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Summary will appear here after filling previous steps
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span>Ticket Approvers <span class="required-mark">*</span></span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addApprover()">
                                <i class="bi bi-plus-lg"></i> Add Approver
                            </button>
                        </label>
                        <p class="text-muted small">Add names of people who need to approve this request (Min. 1 required).</p>
                        
                        <div id="approvers_list">
                            <!-- Dynamic inputs -->
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="accept_terms" id="terms_checkbox" value="1" required>
                        <label class="form-check-label" for="terms_checkbox">
                            I confirm that all information provided is accurate and I understand that this request will be reviewed by the IT department.
                        </label>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="navigation-buttons">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="bi bi-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                        Next <i class="bi bi-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                        <i class="bi bi-check-circle"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <style>
        /* ... (existing styles) ... */
        .note-editor .note-toolbar {
            background: #f8f9fa;
        }
        .note-editor.note-frame {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            box-shadow: none;
        }
        .note-editor.note-frame.focused {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <!-- ... (rest of body) ... -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (Required for Summernote) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <script>
        // Priority Mapping Configuration with Durations (in hours)
        const priorityMapping = {
            'bug': [
                { value: 'very_low', label: 'Very Low (2 weeks)', duration: 336 },
                { value: 'low', label: 'Low (1 week)', duration: 168 },
                { value: 'medium', label: 'Medium (4 days)', duration: 96 },
                { value: 'high', label: 'High (2 days)', duration: 48 },
                { value: 'very_high', label: 'Very High (1 day)', duration: 24 },
                { value: 'urgent', label: 'Urgent (4 hours)', duration: 4 },
                { value: 'super_urgent', label: 'Super Urgent (1 hour)', duration: 1 }
            ],
            'feature': [
                { value: 'very_low', label: 'Very Low (1 month)', duration: 720 },
                { value: 'low', label: 'Low (2 weeks)', duration: 336 },
                { value: 'medium', label: 'Medium (1 week)', duration: 168 },
                { value: 'high', label: 'High (2 days)', duration: 48 },
                { value: 'very_high', label: 'Very High (1 day)', duration: 24 }
            ],
            'support': [
                { value: 'low', label: 'Low (1 week)', duration: 168 },
                { value: 'medium', label: 'Medium (3 days)', duration: 72 },
                { value: 'high', label: 'High (1 day)', duration: 24 }
            ],
            'data_fix': [
                { value: 'low', label: 'Low (1 week)', duration: 168 },
                { value: 'medium', label: 'Medium (3 days)', duration: 72 },
                { value: 'high', label: 'High (1 day)', duration: 24 }
            ],
            'optimation': [
                { value: 'low', label: 'Low (2 weeks)', duration: 336 },
                { value: 'medium', label: 'Medium (1 week)', duration: 168 },
                { value: 'high', label: 'High (3 days)', duration: 72 }
            ]
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Summernote
            $('#descriptionEditor').summernote({
                placeholder: 'Describe your request in detail... (You can paste images here)',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Load saved data logic ...
            
            // Priority Logic
            const typeSelect = document.querySelector('select[name="type"]');
            const prioritySelect = document.querySelector('select[name="priority"]');
            
            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    updatePriorityOptions(this.value);
                });
                
                // Trigger on load if value exists
                if (typeSelect.value) {
                    updatePriorityOptions(typeSelect.value, '{{ old("priority") }}');
                }
            }
        });

        function updatePriorityOptions(type, selectedValue = null) {
            const prioritySelect = document.querySelector('select[name="priority"]');
            prioritySelect.innerHTML = '<option value="">Select priority...</option>';
            
            if (type && priorityMapping[type]) {
                priorityMapping[type].forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.value;
                    opt.textContent = option.label;
                if (selectedValue === option.value) {
                        opt.selected = true;
                    }
                    opt.dataset.duration = option.duration; // Store duration
                    prioritySelect.appendChild(opt);
                });
            }
        }

        // Calculate and set deadline based on priority
        function updateDeadline() {
            const prioritySelect = document.querySelector('select[name="priority"]');
            const selectedOption = prioritySelect.options[prioritySelect.selectedIndex];
            const durationHours = selectedOption ? parseInt(selectedOption.dataset.duration) : 0;
            const deadlineInput = document.querySelector('input[name="target_deadline"]');

            if (durationHours > 0 && deadlineInput) {
                const now = new Date();
                // Add duration hours to current time
                const deadlineDate = new Date(now.getTime() + (durationHours * 60 * 60 * 1000));
                
                // Format to YYYY-MM-DD
                const yyyy = deadlineDate.getFullYear();
                const mm = String(deadlineDate.getMonth() + 1).padStart(2, '0');
                const dd = String(deadlineDate.getDate()).padStart(2, '0');
                
                const formattedDate = `${yyyy}-${mm}-${dd}`;
                
                // Set the minimum allowed date
                deadlineInput.min = formattedDate;
                
                // Update the value if it's empty or less than the minimum
                if (!deadlineInput.value || deadlineInput.value < formattedDate) {
                    deadlineInput.value = formattedDate;
                }
                
                console.log(`Deadline updated: Min set to ${formattedDate}, Value set to ${deadlineInput.value}`);
            }
        }

        function toggleProjectInput(selectElement) {
            const input = document.getElementById('project_name_input');
            if (selectElement.value === 'other') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        }
        
        let currentStep = 1;
        const totalSteps = 4; // Reduced from 5

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved data from localStorage
            loadFormData();
            
            // Set default method active states
            const formMethodInput = document.getElementById('form_method');
            if (formMethodInput) selectMethod('form_method', formMethodInput.value);
            
            const reqMethodInput = document.getElementById('requirements_method');
            if (reqMethodInput) selectMethod('requirements_method', reqMethodInput.value);
            
            // Priority Logic
            const typeSelect = document.querySelector('select[name="type"]');
            const prioritySelect = document.querySelector('select[name="priority"]');
            
            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    updatePriorityOptions(this.value);
                    // Reset deadline or keep? Let's reset priority selection which will handle it
                });
                
                // Trigger on load if value exists
                if (typeSelect.value) {
                    updatePriorityOptions(typeSelect.value, '{{ old("priority") }}');
                }
            }

            if (prioritySelect) {
                prioritySelect.addEventListener('change', updateDeadline);
            }
            
            // Auto-save on input change
            document.getElementById('publicRequestForm').addEventListener('input', debounce(saveFormData, 500));
            
            // Add form submit handler
            document.getElementById('publicRequestForm').addEventListener('submit', function(e) {
                // Prevent default first to handle validation manually
                // We use novalidate on form, so we must check everything here
                
                let isValid = true;
                let errorMessage = '';

                // 1. Validate Terms & Conditions
                const termsCheckbox = document.getElementById('terms_checkbox');
                if (!termsCheckbox.checked) {
                    isValid = false;
                    errorMessage = 'Please accept the terms and conditions.';
                    termsCheckbox.focus();
                }

                // 2. Validate Approvers (Required in Step 4)
                if (isValid) {
                    const approverInputs = document.querySelectorAll('input[name^="approvers"]');
                    if (approverInputs.length === 0) {
                        isValid = false;
                        errorMessage = 'Please add at least one ticket approver.';
                    } else {
                        // Check if approver names are filled
                        let allFilled = true;
                        approverInputs.forEach(input => {
                            if (!input.value.trim()) {
                                allFilled = false;
                                input.classList.add('is-invalid');
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });
                        
                        if (!allFilled) {
                            isValid = false;
                            errorMessage = 'Please fill in all approver names.';
                        }
                    }
                }

                // 3. Validate Description (Summernote)
                if (isValid) {
                    if ($('#descriptionEditor').summernote('isEmpty')) {
                        isValid = false;
                        errorMessage = 'Please provide a description for your request.';
                        // Go to step 2 if we need to show error
                        if (currentStep !== 2) {
                            changeStep(2 - currentStep);
                        }
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    if (errorMessage) alert(errorMessage);
                    return false;
                }

                console.log('Form submitting...');
                
                // Show loading on submit button
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
                
                // Log form data for debugging
                const formData = new FormData(this);
                // ... logging ...
            });
                

        });

        function changeStep(direction) {
            // Validate current step before proceeding
            if (direction > 0 && !validateCurrentStep()) {
                return;
            }

            const newStep = currentStep + direction;
            
            if (newStep < 1 || newStep > totalSteps) return;

            // Hide current section
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });

            // Update step indicators
            document.querySelectorAll('.step').forEach(step => {
                const stepNumber = parseInt(step.dataset.step);
                step.classList.remove('active');
                if (stepNumber < newStep) {
                    step.classList.add('completed');
                } else {
                    step.classList.remove('completed');
                }
            });

            // Show new section
            document.querySelector(`.form-section[data-section="${newStep}"]`).classList.add('active');
            document.querySelector(`.step[data-step="${newStep}"]`).classList.add('active');

            currentStep = newStep;

            // Update navigation buttons
            document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';

            // Generate review summary on last step
            if (currentStep === totalSteps) {
                generateReviewSummary();
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateCurrentStep() {
            const currentSection = document.querySelector(`.form-section[data-section="${currentStep}"]`);
            const requiredFields = currentSection.querySelectorAll('[required]');
            
            for (let field of requiredFields) {
                // Skip hidden fields
                if (field.offsetParent === null) continue;
                
                if (!field.value || field.value.trim() === '') {
                    field.focus();
                    field.classList.add('is-invalid');
                    alert('Please fill in all required fields marked with *');
                    return false;
                }
                field.classList.remove('is-invalid');
            }

            // Specific validation for Review step (Step 4)
            if (currentStep === 4) {
                const approverInputs = currentSection.querySelectorAll('input[name^="approvers"]');
                if (approverInputs.length === 0) {
                    alert('Please add at least one ticket approver.');
                    return false;
                }
            }
            
            return true;
        }

        function selectMethod(inputName, method) {
            document.getElementById(inputName).value = method;
            
            // Update active state
            const parentSection = document.getElementById(inputName).closest('.form-section');
            parentSection.querySelectorAll('.method-option').forEach(option => {
                option.classList.remove('active');
            });
            parentSection.querySelector(`.method-option[data-method="${method}"]`).classList.add('active');

            // Show/hide appropriate fields
            if (inputName === 'form_method') {
                document.getElementById('inline_form_fields').style.display = method === 'inline' ? 'block' : 'none';
                document.getElementById('upload_form_field').style.display = method === 'upload' ? 'block' : 'none';
            } else if (inputName === 'requirements_method') {
                document.getElementById('inline_requirements_fields').style.display = method === 'inline' ? 'block' : 'none';
                document.getElementById('upload_requirements_field').style.display = method === 'upload' ? 'block' : 'none';
            }
        }

        function displayFiles(input) {
            const container = document.getElementById('file-list-container');
            container.innerHTML = '';
            
            Array.from(input.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <div>
                        <i class="bi bi-file-earmark"></i>
                        <strong>${file.name}</strong>
                        <small class="text-muted">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                container.appendChild(fileItem);
            });
        }

        function removeFile(index) {
            const input = document.getElementById('functional_spec_files');
            const dt = new DataTransfer();
            
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) dt.items.add(file);
            });
            
            input.files = dt.files;
            displayFiles(input);
        }

        function generateReviewSummary() {
            const form = document.getElementById('publicRequestForm');
            const formData = new FormData(form);
            
            let summary = '<h5>Request Summary</h5><hr>';
            
            summary += '<strong>Contact Information:</strong><br>';
            summary += `Name: ${formData.get('guest_name')}<br>`;
            summary += `Email: ${formData.get('guest_email')}<br>`;
            summary += `Department: ${formData.get('guest_department')}<br><br>`;
            
            summary += '<strong>Ticket Details:</strong><br>';
            summary += `Title: ${formData.get('title')}<br>`;
            summary += `Type: ${formData.get('type')}<br>`;
            summary += `Priority: ${formData.get('priority')}<br><br>`;
            
            summary += '<strong>Documents:</strong><br>';
            
            const files = document.getElementById('functional_spec_files').files;
            if (files.length > 0) {
                summary += `Functional Specs: ${files.length} file(s) uploaded<br>`;
            } else {
                summary += 'No files uploaded<br>';
            }
            
            document.getElementById('review-summary').innerHTML = summary;
        }

        // Auto-save functionality
        function saveFormData() {
            const form = document.getElementById('publicRequestForm');
            const formData = new FormData(form);
            const data = {};
            
            formData.forEach((value, key) => {
                // Don't save file inputs
                if (!key.includes('file')) {
                    data[key] = value;
                }
            });
            
            localStorage.setItem('publicRequestFormData', JSON.stringify(data));
        }

        function loadFormData() {
            const saved = localStorage.getItem('publicRequestFormData');
            if (saved) {
                const data = JSON.parse(saved);
                Object.keys(data).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input && !input.files) {
                        input.value = data[key];
                    }
                });
            }
        }

        // Clear saved data after successful submission
        window.addEventListener('load', function() {
            if (window.location.href.includes('success')) {
                localStorage.removeItem('publicRequestFormData');
            }
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function toggleProjectInput(value) {
            const input = document.getElementById('project_name_input');
            if (value === 'other') {
                input.style.display = 'block';
                input.required = true;
                input.focus();
            } else {
                input.style.display = 'none';
                input.required = false;
                input.value = ''; // Clear value if hidden
            }
        }

        let approverCount = 0;

        function addApprover() {
            const container = document.getElementById('approvers_list');
            const index = approverCount++;
            
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.id = `approver_${index}`;
            div.innerHTML = `
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="approvers[${index}][name]" placeholder="Approver Name" required>
                <button type="button" class="btn btn-outline-danger" onclick="removeApprover(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(div);
            
            // Focus new input
            div.querySelector('input').focus();
        }

        function removeApprover(index) {
            const element = document.getElementById(`approver_${index}`);
            if (element) {
                element.remove();
            }
        }
    </script>
</body>
</html>
