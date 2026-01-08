<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,600|poppins:400,600,800" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Scripts -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <!-- Mermaid JS -->
    <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
        
        // Initialize Mermaid with custom theme settings based on app theme
        document.addEventListener('DOMContentLoaded', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const mermaidTheme = currentTheme === 'dark' ? 'dark' : 'base';
            
            mermaid.initialize({ 
                startOnLoad: true,
                theme: mermaidTheme,
                themeVariables: {
                    primaryColor: '#667eea',
                    primaryTextColor: '#fff',
                    primaryBorderColor: '#000',
                    lineColor: '#000',
                    secondaryColor: '#E52521',
                    tertiaryColor: '#fff'
                }
            });
        });
    </script>
    
    <!-- Theme CSS link moved to END for proper loading order -->
    
    <script>
        // Theme loader - matches dashboard admin
        (function() {
            try {
                const savedTheme = localStorage.getItem('app-theme') || 'modern-gradient';
                
                
                console.log('[LANDING THEME] Loading saved theme:', savedTheme);
                
                // Set data-theme attribute IMMEDIATELY
                document.documentElement.setAttribute('data-theme', savedTheme);
                
                // Wait for DOM
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', loadThemeCSS);
                } else {
                    loadThemeCSS();
                }
                
                function loadThemeCSS() {
                    const themeLink = document.getElementById('theme-css');
                    
                    if (!themeLink) {
                        console.error('[LANDING THEME] ERROR: theme-css link not found!');
                        return;
                    }
                    
                    if (savedTheme === 'modern-gradient') {
                        const timestamp = new Date().getTime();
                        themeLink.href = '{{ asset("css/themes/modern-gradient.css") }}?v=' + timestamp;
                        console.log('[LANDING THEME] Loaded modern-gradient CSS');
                    } else {
                        themeLink.href = '';
                        console.log('[LANDING THEME] Using inline styles');
                    }
                }
            } catch(error) {
                console.error('[LANDING THEME] Error:', error);
            }
        })();
    </script>
    
    <style>
    <style>
        /* =========================================
           NEO-BRUTALISM & DARK THEME VARIABLES
           ========================================= */
        :root[data-theme='neobrutalism'],
        :root[data-theme='dark'] {
            /* Light Mode - Balanced Nintendo Aesthetic */
            --neo-border-color: #000000;
            --neo-bg-color: #E52521; /* Mario Red Hero */
            --neo-body-bg: #F5F5F7; /* Clean Light Grey */
            --neo-card-bg: #FFFFFF;
            --neo-text-color: #1A1A1A;
            --neo-primary: #E52521; /* Mario Red */
            --neo-info: #5C94FC; /* Mario Sky Blue */
            --neo-warning: #FBD000; /* Coin Yellow */
            --neo-shadow: 6px 6px 0px 0px #000;
            --neo-shadow-hover: 3px 3px 0px 0px #000;
        }

        [data-theme='dark'] {
            /* Dark Mode - Balanced Night Level */
            --neo-bg-color: #0D0D0F; /* Darker Hero */
            --neo-body-bg: #121214;
            --neo-card-bg: #1E1E22;
            --neo-text-color: #F0F0F0;
            --neo-primary: #FF3131;
            --neo-shadow: 4px 4px 0px 0px #FF3131;
        }
        
        /* =========================================
           MODERN GRADIENT VARIABLES (Local overrides)
           ========================================= */
        :root[data-theme='modern-gradient'] {
            --mg-hero-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --mg-text-color: #2c3e50;
        }

        /* =========================================
           COMMON STYLES
           ========================================= */
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            transition: all 0.3s ease;
        }
        
        /* =========================================
           NEO-BRUTALISM & DARK STYLES
           ========================================= */
        [data-theme='neobrutalism'] body,
        [data-theme='dark'] body {
            background-color: var(--neo-body-bg);
            color: var(--neo-text-color);
        }

        /* Hero Section */
        [data-theme='neobrutalism'] .hero-section,
        [data-theme='dark'] .hero-section {
            background-color: var(--neo-bg-color);
            color: #FFFFFF;
            text-shadow: 1px 1px 0 #000, 2px 2px 0 rgba(0,0,0,0.4);
            padding: 120px 0;
            border-bottom: 5px solid #000;
            margin-bottom: 50px;
        }

        /* Card */
        [data-theme='neobrutalism'] .card,
        [data-theme='dark'] .card {
            background-color: var(--neo-card-bg);
            border: 4px solid #000 !important;
            border-radius: 0 !important;
            box-shadow: var(--neo-shadow);
            transition: all 0.2s ease;
            color: var(--neo-text-color);
        }

        [data-theme='neobrutalism'] .card:hover,
        [data-theme='dark'] .card:hover {
            transform: translate(-3px, -3px);
            box-shadow: 8px 8px 0px 0px #000;
        }

        /* Feature Icon */
        [data-theme='neobrutalism'] .feature-icon,
        [data-theme='dark'] .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: inline-block;
            background: var(--neo-info);
            padding: 10px;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px 0px #000;
            color: #000;
        }

        /* Buttons */
        [data-theme='neobrutalism'] .btn,
        [data-theme='dark'] .btn {
            border: 4px solid #000 !important;
            border-radius: 0 !important;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: var(--neo-shadow);
            letter-spacing: 1px;
        }

        [data-theme='neobrutalism'] .btn:hover,
        [data-theme='dark'] .btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px #000;
        }

        [data-theme='neobrutalism'] .btn:active,
        [data-theme='dark'] .btn:active {
            transform: translate(2px, 2px);
            box-shadow: var(--neo-shadow-hover);
        }

        [data-theme='neobrutalism'] .btn-light-custom,
        [data-theme='dark'] .btn-light-custom {
            background-color: #fff;
            color: #000;
        }

        [data-theme='neobrutalism'] .btn-primary,
        [data-theme='dark'] .btn-primary {
            background-color: var(--neo-primary) !important;
            color: #fff !important;
            text-shadow: 1px 1px 0 #000;
        }

        [data-theme='neobrutalism'] .btn-outline-light,
        [data-theme='dark'] .btn-outline-light {
            border-color: #000 !important;
            color: var(--neo-text-color) !important;
            background-color: var(--neo-card-bg);
            text-shadow: none !important;
        }
        
        /* Logo */
        [data-theme='neobrutalism'] .neo-logo,
        [data-theme='dark'] .neo-logo {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            color: #FFF !important;
            background-color: #E52521;
            padding: 5px 15px !important;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px 0px #000;
            text-shadow: 2px 2px 0 #000;
            display: inline-block;
            text-decoration: none !important;
        }

        [data-theme='dark'] .neo-logo {
            box-shadow: 4px 4px 0px 0px #FFF;
            border-color: #000;
        }

        [data-theme='neobrutalism'] .neo-logo:hover,
        [data-theme='dark'] .neo-logo:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px #000;
            color: #FFF !important;
        }

        [data-theme='dark'] .neo-logo:hover {
            box-shadow: 6px 6px 0px 0px #FFF;
        }
        
        /* Utils */
        [data-theme='neobrutalism'] .section-title,
        [data-theme='dark'] .section-title {
            color: var(--neo-text-color) !important;
        }

        [data-theme='neobrutalism'] .text-muted,
        [data-theme='dark'] .text-muted {
            color: var(--neo-text-color) !important;
            opacity: 0.7;
        }
        
        /* =========================================
           MODERN GRADIENT SPECIFIC STYLES
           ========================================= */
        [data-theme='modern-gradient'] .hero-section {
            background: var(--mg-hero-bg);
            color: #FFFFFF;
            padding: 120px 0;
            border-bottom: none;
            margin-bottom: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        [data-theme='modern-gradient'] .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: inline-block;
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 10px;
            border: none;
            box-shadow: none;
        }
        
        [data-theme='modern-gradient'] .btn-light-custom {
            background: #ffffff;
            color: #667eea;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        [data-theme='modern-gradient'] .btn-light-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        [data-theme='modern-gradient'] .section-title {
            color: #2c3e50;
            font-weight: 700;
        }
        
        /* =========================================
           TICKET GUIDE SECTION
           ========================================= */
        /* Neo-Brutalism Defaults */
        .ticket-guide-section {
            background-color: var(--neo-card-bg);
        }
        
        .guide-card {
            background-color: var(--neo-card-bg);
            border: 3px solid #000 !important;
            box-shadow: var(--neo-shadow);
            transition: transform 0.2s;
        }
        
        .guide-card:hover {
            transform: translate(-3px, -3px);
            box-shadow: 8px 8px 0px 0px #000 !important;
        }
        
        .step-badge {
            border: 2px solid #000;
            box-shadow: 2px 2px 0px 0px #000;
        }
        
        .status-check-card {
            background-color: var(--neo-body-bg);
            border: 3px solid #000 !important;
            box-shadow: var(--neo-shadow);
        }
        
        .token-input {
            border: 3px solid #000 !important;
            font-weight: 800;
            border-radius: 0 !important;
        }
        
        .track-btn {
            border-radius: 0 !important;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Modern Gradient Overrides */
        [data-theme='modern-gradient'] .ticket-guide-section {
            background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        [data-theme='modern-gradient'] .guide-card {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        [data-theme='modern-gradient'] .guide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        
        [data-theme='modern-gradient'] .step-badge {
            border: none !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
        }
        
        [data-theme='modern-gradient'] .status-check-card {
            background: #ffffff !important;
            border: none !important;
            border-radius: 25px !important;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1) !important;
        }
        
        [data-theme='modern-gradient'] .token-input {
            border: 1px solid #e0e0e0 !important;
            border-radius: 50px !important;
            padding-left: 25px;
            font-weight: 500;
            background: #f8f9fa;
        }
        
        [data-theme='modern-gradient'] .token-input:focus {
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
            border-color: #667eea !important;
        }
        
        [data-theme='modern-gradient'] .track-btn {
            border-radius: 50px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none !important;
            box-shadow: 0 4px 15px rgba(118, 75, 162, 0.3) !important;
            text-transform: none;
            letter-spacing: normal;
        }
        
        [data-theme='modern-gradient'] .track-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(118, 75, 162, 0.4) !important;
        }
    </style>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('neo-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent position-absolute w-100 top-0 pt-4" style="z-index: 1000;">
        <div class="container">
            <a class="neo-logo" href="{{ url('/') }}">{{ config('app.name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <!-- Theme Switcher Dropdown -->
                    <li class="nav-item dropdown me-3">
                        <a id="themeDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white !important; font-weight: 600;">
                            <span class="theme-icon me-2">🎨</span> Theme 
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="themeDropdown" style="border-radius: 12px; overflow: hidden;">
                            <li><a class="dropdown-item d-flex align-items-center py-2" href="#" data-value="neobrutalism">
                                <span class="me-2">🟥</span> Neo-Brutalism
                            </a></li>
                            <li><a class="dropdown-item d-flex align-items-center py-2" href="#" data-value="modern-gradient">
                                <span class="me-2">🎨</span> Modern Gradient
                            </a></li>
                            <li><a class="dropdown-item d-flex align-items-center py-2" href="#" data-value="dark">
                                <span class="me-2">🌙</span> Dark Mode
                            </a></li>
                        </ul>
                    </li>
                    
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/projects') }}" class="btn btn-outline-light px-4">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item me-3">
                                <a href="{{ route('login') }}" class="nav-link text-white fw-bold">Login</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-light px-4 fw-bold">Register</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Switcher Logic
            const themeOptions = document.querySelectorAll('.dropdown-item[data-value]');
            const themeDropdown = document.getElementById('themeDropdown');
            const themeIcon = themeDropdown ? themeDropdown.querySelector('.theme-icon') : null;
            
            // Map themes to icons
            const themeIcons = {
                'neobrutalism': '🟥',
                'modern-gradient': '🎨',
                'dark': '🌙'
            };
            
            // Initial state check
            const currentTheme = localStorage.getItem('app-theme') || 'modern-gradient';
            document.documentElement.setAttribute('data-theme', currentTheme);
            if(themeIcon && themeIcons[currentTheme]) {
                themeIcon.textContent = themeIcons[currentTheme];
            }
            
            // Handle click events
            themeOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newTheme = this.getAttribute('data-value');
                    
                    // 1. Save to local storage
                    try {
                        localStorage.setItem('app-theme', newTheme);
                        console.log('[LANDING THEME] Saved preference:', newTheme);
                    } catch(err) {
                        console.error('[LANDING THEME] LocalStorage error:', err);
                        alert('Could not save theme preference');
                        return;
                    }
                    
                    // 2. Update Document Attribute (CSS triggers)
                    document.documentElement.setAttribute('data-theme', newTheme);
                    
                    // 3. Update Dropdown Icon
                    if(themeIcon && themeIcons[newTheme]) {
                        themeIcon.textContent = themeIcons[newTheme];
                    }
                    
                    // 4. Handle External CSS for Modern Gradient
                    const themeLink = document.getElementById('theme-css');
                    if (themeLink) {
                        if (newTheme === 'modern-gradient') {
                            // Add timestamp to bust browser cache
                            const timestamp = new Date().getTime();
                            const cssUrl = '{{ asset("css/themes/modern-gradient.css") }}';
                            themeLink.href = cssUrl + '?v=' + timestamp;
                            console.log('[LANDING THEME] Loaded modern-gradient CSS');
                        } else {
                            themeLink.href = '';
                            console.log('[LANDING THEME] Cleared external CSS');
                        }
                    }
                    
                    console.log('[LANDING THEME] Theme changed to:', newTheme);
                });
            });
        });
    </script>

    <header class="hero-section d-flex align-items-center">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4">IGI Project Manager</h1>
                    <p class="lead mb-5 opacity-75">Streamline your workflow, collaborate with ease, and deliver results on time. The ultimate, simple tool for modern teams.</p>
                    @auth
                        <a href="{{ url('/projects') }}" class="btn btn-light-custom btn-lg">Go to Projects</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-light-custom btn-lg">Get Started for Free</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

     <section class="bg-light py-5 border-top border-bottom border-dark border-4">
        <div class="container text-center py-5">
            <h2 class="fw-bold mb-4">Ready to boost productivity?</h2>
            <p class="lead text-muted mb-4">Join thousands of users who are organizing their life with {{ config('app.name') }}.</p>
             @if (Route::has('register'))
                <!-- <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 me-3 mb-3">Join Now</a> -->
            @endif
            <a href="{{ route('public.ticket-request') }}" class="btn btn-outline-dark btn-lg px-5 mb-3" target="_blank">
                <i class="bi bi-ticket-perforated me-2"></i>Submit Public Ticket
            </a>
        </div>
    </section>

    <!-- Public Ticket Guide Section -->
    <section class="ticket-guide-section py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title fw-bold display-5 mb-3">How to Submit a Request</h2>
                <p class="lead text-muted">Follow these simple steps to submit your project request or issue report.</p>
            </div>

            <div class="row g-4">
                <!-- Step 1 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm position-relative guide-card">
                        <div class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary fs-5 step-badge">1</div>
                        <div class="card-body text-center mt-3">
                            <div class="fs-1 mb-3">📝</div>
                            <h4 class="fw-bold">Fill Information</h4>
                            <p class="text-muted small">Provide your contact details and describe your request clearly. Selecting the right priority helps us triage effectively.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm position-relative guide-card">
                        <div class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-warning text-dark fs-5 step-badge">2</div>
                        <div class="card-body text-center mt-3">
                            <div class="fs-1 mb-3">📎</div>
                            <h4 class="fw-bold">Upload Documents</h4>
                            <p class="text-muted small">Attach necessary files like PDF requirements, functional specs, or mockups. You can upload up to 10 files.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm position-relative guide-card">
                        <div class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success fs-5 step-badge">3</div>
                        <div class="card-body text-center mt-3">
                            <div class="fs-1 mb-3">🎟️</div>
                            <h4 class="fw-bold">Get Information</h4>
                            <p class="text-muted small">Receive a unique <strong>Tracking Token</strong> (e.g., PUBR-2026-XYZ). Use this to monitor progress in real-time.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Flowchart Section -->
            <div class="row justify-content-center mt-5">
                <div class="col-12 text-center mb-4">
                    <h3 class="fw-bold">Submission Process Flow</h3>
                    <p class="text-muted">A detailed look at the request lifecycle.</p>
                </div>
                <div class="col-lg-10">
                    <div class="card p-4 border-0 guide-card" style="overflow-x: auto;">
                        <div class="mermaid text-center">
                            graph TD
                            %% User Actions
                            Start([🌐 Visit Page]) --> Input[📝 Fill & Upload]
                            Input --> Submit[🚀 Submit Request]
                            Submit --> Token{🎟️ Get Token}
                            
                            %% System/Internal Actions
                            Token --> |User| Track[🔍 Track Status]
                            
                            %% Internal Process
                            Track --> Review[👀 Admin Review]
                            Review --> |Approved| Dev[⚙️ Development]
                            Review --> |Rejected| Closed([❌ Closed])
                            
                            Dev --> QC[🧪 Testing & QC]
                            QC --> Done([✅ Completed])
                            
                            %% Styling
                            style Start fill:#667eea,stroke:#000,stroke-width:2px,color:#fff
                            style Done fill:#28a745,stroke:#000,stroke-width:2px,color:#fff
                            style Closed fill:#dc3545,stroke:#000,stroke-width:2px,color:#fff
                            style Token fill:#FBD000,stroke:#000,stroke-width:2px,color:#000
                            style Track fill:#667eea,stroke:#000,stroke-width:2px,color:#000
                            style Input fill:#fff,stroke:#000,stroke-width:2px,color:#000
                            style Submit fill:#fff,stroke:#000,stroke-width:2px,color:#000
                            style Review fill:#fff,stroke:#000,stroke-width:2px,color:#000
                            style Dev fill:#fff,stroke:#000,stroke-width:2px,color:#000
                            style QC fill:#fff,stroke:#000,stroke-width:2px,color:#000
                        </div>
                    </div>
                </div>
            </div>

            <!-- Check Status Widget -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8">
                    <div class="card p-4 border-0 status-check-card">
                        <div class="card-body text-center">
                            <h4 class="fw-bold mb-3">Already submitted a request?</h4>
                            <p class="mb-4">Check the status of your ticket using your Tracking Token.</p>
                            
                            <form action="#" method="GET" id="trackForm" class="d-flex justify-content-center gap-2 flex-wrap">
                                <input type="text" id="tokenInput" class="form-control form-control-lg w-auto token-input" placeholder="Enter Token (e.g. PUBR-2026...)" autocomplete="off">
                                <button type="button" onclick="checkStatus()" class="btn btn-dark btn-lg px-4 track-btn">
                                    <i class="bi bi-search me-2"></i>Track
                                </button>
                            </form>
                            <script>
                                function checkStatus() {
                                    const token = document.getElementById('tokenInput').value.trim();
                                    if(token) {
                                        window.location.href = "{{ url('/public/ticket-request/view') }}/" + token;
                                    } else {
                                        alert('Please enter a valid token');
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container text-center py-5">
            <h2 class="fw-bold mb-5 text-dark">Why Choose Us?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 h-100 p-4 shadow-sm hover-shadow">
                        <div class="card-body">
                            <div class="feature-icon">🚀</div>
                            <h4 class="fw-bold mb-3">Fast & efficient</h4>
                            <p class="text-muted">Designed for speed, so you can spend less time managing and more time doing.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 h-100 p-4 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon">📊</div>
                            <h4 class="fw-bold mb-3">Clear Insights</h4>
                            <p class="text-muted">Track progress with simple statuses and visual indicators. Know where you stand at a glance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 h-100 p-4 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon">🛡️</div>
                            <h4 class="fw-bold mb-3">Secure & Private</h4>
                            <p class="text-muted">Your data is yours. Strict policies ensure only you see what you need to see.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   



    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
