<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,600|poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="{{ mix('js/app.js') }}" defer></script>
    @stack('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Theme CSS link moved to END of head for proper loading order -->
    <!-- See before </head> tag below -->
    
    <script>
        // Theme loader - CRITICAL: Must run BEFORE DOM ready
        (function() {
            try {
                // Get saved theme preference (default to modern-gradient)
                const savedTheme = localStorage.getItem('app-theme') || 'modern-gradient';
                
                console.log('[THEME] Loading saved theme:', savedTheme);
                console.log('[THEME] localStorage value:', localStorage.getItem('app-theme'));
                // Initial state check
            document.documentElement.setAttribute('data-theme', savedTheme);
                console.log('[THEME] Set data-theme attribute to:', savedTheme);
                
                // Wait for DOM to ensure link element exists
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', loadThemeCSS);
                } else {
                    loadThemeCSS();
                }
                
                function loadThemeCSS() {
                    const themeLink = document.getElementById('theme-css');
                    
                    if (!themeLink) {
                        console.error('[THEME] ERROR: theme-css link element not found!');
                        return;
                    }
                    
                    if (savedTheme === 'modern-gradient') {
                        // Add timestamp to bust cache
                        const timestamp = new Date().getTime();
                        const cssUrl = '{{ asset("css/themes/modern-gradient.css") }}';
                        themeLink.href = cssUrl + '?v=' + timestamp;
                        console.log('[THEME] Loaded modern-gradient CSS:', themeLink.href);
                        
                        // Verify CSS loaded after a short delay
                        setTimeout(() => {
                            const computedBg = window.getComputedStyle(document.body).backgroundColor;
                            console.log('[THEME] Body background color:', computedBg);
                            if (computedBg === 'rgb(255, 255, 255)' || computedBg === 'rgba(0, 0, 0, 0)') {
                                console.warn('[THEME] WARNING: Modern Gradient CSS may not be applying!');
                            }
                        }, 500);
                    } else {
                        // Neo-brutalism and dark use inline styles only
                        themeLink.href = '';
                        console.log('[THEME] Using inline styles for:', savedTheme);
                    }
                }
                
            } catch(error) {
                console.error('[THEME] Error loading theme:', error);
            }
        })();
    </script>
    
    <style>
        :root {
            /* Light Mode - Balanced Nintendo Aesthetic */
            --neo-border-color: #000000;
            --neo-bg-color: #F5F5F7; /* Clean Light Grey */
            --neo-card-bg: #FFFFFF;
            --neo-nav-bg: #E52521; /* Mario Red */
            --neo-text-color: #1A1A1A;
            --neo-primary: #E52521; /* Mario Red */
            --neo-secondary: #049CD8; /* Mario Blue */
            --neo-success: #43B047; /* Luigi Green */
            --neo-warning: #FBD000; /* Coin Yellow */
            --neo-info: #5C94FC; /* Mario Sky Blue */
            --neo-shadow: 6px 6px 0px 0px #000;
            --neo-shadow-hover: 3px 3px 0px 0px #000;
        }

        [data-theme='dark'] {
            /* Dark Mode - Balanced Night Level */
            --neo-bg-color: #121214; /* Deeper Night Blue/Black */
            --neo-card-bg: #1E1E22;
            --neo-nav-bg: #0D0D0F;
            --neo-text-color: #F0F0F0;
            --neo-primary: #FF3B30; /* Vibrant Red */
            --neo-secondary: #0A84FF; /* Vibrant Blue */
            --neo-border-color: #000000; 
            --neo-shadow: 6px 6px 0px 0px #000;
        }

        /* Neo-Brutalism theme styles - only apply when neobrutalism or dark theme active */
        [data-theme="neobrutalism"] body,
        [data-theme="dark"] body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--neo-bg-color);
            color: var(--neo-text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        [data-theme="neobrutalism"] h1, 
        [data-theme="neobrutalism"] h2, 
        [data-theme="neobrutalism"] h3, 
        [data-theme="neobrutalism"] h4, 
        [data-theme="neobrutalism"] h5, 
        [data-theme="neobrutalism"] h6,
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6 {
            color: var(--neo-text-color) !important;
        }

        [data-theme="neobrutalism"] .navbar-custom,
        [data-theme="dark"] .navbar-custom {
            background-color: var(--neo-nav-bg) !important;
            border-bottom: 3px solid var(--neo-border-color);
            box-shadow: none;
            margin-bottom: 2rem;
            transition: background-color 0.3s ease;
        }

        [data-theme="neobrutalism"] .neo-logo,
        [data-theme="dark"] .neo-logo {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            color: #FFF !important;
            background-color: #E52521; /* Mario Red */
            padding: 5px 15px !important;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px 0px #000;
            transition: all 0.1s ease;
            text-shadow: 2px 2px 0 #000;
            display: inline-block;
            text-decoration: none !important;
        }

        [data-theme='dark'] .neo-logo {
            box-shadow: 4px 4px 0px 0px #FFF; /* White shadow for dark mode */
            border-color: #000;
        }

        [data-theme="neobrutalism"] .neo-logo:hover,
        [data-theme="dark"] .neo-logo:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px #000;
            color: #FFF !important;
        }

        [data-theme='dark'] .neo-logo:hover {
            box-shadow: 6px 6px 0px 0px #FFF;
        }

        [data-theme="neobrutalism"] .nav-link,
        [data-theme="dark"] .nav-link {
            color: #FFFFFF !important;
            text-shadow: 1px 1px 0 #000, 2px 2px 0 rgba(0,0,0,0.4);
            font-weight: 800;
            letter-spacing: 1px;
        }

        [data-theme="neobrutalism"] .dropdown-menu,
        [data-theme="dark"] .dropdown-menu {
            background-color: var(--neo-card-bg);
            border: 3px solid var(--neo-border-color);
            border-radius: 0;
            box-shadow: var(--neo-shadow);
        }

        [data-theme="neobrutalism"] .dropdown-item,
        [data-theme="dark"] .dropdown-item {
            color: var(--neo-text-color);
        }

        [data-theme="neobrutalism"] .dropdown-item:hover,
        [data-theme="dark"] .dropdown-item:hover {
            background-color: var(--neo-primary);
            color: #000;
        }

        [data-theme="neobrutalism"] .card,
        [data-theme="dark"] .card {
            background-color: var(--neo-card-bg);
            border: 3px solid var(--neo-border-color) !important;
            border-radius: 0 !important;
            box-shadow: var(--neo-shadow);
            transition: all 0.2s ease;
            color: var(--neo-text-color);
            margin-bottom: 1.5rem;
        }

        [data-theme="neobrutalism"] .card:hover,
        [data-theme="dark"] .card:hover {
            transform: translate(-3px, -3px);
            box-shadow: 9px 9px 0px 0px #000;
        }

        [data-theme="neobrutalism"] .card-header,
        [data-theme="dark"] .card-header {
            background-color: var(--neo-card-bg);
            border-bottom: 3px solid var(--neo-border-color) !important;
            border-radius: 0 !important;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--neo-text-color);
        }

        [data-theme="neobrutalism"] .btn,
        [data-theme="dark"] .btn {
            border: 3px solid var(--neo-border-color) !important;
            border-radius: 0 !important;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: var(--neo-shadow-hover);
            transition: all 0.1s ease;
        }

       [data-theme="neobrutalism"] .btn:hover,
        [data-theme="dark"] .btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 4px 4px 0px 0px #000;
        }

        [data-theme="neobrutalism"] .btn:active,
        [data-theme="dark"] .btn:active {
            transform: translate(0, 0);
            box-shadow: none;
        }

        [data-theme="neobrutalism"] .btn-primary,
        [data-theme="dark"] .btn-primary { 
            background-color: var(--neo-primary) !important; 
            color: #FFF !important; 
            text-shadow: 1px 1px 0 #000;
        }
        [data-theme="neobrutalism"] .btn-primary:hover,
        [data-theme="dark"] .btn-primary:hover { background-color: var(--neo-primary); color: #FFF; }
        
        [data-theme="neobrutalism"] .btn-outline-primary,
        [data-theme="dark"] .btn-outline-primary {
            color: var(--neo-primary) !important;
            border-color: var(--neo-border-color) !important;
            background-color: transparent;
        }
        
        [data-theme="neobrutalism"] .btn-outline-primary:hover,
        [data-theme="dark"] .btn-outline-primary:hover {
            background-color: var(--neo-primary) !important;
            color: #FFF !important;
            text-shadow: 1px 1px 0 #000;
        }

        [data-theme="neobrutalism"] .form-control, 
        [data-theme="neobrutalism"] .form-select,
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background-color: var(--neo-card-bg);
            color: var(--neo-text-color);
            border: 3px solid var(--neo-border-color) !important;
            border-radius: 0 !important;
            padding: 0.75rem;
            font-weight: 600;
            box-shadow: inset 2px 2px 0px 0px rgba(0,0,0,0.1);
        }

        [data-theme="neobrutalism"] .form-control:focus, 
        [data-theme="neobrutalism"] .form-select:focus,
        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            background-color: var(--neo-card-bg);
            color: var(--neo-text-color);
            box-shadow: var(--neo-shadow) !important;
            border-color: var(--neo-border-color) !important;
            outline: none;
        }

        [data-theme="neobrutalism"] .badge,
        [data-theme="dark"] .badge {
            border: 2px solid var(--neo-border-color);
            border-radius: 0 !important;
            padding: 0.5em 0.8em;
            font-weight: 700;
            color: #fff;
            box-shadow: 2px 2px 0 #000;
        }

        [data-theme="neobrutalism"] .badge:not(.bg-white):not(.bg-light),
        [data-theme="dark"] .badge:not(.bg-white):not(.bg-light) {
            text-shadow: 1px 1px 0 #000;
        }

        [data-theme="neobrutalism"] .alert,
        [data-theme="dark"] .alert {
            border: 3px solid var(--neo-border-color) !important;
            border-radius: 0 !important;
            box-shadow: var(--neo-shadow);
            color: #000 !important;
            font-weight: 600;
        }

        /* Removed obsolete #theme-toggle styles - replaced with dropdown */

        [data-theme="neobrutalism"] .bg-primary,
        [data-theme="dark"] .bg-primary { background-color: var(--neo-primary) !important; }
        [data-theme="neobrutalism"] .bg-success,
        [data-theme="dark"] .bg-success { background-color: var(--neo-success) !important; }
        [data-theme="neobrutalism"] .bg-info,
        [data-theme="dark"] .bg-info { background-color: var(--neo-info) !important; }
        [data-theme="neobrutalism"] .bg-warning,
        [data-theme="dark"] .bg-warning { background-color: var(--neo-warning) !important; }
        [data-theme="neobrutalism"] .bg-danger,
        [data-theme="dark"] .bg-danger { background-color: var(--neo-danger) !important; }
        [data-theme="neobrutalism"] .text-primary,
        [data-theme="dark"] .text-primary { 
            color: var(--neo-primary) !important;
            font-weight: 800;
            text-shadow: 1px 1px 0 #000;
        }

        .card.bg-primary, .card.bg-success, .card.bg-info, .card.bg-danger {
            color: #fff !important;
        }

        .card.bg-primary .card-title, 
        .card.bg-success .card-title, 
        .card.bg-info .card-title, 
        .card.bg-danger .card-title {
            color: #fff !important;
            text-shadow: 1px 1px 0 #000;
        }

        .auth-wrapper {
            background-color: var(--neo-secondary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 5px solid #000;
        }
        /* Quill Neobrutalist Overrides */
        .ql-toolbar.ql-snow {
            border: 3px solid var(--neo-border-color) !important;
            background: var(--neo-card-bg) !important;
            margin-bottom: 0 !important;
            border-bottom: none !important;
            border-radius: 0 !important;
            font-family: 'Poppins', sans-serif !important;
        }
        .ql-container.ql-snow {
            border: 3px solid var(--neo-border-color) !important;
            background: var(--neo-card-bg) !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 1rem !important;
            border-radius: 0 !important;
            color: var(--neo-text-color) !important;
        }
        #comment-editor .ql-editor {
            min-height: 120px;
        }
        .comment-content.ql-editor {
            min-height: 0 !important;
            padding: 0 !important;
            height: auto !important;
            overflow: visible !important;
        }
        .comment-content.ql-editor p {
            margin-bottom: 0 !important;
            padding: 0 !important;
        }
        .ql-editor::before {
            color: var(--neo-text-color) !important;
            opacity: 0.5;
            font-style: normal !important;
        }
        .ql-snow .ql-stroke {
            stroke: var(--neo-text-color) !important;
        }
        .ql-snow .ql-fill {
            fill: var(--neo-text-color) !important;
        }
        .ql-snow .ql-picker {
            color: var(--neo-text-color) !important;
        }

        /* Ticket Detail Semantic Classes (Brutalist Defaults) */
        .ticket-detail-header {
            /* No special box for header in brutalist, just layout */
            margin-bottom: 2rem;
        }
        
        .btn-action {
            background: #fff;
            border: 2px solid #000 !important;
            box-shadow: 2px 2px 0 #000 !important;
            font-weight: 700;
            color: #000;
            border-radius: 0 !important;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-action:hover {
            box-shadow: 4px 4px 0 #000 !important;
            transform: translate(-1px, -1px);
        }

        .card-ticket {
            border: 3px solid #000 !important;
            box-shadow: 6px 6px 0 0 #000 !important;
            border-radius: 0 !important;
            margin-bottom: 1.5rem;
            background: var(--neo-card-bg);
        }

        .doc-item {
            border: 2px solid #000 !important;
            box-shadow: 3px 3px 0 #000 !important;
            background: #fff;
            border-radius: 0 !important;
            transition: all 0.2s;
        }
        
        /* Note: .stage-circle is already defined in show.blade.php but we should move it global or letting it stay if it overrides? 
           We will remove it from show.blade and define here. */
        .stage-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-weight: bold;
            font-size: 18px;
            border: 3px solid #000 !important;
            box-shadow: 3px 3px 0 #000 !important;
        }

        .badge-priority {
            border: 1px solid #000 !important;
            box-shadow: 2px 2px 0 #000 !important;
            border-radius: 0 !important;
        }
        
        .avatar-initial {
             border: 1px solid rgba(0,0,0,0.1);
        }

        /* Sidebar Layout Styles */
        #wrapper {
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -16rem;
            transition: margin .25s ease-out;
            width: 16rem;
            position: fixed;
            top: 0;
            bottom: 0;
            z-index: 1040; /* Increased to be above sticky-top (1020) */
            background-color: var(--neo-nav-bg);
            border-right: 3px solid var(--neo-border-color);
            display: flex;
            flex-direction: column;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1rem 1.25rem;
            font-size: 1.2rem;
            font-weight: 900;
            color: #fff;
            background-color: rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--neo-border-color);
            height: 70px; /* Match navbar height */
            display: flex;
            align-items: center;
        }

        #sidebar-wrapper .list-group {
            width: 16rem;
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }
        
        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            border: none;
            color: rgba(255,255,255,0.8);
            font-weight: 700;
            padding: 0.75rem 1.25rem;
            border-radius: 0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #sidebar-wrapper .list-group-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            padding-left: 1.5rem;
        }
        
        #sidebar-wrapper .list-group-item.active {
            background-color: var(--neo-card-bg);
            color: var(--neo-text-color);
            border-left: 5px solid var(--neo-border-color);
            border-top: 2px solid var(--neo-border-color);
            border-bottom: 2px solid var(--neo-border-color);
            margin-right: -2px; /* Merge with content */
            position: relative;
            z-index: 2;
        }

        #page-content-wrapper {
            min-width: 100vw;
            transition: margin .25s ease-out;
            background-color: var(--neo-bg-color);
        }
        
        /* Top Navigation in Content Wrapper */
        .navbar-custom {
            border-bottom: 3px solid var(--neo-border-color);
            background-color: var(--neo-card-bg) !important; /* Top bar becomes card color */
            margin-bottom: 0 !important;
            height: 70px;
            position: sticky;
            top: 0;
            z-index: 1030; /* Below sidebar (1040)/overlay (1039) but above content */
        }

        /* Adjust colors for top navbar since it's now content-bg */
        [data-theme="neobrutalism"] .navbar-custom .nav-link, 
        [data-theme="dark"] .navbar-custom .nav-link,
        .navbar-custom .btn-link {
            color: var(--neo-text-color) !important;
            text-shadow: none !important;
        }
        
        #sidebarToggle {
            cursor: pointer;
            color: var(--neo-text-color);
            font-size: 1.5rem;
        }
        
        /* Mobile: Show sidebar when toggled */
        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
                margin-left: 16rem;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: -16rem;
            }

            body.sb-sidenav-toggled #page-content-wrapper {
                margin-left: 0;
            }
        }
        
        /* Modern Gradient Sidebar Overrides */
        [data-theme="modern-gradient"] #sidebar-wrapper {
            background: var(--mg-bg-card) !important; /* Clean white/card bg */
            border-right: none;
            box-shadow: var(--mg-shadow-sm);
        }
        
        [data-theme="modern-gradient"] #sidebar-wrapper .sidebar-heading {
            background: transparent !important;
            color: #1e3a8a !important; /* Dark Blue */
            border-bottom: none;
        }
        
        [data-theme="modern-gradient"] .sidebar-heading .neo-logo {
            background: transparent !important;
            border: none;
            box-shadow: none;
            color: #1e3a8a !important; /* Dark Blue */
            text-shadow: none;
            font-size: 1.4rem; /* Larger logo text */
            white-space: nowrap; /* Prevent wrapping */
            font-weight: 800;
        }
        
        [data-theme="modern-gradient"] .sidebar-label {
            font-size: 0.8rem; /* Scaled up from 0.7 */
            letter-spacing: 0.5px;
            color: var(--mg-primary) !important;
            background: rgba(102, 126, 234, 0.08) !important; /* Very light tint */
            padding: 0.6rem 1.2rem; /* Larger padding */
            margin: 1.2rem 1rem 0.6rem 1rem;
            border-radius: 6px;
        }

        [data-theme="modern-gradient"] #sidebar-wrapper .list-group-item {
            color: #1e3a8a !important;
            font-weight: 600;
        }

        [data-theme="modern-gradient"] #sidebar-wrapper .list-group-item:hover {
            background-color: rgba(30, 58, 138, 0.1);
            color: #1e3a8a !important;
        }

        [data-theme="modern-gradient"] #sidebar-wrapper .list-group-item.active {
            background-color: rgba(30, 58, 138, 0.2);
            color: #1e3a8a !important;
            border-left: 4px solid #1e3a8a;
        }

        [data-theme="modern-gradient"] .sidebar-label {
            color: #1e3a8a !important;
            background: rgba(30, 58, 138, 0.1) !important;
        }

        /* Modern Gradient Topbar overrides */
        [data-theme="modern-gradient"] .navbar-custom {
            background: rgba(255, 255, 255, 0.9) !important;
            border-bottom: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Slight shadow */
        }
        
        [data-theme="modern-gradient"] .navbar-custom .nav-link,
        [data-theme="modern-gradient"] .navbar-custom .btn-outline-light,
        [data-theme="modern-gradient"] .navbar-custom .text-muted {
            color: #1e3a8a !important;
            border-color: #1e3a8a !important;
        }
        
        [data-theme="modern-gradient"] .table-dark {
            background-color: #1e3a8a !important;
            color: #fff;
        }
        
        [data-theme="modern-gradient"] .table-dark th {
            background-color: #1e3a8a !important;
            border-color: rgba(255,255,255,0.1);
        }

        [data-theme="modern-gradient"] #sidebarToggle {
            color: #1e3a8a !important;
            background: rgba(30, 58, 138, 0.1);
            border-radius: 8px;
            padding: 5px 10px;
            margin-right: 10px;
        }
        
        [data-theme="modern-gradient"] #sidebarToggle:hover {
            background: rgba(30, 58, 138, 0.2);
            transform: translateY(-2px);
        }
        
        /* Sidebar Overlay */
        #sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1039; /* Below sidebar (1040) but above sticky (1020) */
            backdrop-filter: blur(2px);
            transition: opacity 0.3s ease;
        }

        /* Modern Gradient Global Styles */
        [data-theme="modern-gradient"] body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        [data-theme="modern-gradient"] #page-content-wrapper {
            background: transparent;
        }

        [data-theme="modern-gradient"] .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: none !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border-radius: 1rem !important;
        }

        [data-theme="modern-gradient"] .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        
        [data-theme="modern-gradient"] .navbar-custom {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
        }
    </style>
    
    <!-- CRITICAL: Theme CSS must load AFTER inline styles for proper precedence -->
    <link id="theme-css" rel="stylesheet" href="">
    
    <!-- Remove duplicate old theme script - already handled above -->
</head>
<body>
    <div id="app" class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading">
                <a class="neo-logo text-decoration-none" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
            <div class="list-group list-group-flush">
                @auth
                    <a class="list-group-item list-group-item-action {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a class="list-group-item list-group-item-action {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <i class="bi bi-briefcase me-2"></i> Projects
                    </a>
                    <a class="list-group-item list-group-item-action {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                        <i class="bi bi-list-check me-2"></i> My Tasks
                    </a>
                    <a class="list-group-item list-group-item-action {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                        <i class="bi bi-ticket-perforated me-2"></i> Tickets
                    </a>
                    
                    @if(Auth::user()->role === 'admin')
                        <div class="sidebar-label mt-3 mb-1 text-uppercase fw-bold">Administration</div>
                        <a class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="bi bi-people me-2"></i> Manage Users
                        </a>
                        <a class="list-group-item list-group-item-action {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                            <i class="bi bi-building me-2"></i> Departments
                        </a>
                        <a class="list-group-item list-group-item-action {{ request()->routeIs('statuses.*') ? 'active' : '' }}" href="{{ route('statuses.index') }}">
                            <i class="bi bi-kanban me-2"></i> Statuses
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light navbar-custom px-3">
                <div class="d-flex align-items-center w-100">
                    <button class="btn btn-link" id="sidebarToggle">
                        <i class="bi bi-grid-fill fs-4"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto align-items-center">
                            {{-- Impersonation Indicator --}}
                            @if(session('impersonate_original_user'))
                            <li class="nav-item me-3">
                                <form action="{{ route('leave-impersonation') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning border border-2 border-dark fw-bold" style="box-shadow: 3px 3px 0 #000;">
                                        <i class="bi bi-box-arrow-left"></i> Leave Impersonation
                                    </button>
                                </form>
                            </li>
                            @endif
                            
                            <li class="nav-item dropdown me-3">
                                <button class="btn btn-sm btn-outline-light dropdown-toggle" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Change Theme">
                                    <i class="bi bi-palette"></i> Theme
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="themeDropdown">
                                    <li>
                                        <button class="dropdown-item theme-option" data-theme="neobrutalism">
                                            <i class="bi bi-lightning-fill text-danger"></i> Neo-Brutalism
                                            <span class="theme-check ms-2 d-none"><i class="bi bi-check2"></i></span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item theme-option" data-theme="modern-gradient">
                                            <i class="bi bi-stars text-primary"></i> Modern Gradient
                                            <span class="theme-check ms-2 d-none"><i class="bi bi-check2"></i></span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item theme-option" data-theme="dark">
                                            <i class="bi bi-moon-stars-fill text-secondary"></i> Dark Mode
                                            <span class="theme-check ms-2 d-none"><i class="bi bi-check2"></i></span>
                                        </button>
                                    </li>
                                </ul>
                            </li>
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }} 
                                        <span class="badge bg-primary ms-1">{{ ucfirst(Auth::user()->role) }}</span>
                                        @if(Auth::user()->departments->isNotEmpty())
                                            <span class="badge bg-secondary ms-1">
                                                <i class="bi bi-building me-1"></i>{{ Auth::user()->departments->first()->name }}
                                            </span>
                                        @endif
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>
        </div>
            </main>
        </div>
    </div>
    
    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebar-overlay" class="d-none"></div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            // Sidebar Toggle
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const wrapper = document.getElementById('wrapper');
            
            // Create Close Button for Mobile Sidebar
            const sidebarWrapper = document.getElementById('sidebar-wrapper');
            if (sidebarWrapper) {
                const closeBtn = document.createElement('button');
                closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
                closeBtn.className = 'btn btn-link text-white position-absolute top-0 end-0 m-2 d-md-none sidebar-close-btn';
                closeBtn.style.zIndex = '1001';
                closeBtn.style.opacity = '0.8';
                
                // Add to sidebar header or top of sidebar
                const heading = sidebarWrapper.querySelector('.sidebar-heading');
                if (heading) {
                    heading.style.position = 'relative'; // Ensure positioning context
                    heading.appendChild(closeBtn);
                } else {
                    sidebarWrapper.appendChild(closeBtn);
                }
                
                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.body.classList.remove('sb-sidenav-toggled');
                    sidebarOverlay.classList.add('d-none');
                });
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    
                    // Toggle overlay visibility on mobile
                    if (window.innerWidth < 768) {
                        if (document.body.classList.contains('sb-sidenav-toggled')) {
                            sidebarOverlay.classList.remove('d-none');
                        } else {
                            sidebarOverlay.classList.add('d-none');
                        }
                    }
                });
            }
            
            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', () => {
                   document.body.classList.remove('sb-sidenav-toggled');
                   sidebarOverlay.classList.add('d-none');
                });
            }

            // Theme Switcher Logic
            const themeOptions = document.querySelectorAll('.theme-option');
            const themeChecks = document.querySelectorAll('.theme-check');
            
            // Update active theme indicator
            function updateActiveTheme(selectedTheme) {
                themeOptions.forEach(option => {
                    const check = option.querySelector('.theme-check');
                    if (option.dataset.theme === selectedTheme) {
                        check.classList.remove('d-none');
                    } else {
                        check.classList.add('d-none');
                    }
                });
            }
            
            // Show current theme on load
            const currentTheme = localStorage.getItem('app-theme') || 'neobrutalism';
            updateActiveTheme(currentTheme);
            
            // Handle theme change
            themeOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newTheme = this.dataset.theme;
                    
                    console.log('[THEME SWITCH] User clicked:', newTheme);
                    
                    // CRITICAL: Save to localStorage immediately and verify
                    try {
                        localStorage.setItem('app-theme', newTheme);
                        const savedValue = localStorage.getItem('app-theme');
                        
                        if (savedValue !== newTheme) {
                            console.error('[THEME SWITCH] ERROR: localStorage did not save correctly!');
                            alert('Error saving theme preference. Please try again.');
                            return;
                        }
                    } catch(error) {
                        console.error('[THEME SWITCH] localStorage error:', error);
                        alert('Cannot save theme preference');
                        return;
                    }
                    
                    // Update data-theme attribute on HTML element
                    document.documentElement.setAttribute('data-theme', newTheme);
                    
                    // Update CSS link dynamically
                    const themeLink = document.getElementById('theme-css');
                    if (themeLink) {
                        if (newTheme === 'modern-gradient') {
                            const timestamp = new Date().getTime();
                            const cssUrl = '{{ asset("css/themes/modern-gradient.css") }}';
                            themeLink.href = cssUrl + '?v=' + timestamp;
                        } else {
                            themeLink.href = '';
                        }
                    }
                    
                    // Update active indicator
                    updateActiveTheme(newTheme);
                    
                    // Show success toast
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed top-0 end-0 p-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                        <div class="toast show alert alert-success" role="alert">
                            <div class="toast-header bg-success text-white">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong class="me-auto">Theme Applied!</strong>
                                <button type="button" class="btn-close btn-close-white" onclick="this.closest('.toast').remove()"></button>
                            </div>
                            <div class="toast-body">
                                Successfully switched to <strong>${this.textContent.trim()}</strong>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
