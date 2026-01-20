<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,600|poppins:300,400,500,600,700" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            border: none;
            overflow: hidden;
        }
        .neo-logo {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            color: #764ba2;
            text-decoration: none;
            font-size: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app" class="w-100">
        @yield('content')
    </div>
</body>
</html>
