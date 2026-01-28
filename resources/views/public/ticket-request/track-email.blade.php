<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track My Tickets - Project Manager</title>
    
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .track-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border: none;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>

    <div class="track-card">
        <div class="card-header">
            <h2 class="mb-0"><i class="bi bi-search"></i> Track My Tickets</h2>
            <p class="mb-0 mt-2 opacity-75">View all your submitted tickets</p>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('public.ticket-request.track.submit') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="Enter your email to search..." required value="{{ old('email') }}" style="height: 50px;">
                    </div>
                    <div class="form-text mt-2">We will verify your email against our records.</div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg mb-3">
                    Check Tickets
                </button>

                <div class="text-center">
                    <a href="{{ route('public.ticket-request') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left"></i> Back to Submit Request
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
