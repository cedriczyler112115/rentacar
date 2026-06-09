<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $moduleTitle = 'Auth';
            if (request()->routeIs('login')) $moduleTitle = 'Login';
            elseif (request()->routeIs('register')) $moduleTitle = 'Register';
            elseif (request()->routeIs('password.request') || request()->routeIs('password.email')) $moduleTitle = 'Forgot Password';
            elseif (request()->routeIs('password.reset') || request()->routeIs('password.update')) $moduleTitle = 'Reset Password';
            elseif (request()->routeIs('verification.notice')) $moduleTitle = 'Verify Email';
            elseif (request()->routeIs('google.login') || request()->routeIs('google.callback')) $moduleTitle = 'Google Login';
        @endphp
        <title>Auto Amegos Rent-a-Car - {{ $moduleTitle }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="margin: 0; min-height: 100vh; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: 100%; max-width: 450px; display: flex; flex-direction: column; align-items: center; position: relative;">
            <a href="javascript:history.back()" style="position: absolute; left: 0; top: 15px; color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back
            </a>
            <a href="/" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; margin-bottom: 30px;">
                <x-application-logo style="height: 70px; width: auto; margin-bottom: 15px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));" />
                <div style="font-size: 2rem; font-weight: 800; color: white; letter-spacing: 2px; font-family: 'Outfit', sans-serif;">AARACC<span style="color: #f59e0b;">.</span></div>
            </a>

            <div style="background: white; width: 100%; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3); padding: 40px; color: #0f172a;" class="auth-card-body">
                <style>
                    /* Premium Auth Form Overrides */
                    .auth-card-body input:not([type="checkbox"]) { border-radius: 8px !important; border: 1px solid #cbd5e1 !important; padding: 12px 16px !important; width: 100% !important; transition: all 0.2s !important; box-shadow: none !important; }
                    .auth-card-body input:not([type="checkbox"]):focus { border-color: #f59e0b !important; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important; outline: none !important; }
                    .auth-card-body button[type="submit"], .auth-card-body .bg-gray-800 { background: #f59e0b !important; color: #0f172a !important; font-weight: 700 !important; padding: 12px 24px !important; border-radius: 8px !important; width: 100% !important; border: none !important; cursor: pointer !important; transition: all 0.3s !important; font-size: 1.05rem !important; display: flex !important; justify-content: center !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; }
                    .auth-card-body button[type="submit"]:hover, .auth-card-body .bg-gray-800:hover { background: #d97706 !important; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.4) !important; }
                    .auth-card-body a { color: #3b82f6 !important; text-decoration: none !important; transition: all 0.2s; font-weight: 500 !important; }
                    .auth-card-body a:hover { color: #1d4ed8 !important; text-decoration: underline !important; }
                    .auth-card-body label { font-weight: 600 !important; color: #334155 !important; margin-bottom: 6px !important; display: block; font-size: 0.95rem !important; }
                    .auth-card-body .text-gray-600 { color: #64748b !important; }
                    .auth-card-body .social-btn { border: 1px solid #e2e8f0 !important; background: white !important; color: #334155 !important; border-radius: 8px !important; }
                    .auth-card-body .social-btn:hover { background: #f8fafc !important; border-color: #cbd5e1 !important; }
                </style>
                {{ $slot }}
            </div>
        </div>
        @include('partials.global-loader')
        @include('partials.tawk')
        @include('partials.cookie-consent')
    </body>
</html>
