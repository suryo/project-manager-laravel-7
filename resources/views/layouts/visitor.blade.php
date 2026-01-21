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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: #fff;
        }
        .visitor-navbar {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .visitor-navbar .navbar-brand {
            font-weight: 800;
            color: #fff !important;
            letter-spacing: -0.5px;
        }
        .visitor-navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
        }
        .visitor-navbar .nav-link:hover {
            color: #fff !important;
        }
        .dropdown-menu, .modal-content, 
        .dropdown-menu *, .modal-content * {
            color: #212529 !important; /* Force dark text for light backgrounds */
        }
        .modal-content {
            border: 4px solid #000 !important;
            border-radius: 0 !important;
            box-shadow: 10px 10px 0 #000;
        }
        .footer-visitor {
            background: rgba(0, 0, 0, 0.1);
            padding: 2rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 4rem;
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg visitor-navbar py-3">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-kanban me-2"></i>{{ config('app.name', 'IGI Manager') }}
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#visitorNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="visitorNav">
                    <ul class="navbar-nav ms-auto align-items-center gap-3">
                        @php $depts = \App\Models\Department::all(); @endphp
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-building me-1"></i> Departments
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                @foreach($depts as $dept)
                                    <li><a class="dropdown-item" href="{{ route('department.landing', $dept->slug) }}">{{ $dept->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @guest
                            <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                        @else
                            <li class="nav-item"><a href="{{ route('projects.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">Dashboard</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="footer-visitor text-center">
            <div class="container">
                <p class="mb-0 text-muted small">&copy; {{ date('Y') }} {{ config('app.name') }} - Indraco Web Dev Division</p>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
