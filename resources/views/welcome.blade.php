<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'IGI Project Manager') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,600|poppins:400,600,700,800" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Scripts -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <!-- Mermaid JS -->
    <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
        mermaid.initialize({ 
            startOnLoad: true,
            theme: 'base',
            themeVariables: {
                primaryColor: '#667eea',
                primaryTextColor: '#000',
                primaryBorderColor: '#667eea',
                lineColor: '#764ba2',
                secondaryColor: '#f8f9fa',
                tertiaryColor: '#fff',
                textColor: '#000',
                nodeTextColor: '#000',
                mainBkg: '#fff',
                nodeBorder: '#000'
            }
        });
    </script>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            --card-shadow: 0 10px 40px rgba(0,0,0,0.08);
            --card-hover-shadow: 0 20px 60px rgba(0,0,0,0.12);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --radius-lg: 20px;
            --radius-md: 12px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            background: var(--primary-gradient);
            color: white;
            padding: 140px 0 100px;
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }

        .hero-title {
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-weight: 400;
            opacity: 0.9;
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 40px;
        }

        /* Navbar */
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: white !important;
            letter-spacing: -0.5px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .nav-link:hover {
            color: white !important;
        }

        .btn-hero-primary {
            background: white;
            color: #667eea;
            font-weight: 700;
            padding: 12px 35px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            background: white;
            color: #764ba2;
        }

        /* Modern Cards */
        .modern-card {
            background: white;
            border: none;
            border-radius: var(--radius-lg);
            padding: 2.5rem 2rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        /* Steps */
        .step-number {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 4rem;
            font-weight: 900;
            color: #f3f4f6;
            line-height: 1;
            z-index: 0;
            font-family: 'Nunito', sans-serif;
        }

        .step-content {
            position: relative;
            z-index: 1;
        }

        .step-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        /* Tracking Widget */
        .tracking-widget-container {
            margin-top: -60px; /* Overlap effect */
            position: relative;
            z-index: 10;
        }

        .tracking-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
            border: 1px solid rgba(0,0,0,0.02);
        }

        .token-input {
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-family: monospace;
            text-align: center;
            letter-spacing: 1px;
            transition: all 0.2s;
        }

        .token-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-track {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        /* Features */
        .feature-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: linear-gradient(135deg, #f3f4f6 0%, #fff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .feature-icon-wrapper i {
            font-size: 1.75rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Footer */
        .footer-modern {
            background: white;
            padding: 40px 0;
            margin-top: 80px;
            border-top: 1px solid #edf2f7;
            text-align: center;
            color: var(--text-secondary);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title {
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 15px;
            font-size: 2.25rem;
        }
        
        .section-desc {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .mermaid-container {
            background: white;
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: var(--card-shadow);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent position-absolute w-100 top-0 pt-4" style="z-index: 1000;">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-kanban me-2"></i>{{ config('app.name', 'IGI Project Manager') }}
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-globe me-1"></i> {{ strtoupper(session('locale', 'id')) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                            <li><a class="dropdown-item {{ session('locale') == 'id' ? 'active' : '' }}" href="{{ route('lang.switch', 'id') }}">🇮🇩 Indonesia</a></li>
                            <li><a class="dropdown-item {{ session('locale') == 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">🇬🇧 English</a></li>
                        </ul>
                    </li>

                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/projects') }}" class="btn btn-outline-light rounded-pill px-4">{{ __('Dashboard') }}</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">{{ __('Log in') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-4 text-primary fw-bold">{{ __('Sign Up') }}</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm fw-bold mb-2">
                            <i class="bi bi-building me-1"></i> {{ __('Indraco Web Dev Division') }}
                        </span>
                    </div>
                    <h1 class="display-3 hero-title">{{ __('Manage Projects with Clarity') }}</h1>
                    <p class="hero-subtitle">{{ __('Streamline your workflow, collaborate effectively, and deliver results on time. The modern solution for agile teams.') }}</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('public.ticket-request') }}" class="btn btn-hero-primary">
                            <i class="bi bi-ticket-perforated me-2"></i>{{ __('Submit Public Ticket') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Tracking Widget -->
    <div class="container tracking-widget-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="tracking-card">
                    <h3 class="fw-bold mb-3">{{ __('Track Your Request') }}</h3>
                    <p class="text-muted mb-4">{{ __('Already submitted a ticket? Enter your tracking token below.') }}</p>
                    
                    <form action="#" method="GET" id="trackForm" class="d-flex justify-content-center gap-2 flex-wrap">
                        <input type="text" id="tokenInput" class="form-control w-auto token-input" placeholder="e.g. PUBR-2026-XYZ" autocomplete="off" style="min-width: 300px;">
                        <button type="button" onclick="checkStatus()" class="btn btn-track">
                            {{ __('Track Status') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- How it Works -->
    <section class="py-5 mt-5" id="how-it-works">
        <div class="container py-5">
            <div class="section-header">
                <h2 class="section-title">{{ __('How to Submit a Request') }}</h2>
                <p class="section-desc">{{ __('Follow these simple steps to get your project moving.') }}</p>
            </div>

            <div class="row g-4">
                <!-- Step 1 -->
                <div class="col-md-4">
                    <div class="modern-card">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-icon"><i class="bi bi-pencil-square"></i></div>
                            <h4 class="fw-bold mb-3">{{ __('Fill Information') }}</h4>
                            <p class="text-muted">{{ __('Provide detailed information about your request. The more details you provide, the faster we can process it.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4">
                    <div class="modern-card">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-icon"><i class="bi bi-cloud-upload"></i></div>
                            <h4 class="fw-bold mb-3">{{ __('Upload Documents') }}</h4>
                            <p class="text-muted">{{ __('Attach any necessary files, specifications, or mockups. Our system supports multiple file formats securely.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4">
                    <div class="modern-card">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-icon"><i class="bi bi-qr-code-scan"></i></div>
                            <h4 class="fw-bold mb-3">{{ __('Track Progress') }}</h4>
                            <p class="text-muted">{{ __('Receive a unique tracking token instantly. Monitor your request status in real-time without logging in.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Flowchart -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-10">
                        <div class="mermaid-container">
                        <h5 class="text-center fw-bold mb-4 text-muted text-uppercase small ls-1">{{ __('Project Workflow') }}</h5>
                        <div class="mermaid" style="text-align: center;">
graph TD
    Start([🌐 Visit Page]) --> Input[📝 Fill & Upload]
    Input --> Submit[🚀 Submit Request]
    Submit --> Token{🎟️ Get Token}
    
    Token --> |User| Track[🔍 Track Status]
    
    Track --> Review[👀 Admin Review]
    Review --> |Approved| Dev[⚙️ Development]
    Review --> |Rejected| Closed([❌ Closed])
    
    Dev --> QC[🧪 Testing & QC]
    QC --> Done([✅ Completed])
    
    style Start fill:#f8f9fa,stroke:#667eea,stroke-width:2px
    style Done fill:#d1fae5,stroke:#10b981,stroke-width:2px,color:#065f46
    style Closed fill:#fee2e2,stroke:#ef4444,stroke-width:2px,color:#991b1b
    style Token fill:#fef3c7,stroke:#f59e0b,stroke-width:2px,color:#92400e
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <footer class="footer-modern">
        <div class="container">
            <p class="mb-2 fw-bold text-dark">{{ config('app.name', 'IGI Project Manager') }}</p>
            <p class="mb-0 small">&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </footer>

    <script>
        function checkStatus() {
            const token = document.getElementById('tokenInput').value.trim();
            if(token) {
                window.location.href = "{{ url('/ticket-request/view') }}/" + token;
            } else {
                // Shake animation or simple alert
                const input = document.getElementById('tokenInput');
                input.style.borderColor = '#dc3545';
                setTimeout(() => input.style.borderColor = '#e2e8f0', 2000);
            }
        }
    </script>
</body>
</html>
