<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - Project Manager</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .list-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .list-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
            border-radius: 50rem;
        }
        
        .status-open { background-color: #e3f2fd; color: #0d47a1; }
        .status-in_progress { background-color: #fff3e0; color: #e65100; }
        .status-completed { background-color: #e8f5e9; color: #1b5e20; }
        .status-cancelled { background-color: #eceff1; color: #455a64; }
    </style>
</head>
<body>

    <div class="list-container">
        <div class="list-card">
            <div class="card-header">
                <div>
                    <h2 class="mb-0 h4"><i class="bi bi-ticket-perforated"></i> My Submitted Tickets</h2>
                    <p class="mb-0 opacity-75 small">Email: {{ $email }}</p>
                </div>
                <a href="{{ route('public.ticket-request.track') }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-search"></i> Search Other
                </a>
            </div>
            
            <div class="card-body p-0">
                @if($tickets->isEmpty())
                    <div class="text-center p-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No tickets found for this email.</h5>
                        <p class="text-muted small">Are you sure you used this email address?</p>
                        <a href="{{ route('public.ticket-request') }}" class="btn btn-primary mt-3">Submit New Ticket</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Ticket #</th>
                                    <th class="py-3">Title</th>
                                    <th class="py-3">Project</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Status</th>
                                    <th class="text-end pe-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted small">
                                        {{ $ticket->ticket_number }}
                                        <div class="text-primary" style="font-size: 0.85em;">{{ $ticket->tracking_token }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ Str::limit($ticket->title, 40) }}</div>
                                        <div class="small text-muted">{{ $ticket->type }}</div>
                                    </td>
                                    <td>
                                        @if($ticket->project)
                                            <span class="badge bg-light text-dark border">{{ Str::limit($ticket->project->title, 20) }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'open' => 'status-open',
                                                'in_progress' => 'status-in_progress',
                                                'completed' => 'status-completed',
                                                'cancelled' => 'status-cancelled'
                                            ];
                                            $statusClass = $statusClasses[$ticket->status] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('public.ticket-request.view', $ticket->tracking_token) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            
            <div class="card-footer bg-light p-3 text-center">
                <a href="{{ route('public.ticket-request') }}" class="text-decoration-none fw-bold small">
                    <i class="bi bi-plus-circle"></i> Submit Another Request
                </a>
            </div>
        </div>
    </div>

</body>
</html>
