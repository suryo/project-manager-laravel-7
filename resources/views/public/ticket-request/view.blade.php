<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Request - Project Manager</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            max-width: 700px;
            width: 100%;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header removed as requested, but keeping CSS classes if needed for future or consistency */
        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 50px 30px;
            text-align: center;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: checkmark 0.8s ease-in-out;
        }
        
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .success-icon i {
            font-size: 50px;
            color: #28a745;
        }
        
        .success-body {
            padding: 40px;
        }
        
        .tracking-box {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        
        .tracking-token {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
        }
        
        .copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .copy-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        
        .info-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-outline:hover {
            background: #f8f9ff;
            color: #5568d3;
            border-color: #5568d3;
        }
        
        .next-steps {
            background: #e7f3ff;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .next-steps h5 {
            color: #0066cc;
            margin-bottom: 15px;
        }
        
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .next-steps li {
            margin-bottom: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <!-- Header Removed -->
            
            <div class="success-body">
                <div class="tracking-box">
                    <i class="bi bi-ticket-perforated" style="font-size: 40px; color: #667eea;"></i>
                    <h4 class="mt-3">Tracking Number</h4>
                    <div class="tracking-token" id="trackingToken">{{ $ticket->tracking_token }}</div>
                    <button class="copy-btn" onclick="copyToken()">
                        <i class="bi bi-clipboard"></i> Copy Number
                    </button>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Ticket Number</div>
                        <div class="info-value">{{ $ticket->ticket_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Request Title</div>
                        <div class="info-value">{{ $ticket->title }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Priority</div>
                        <div class="info-value">
                            @php
                                $priorityColors = [
                                    'very_low' => '#28a745',
                                    'low' => '#28a745',
                                    'medium' => '#ffc107',
                                    'high' => '#fd7e14',
                                    'very_high' => '#dc3545',
                                    'urgent' => '#dc3545',
                                    'super_urgent' => '#b02a37'
                                ];
                            @endphp
                            <span style="color: {{ $priorityColors[$ticket->priority] ?? '#6c757d' }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge bg-warning text-dark">Pending Review</span>
                        </div>
                    </div>
                </div>

                <div class="next-steps d-none">
                    <h5><i class="bi bi-list-check"></i> What Happens Next?</h5>
                    <ul>
                        <li><strong>Email Confirmation:</strong> You'll receive a confirmation email at <strong>{{ $ticket->guest_email }}</strong></li>
                        <li><strong>Review Process:</strong> Our team will review your request within 1-2 business days</li>
                        <li><strong>Status Updates:</strong> You'll be notified via email about any status changes</li>
                        <li><strong>Track Progress:</strong> Use your tracking number to check real-time status</li>
                    </ul>
                </div>

                <!-- TESTING ONLY: Display Approval Links -->
                @if($ticket->approvals->count() > 0)
                <div class="alert alert-warning mt-4 text-start">
                    <h5><i class="bi bi-cone-striped"></i>Approval Links</h5>
                    <p class="mb-2 small">Since emails are not configured, use these links to approve or reject your request:</p>
                    <ul class="list-group">
                        @foreach($ticket->approvals as $approval)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $approval->approver_name }}</strong>
                                    <span class="badge bg-{{ $approval->status == 'approved' ? 'success' : ($approval->status == 'rejected' ? 'danger' : 'warning') }} ms-2">{{ ucfirst($approval->status) }}</span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ route('public.approval.show', $approval->approval_token) }}', this)">
                                        <i class="bi bi-clipboard"></i> Copy Link
                                    </button>
                                    <a href="{{ route('public.approval.show', $approval->approval_token) }}" target="_blank" class="btn btn-outline-primary">
                                        Open Link <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="action-buttons">
                    <a href="{{ route('public.ticket-request.status', $ticket->tracking_token) }}" class="btn btn-primary">
                        <i class="bi bi-eye"></i> Track Status
                    </a>
                    <a href="{{ route('public.ticket-request') }}" class="btn-outline">
                        <i class="bi bi-plus-circle"></i> Submit Another Request
                    </a>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-shield-check"></i> 
                        Your information is secure and will only be used for processing this request
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text, btnElement) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btnElement.innerHTML;
                btnElement.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                btnElement.classList.replace('btn-outline-secondary', 'btn-success');
                
                setTimeout(() => {
                    btnElement.innerHTML = originalHtml;
                    btnElement.classList.replace('btn-success', 'btn-outline-secondary');
                }, 2000);
            });
        }

        function copyToken() {
            const token = document.getElementById('trackingToken').innerText;
            navigator.clipboard.writeText(token).then(() => {
                const btn = document.querySelector('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                btn.style.background = '#28a745';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '#667eea';
                }, 2000);
            });
        }
    </script>
</body>
</html>
