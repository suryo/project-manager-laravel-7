<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,600|poppins:400,600,700,800" rel="stylesheet" />
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --navbar-height: 70px;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            padding-top: var(--navbar-height); /* Offset for fixed-top navbar */
        }

        .visitor-navbar {
            height: var(--navbar-height);
            background-color: #ffffff !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease;
        }

        .visitor-navbar .navbar-brand {
            font-weight: 800;
            color: #4e73df !important;
            letter-spacing: -0.5px;
            font-size: 1.5rem;
        }

        .visitor-navbar .nav-link {
            color: #4e73df !important;
            font-weight: 600;
            padding: 0.5rem 1rem !important;
        }

        .visitor-navbar .nav-link:hover {
            color: #764ba2 !important;
        }

        .navbar-toggler {
            border: 1px solid #e3e6f0 !important;
            padding: 0.5rem;
        }

        @media (max-width: 991px) {
            .visitor-navbar {
                background-color: #667eea !important; /* Force solid color on mobile bar */
                background: #667eea !important;
                border-bottom: 2px solid rgba(0,0,0,0.1);
            }

            .visitor-navbar .navbar-brand, 
            .visitor-navbar .nav-link {
                color: #ffffff !important; /* Use white text for better contrast on solid color */
            }

            .navbar-collapse {
                background-color: #667eea !important; /* Force solid color for the dropdown */
                background: #667eea !important;
                opacity: 1 !important;
                position: fixed;
                top: var(--navbar-height);
                left: 0;
                width: 100%;
                padding: 1.5rem;
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                border-top: 1px solid rgba(255,255,255,0.1);
                z-index: 9999 !important;
                visibility: visible !important;
                display: none; /* Let Bootstrap handle show/hide but keep it solid */
            }

            .navbar-collapse.show, .navbar-collapse.collapsing {
                display: block !important;
                background-color: #667eea !important;
                background: #667eea !important;
                opacity: 1 !important;
            }

            #navIcon {
                color: #ffffff !important; /* White icon on mobile */
            }

            .navbar-nav .nav-item {
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding: 0.5rem 0;
            }

            .navbar-nav .dropdown-menu {
                border: none;
                background-color: rgba(0,0,0,0.1) !important; /* Slightly darker for nested */
                padding-left: 1rem;
                margin-top: 0.5rem;
            }
            
            .navbar-nav .dropdown-item {
                color: #ffffff !important;
            }
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .dropdown-item {
            font-weight: 500;
            padding: 0.7rem 1.5rem;
        }

        .dropdown-item:active {
            background-color: #4e73df;
        }

        .footer-visitor {
            background-color: #ffffff;
            padding: 3rem 0;
            border-top: 1px solid #e3e6f0;
            margin-top: 5rem;
            color: #858796;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg visitor-navbar fixed-top" id="topNav">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="bi bi-kanban-fill me-2"></i>
                    <span>{{ config('app.name', 'Project Manager') }}</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#visitorNav">
                    <span class="bi bi-list fs-3" id="navIcon" style="color: #4e73df;"></span>
                </button>
                <div class="collapse navbar-collapse" id="visitorNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        @php $depts = \App\Models\Department::all(); @endphp
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-building me-1"></i> Departments
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach($depts as $dept)
                                    <li><a class="dropdown-item" href="{{ route('department.landing', $dept->slug) }}">{{ $dept->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @guest
                            <li class="nav-item ms-lg-3">
                                <a href="{{ route('login') }}" class="nav-link text-primary">Login</a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Sign Up</a>
                            </li>
                        @else
                            <li class="nav-item ms-lg-3">
                                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Dashboard</a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer class="footer-visitor">
            <div class="container text-center">
                <div class="mb-3">
                    <a class="navbar-brand text-decoration-none" href="{{ url('/') }}">
                        {{ config('app.name', 'Project Manager') }}
                    </a>
                </div>
                <p class="mb-0 small">&copy; {{ date('Y') }} {{ config('app.name') }} - Indraco Web Dev Division</p>
                <div class="mt-3 small">
                    <span class="badge bg-light text-muted border">v1.2.0</span>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
