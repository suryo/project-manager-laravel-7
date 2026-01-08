<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Processed - Project Manager</title>
    
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
        
        .card-container {
            max-width: 600px;
            width: 100%;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .card-header {
            background: {{ $approval->status === 'approved' ? '#28a745' : '#dc3545' }};
            color: white;
            padding: 40px 30px;
            text-align: center;
            border: none;
        }
        
        .icon-circle {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: {{ $approval->status === 'approved' ? '#28a745' : '#dc3545' }};
        }
        
        .card-body {
            padding: 40px;
            text-align: center;
        }

        .details-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="card">
            <div class="card-header">
                <div class="icon-circle">
                    @if($approval->status === 'approved')
                        <i class="bi bi-check-lg"></i>
                    @else
                        <i class="bi bi-x-lg"></i>
                    @endif
                </div>
                <h2 class="mb-0">
                    @if($approval->status === 'approved')
                        Request Approved
                    @else
                        Request Rejected
                    @endif
                </h2>
            </div>
            <div class="card-body">
                <p class="lead text-muted mb-4">
                    This request has already been processed by <strong>{{ $approval->approver_name }}</strong>.
                </p>
                
                <div class="details-box">
                    <div class="mb-2">
                        <small class="text-muted text-uppercase fw-bold">Processed On</small>
                        <div class="fw-bold">{{ $approval->approved_at ? $approval->approved_at->format('M d, Y H:i') : 'N/A' }}</div>
                    </div>
                    @if($approval->comment)
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Comment</small>
                        <div class="fst-italic">"{{ $approval->comment }}"</div>
                    </div>
                    @endif
                </div>

                <a href="{{ route('public.ticket-request.view', $approval->ticket->tracking_token) }}" class="btn btn-outline-primary mt-3">
                    <i class="bi bi-eye"></i> View Ticket Details
                </a>
            </div>
        </div>
    </div>
</body>
</html>
