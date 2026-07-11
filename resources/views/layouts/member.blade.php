<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $moduleTitle = 'Member Panel';
        $isAaraccUser = (bool) (Auth::user()?->is_aaracc);

        if (request()->routeIs('dashboard')) $moduleTitle = 'My Bookings';
        elseif (request()->routeIs('bookings.manage')) $moduleTitle = 'Client Bookings';
        elseif (request()->routeIs('vehicles.index')) $moduleTitle = 'All Cars';
        elseif (request()->routeIs('my-cars.*')) $moduleTitle = 'My Cars';
        elseif (request()->routeIs('rentals.create') || request()->routeIs('rentals.store')) $moduleTitle = 'Booking';
        elseif (request()->routeIs('municipalities.*')) $moduleTitle = 'Price per Location';
        elseif (request()->routeIs('profile.*')) $moduleTitle = 'Profile';
        elseif (request()->routeIs('admin.index')) $moduleTitle = 'Admin Dashboard';
        elseif (request()->routeIs('admin.users.*')) $moduleTitle = 'Users';
        elseif (request()->routeIs('admin.dispatching.*')) $moduleTitle = 'Dispatching';
        elseif (request()->routeIs('admin.service-fee-payments.*')) $moduleTitle = 'Service Fee Payments';
        elseif (request()->routeIs('admin.carwash-service-payments.*')) $moduleTitle = 'Carwash Service Payments';
        elseif (request()->routeIs('admin.owner-ratings.*')) $moduleTitle = 'Owner Ratings';
        elseif (request()->routeIs('admin.*')) $moduleTitle = 'Admin';
    @endphp
    <title>Auto Amegos Rent-a-Car - {{ $moduleTitle }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery & jQuery-Confirm -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles from welcome.blade.php -->
    <style>
        :root {
            --primary: #0f172a; /* Slate 900 */
            --primary-light: #1e293b; /* Slate 800 */
            --accent: #f59e0b; /* Amber 500 */
            --accent-hover: #d97706; /* Amber 600 */
            --text-main: #f8fafc; /* Slate 50 */
            --text-muted: #cbd5e1; /* Slate 300 */
            --bg-color: #f1f5f9; /* Slate 100 */
            --card-bg: #ffffff;
            --shadow-sm: 0 4px 6px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-lg: 0 20px 25px rgba(0,0,0,0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--primary);
            line-height: 1.6;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container { width: 100%; max-width: 100%; margin: 0 auto; padding: 0 40px; }
        @media (max-width: 767.98px) {
            .container { padding: 0 16px; }
            main { padding-top: 86px; margin-bottom: 24px; }
            .page-header { margin-bottom: 18px; border-radius: 10px; }
            .page-header h2 { font-size: 1.4rem; }
            .toast { min-width: auto; width: calc(100vw - 32px); max-width: calc(100vw - 32px); }
        }
        .admin-layout { display: grid; grid-template-columns: 1fr; gap: 18px; align-items: start; }
        .admin-sidebar { width: 100%; }
        .admin-content { width: 100%; min-width: 0; }
        .admin-form-grid-2 { display:grid; grid-template-columns: 1fr; gap: 16px; }
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        @media (min-width: 768px) { .admin-form-grid-2 { grid-template-columns: 1fr 1fr; } }
        @media (min-width: 992px) { .admin-layout { grid-template-columns: 280px 1fr; } }
        a { text-decoration: none; color: inherit; }
        button, .btn { display: inline-block; cursor: pointer; border: none; outline: none; font-family: 'Outfit', sans-serif; font-weight: 600; border-radius: 8px; transition: var(--transition); }
        .btn-primary { background-color: var(--accent); color: var(--primary); padding: 12px 30px; font-size: 1.1rem; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4); }
        .btn-primary:hover { background-color: var(--accent-hover); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6); }
        .btn-outline { background: transparent; border: 2px solid var(--accent); color: var(--accent); padding: 10px 25px; font-size: 1rem; }
        .btn-outline:hover { background: var(--accent); color: var(--primary); }

        @media (min-width: 768px) {
            .jconfirm .jconfirm-box-container {
                width: 30% !important;
                max-width: 30% !important;
                min-width: 360px !important;
                margin: 0 auto !important; /* center horizontally */
            }
        }

        @media (max-width: 767.98px) {
            .jconfirm .jconfirm-box-container {
                width: 92% !important;
                max-width: 92% !important;
                min-width: auto !important;
                margin: 0 auto !important; /* center horizontally */
            }
        }

        /* Navbar */
        .site-header { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; padding: 20px 0; transition: var(--transition); background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .nav-container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: 800; color: var(--text-main); letter-spacing: 1px; display: flex; align-items: center; gap: 10px; }
        .logo span { color: var(--accent); }
        .nav-links { display: flex; align-items: center; gap: 30px; }
        .nav-links a,
        .nav-links button.nav-dropdown-trigger {
            color: var(--text-main);
            font-weight: 500;
            transition: var(--transition);
            opacity: 0.9;
        }
        .nav-links a.active,
        .nav-links a:hover,
        .nav-links button.nav-dropdown-trigger.active,
        .nav-links button.nav-dropdown-trigger:hover {
            color: var(--accent);
            opacity: 1;
        }
        .nav-owner-rating { margin-right: 14px; display:inline-flex; align-items:center; gap:8px; padding: 8px 14px; border-radius: 999px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.22); color: #10b981; font-weight: 800; font-size: 0.9rem; white-space: nowrap; }
        .nav-owner-rating .dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; display:inline-block; }
        .nav-owner-rating .muted { color: rgba(226, 232, 240, 0.92); font-weight: 800; }
        .login-btn-wrapper { margin-left: 20px; position: relative;}
        .mobile-menu-btn { display: none; color: white; background: none; font-size: 1.5rem; cursor: pointer; }
        
        main { padding-top: 100px; flex: 1; margin-bottom: 40px;}

        .nav-dropdown { position: relative; display: inline-flex; align-items: center; }
        .nav-dropdown-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            padding: 0;
            font-size: inherit;
            line-height: inherit;
        }
        .nav-dropdown-trigger svg { transition: var(--transition); }
        .nav-dropdown .nav-dropdown-trigger[aria-expanded="true"] svg { transform: rotate(180deg); }

        .nav-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 290px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 18px 35px rgba(0,0,0,0.18);
            margin-top: 0;
            padding: 10px;
            opacity: 0;
            transform: translateY(6px);
            pointer-events: none;
            transition: opacity 0.18s ease, transform 0.18s ease;
            z-index: 1500;
        }

        .nav-dropdown .nav-dropdown-trigger[aria-expanded="true"] + .nav-dropdown-menu { opacity: 1; transform: translateY(0); pointer-events: auto; }

        .nav-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--primary) !important;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
        }
        .nav-dropdown-item:hover,
        .nav-dropdown-item:focus { background: #f1f5f9; color: var(--accent) !important; outline: none; }

        /* Dropdown styling */
        .dropdown-menu {
            position: absolute; right: 0; top: 120%; background: white; border-radius: 8px; padding: 10px 0; min-width: 150px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; display: none; flex-direction: column; z-index: 100;
        }
        .dropdown-menu.active { display: flex;}
        .dropdown-item { padding: 10px 20px; color: var(--primary) !important; font-size: 0.95rem; transition: var(--transition); cursor: pointer; width: 100%; text-align: left; background: none; border: none; font-family: 'Outfit', sans-serif; font-weight: 500;}
        .dropdown-item:hover { background: #f1f5f9; color: var(--accent); }

        /* Dashboard specific styles */
        .page-header { background: white; padding: 15px 0; border-bottom: 1px solid #e2e8f0; margin-bottom: 30px; box-shadow: var(--shadow-sm); border-radius: 12px;}
        .page-header h2 { font-size: 1.8rem; font-weight: 700; color: var(--primary); }
        
        .profile-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid #e2e8f0;
            padding: 30px;
            transition: var(--transition);
        }
        .profile-card:hover {
            box-shadow: var(--shadow-lg);
        }

        /* Toast Notification */
        .toast-container { position: fixed; top: 90px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .toast { background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px; pointer-events: auto; min-width: 320px; max-width: 400px; border: 1px solid #f1f5f9; }
        .toast.success { border-left: 5px solid #10b981; }
        .toast.error { border-left: 5px solid #ef4444; }
        .toast-icon { margin-top: 2px; }
        .toast-content { flex: 1; }
        .toast-title { font-weight: 700; font-size: 0.95rem; color: #0f172a; font-family: 'Outfit', sans-serif; line-height: 1.2; }
        .toast-message { font-size: 0.85rem; color: #64748b; margin-top: 4px; font-family: 'Outfit', sans-serif; line-height: 1.4; }
        .toast-close { background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0; outline: none; }
        .toast-close:hover { color: #475569; }
        .error-list { margin-top: 5px; padding-left: 15px; color: #ef4444; font-size: 0.85rem; }
        
        /* Footer */
        footer { background-color: #020617; color: #94a3b8; padding: 60px 0 20px; margin-top: auto;}
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 40px; }
        .footer-col h4 { color: white; font-size: 1.2rem; margin-bottom: 20px; font-weight: 700; }
        .footer-col.about p { margin-top: 15px; line-height: 1.5; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { transition: var(--transition); }
        .footer-links a:hover { color: var(--accent); padding-left: 5px; }
        .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid #1e293b; font-size: 0.9rem; }

        @media (max-width: 900px) {
            .nav-links { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: rgba(15, 23, 42, 0.98); padding: 20px; flex-direction: column; box-shadow: 0 10px 20px rgba(0,0,0,0.2); align-items: flex-start; }
            .nav-links.active { display: flex !important; }
            .mobile-menu-btn { display: block; margin-left: 10px; }
            .nav-dropdown { width: 100%; display: flex; flex-direction: column; align-items: stretch; }
            .nav-dropdown-trigger { width: 100%; justify-content: space-between; }
            .nav-dropdown-menu {
                position: static;
                top: auto;
                left: auto;
                width: 100%;
                min-width: 0;
                margin-top: 10px;
                display: none;
                opacity: 1;
                transform: none;
                pointer-events: auto;
                box-shadow: none;
                border-radius: 10px;
            }
            .nav-dropdown .nav-dropdown-trigger[aria-expanded="true"] + .nav-dropdown-menu { display: block; }
            
            .nav-owner-rating { margin-right: 8px; padding: 6px 10px; font-size: 0.8rem; gap: 4px; }
            .nav-owner-rating span:nth-child(2) { display: none; }
            
            .login-btn-wrapper .btn-outline { padding: 6px 10px; font-size: 0.85rem; gap: 4px; }
            .login-user-name { display: none; }
            
            .logo { font-size: 1.4rem; gap: 5px; }
            .logo img { height: 30px !important; }
        }
    </style>
</head>
<body>

    <!-- Global Toast Notifications -->
    <div class="toast-container">
        @if (session('status') || session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="toast success">
                 <div class="toast-icon text-green-500">
                     <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                 </div>
                 <div class="toast-content">
                    <div class="toast-title">Success</div>
                    <div class="toast-message">
                        @if (session('status') === 'profile-updated') Your profile information has been updated.
                        @elseif (session('status') === 'password-updated') Your password has been updated securely.
                        @elseif (session('status') === 'verification-link-sent') A new verification link has been sent to your email address.
                        @else {{ session('status') ?? session('success') }}
                        @endif
                    </div>
                 </div>
                 <button @click="show = false" class="toast-close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                 </button>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="toast error">
                 <div class="toast-icon">
                     <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                 </div>
                 <div class="toast-content">
                    <div class="toast-title">Error saving data</div>
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                 </div>
                 <button @click="show = false" class="toast-close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                 </button>
            </div>
        @endif
    </div>

    <header class="site-header">
        <div class="container nav-container">
            <div style="display: flex; align-items: center; gap: 40px;">
                <a class="logo" style="cursor: default;">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="AARACC Logo" style="height: 40px; width: auto;">
                    AARACC
                </a>
                
                <nav class="nav-links">
                    @if($isAaraccUser)
                        <div class="nav-dropdown">
                            <button type="button"
                               class="nav-dropdown-trigger {{ request()->routeIs('dashboard') || request()->routeIs('bookings.manage') || request()->routeIs('booking.calendar*') || request()->routeIs('my-income') ? 'active' : '' }}"
                               id="bookingsNavTrigger"
                               aria-haspopup="true"
                               aria-expanded="false">
                                Bookings
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div class="nav-dropdown-menu" role="menu" aria-labelledby="bookingsNavTrigger">
                                <a role="menuitem" href="{{ route('dashboard') }}" class="nav-dropdown-item">My Bookings</a>
                                <a role="menuitem" href="{{ route('my-income') }}" class="nav-dropdown-item">My Income</a>
                                <a role="menuitem" href="{{ route('booking.calendar') }}" class="nav-dropdown-item">My Booking Calendar</a>
                                <a role="menuitem" href="{{ route('bookings.manage') }}" class="nav-dropdown-item">My Client Bookings</a>
                            </div>
                        </div>
                        <a href="{{ route('my-cars.index') }}" class="{{ request()->routeIs('my-cars.*') ? 'active' : '' }}">My Vehicles</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">My Bookings</a>
                    @endif
                    <a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">All Vehicles</a>
                    @if($isAaraccUser)
                        <a href="{{ route('municipalities.index') }}" class="{{ request()->routeIs('municipalities.*') ? 'active' : '' }}">Price per Location</a>
                        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') && !request()->routeIs('admin.loans.*') && !request()->routeIs('admin.loan-collections.*') && !request()->routeIs('admin.member-capitals.*') ? 'active' : '' }}">AARACC Member Panel</a>
                    @endif
                    <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') || request()->routeIs('admin.loans.*') || request()->routeIs('admin.loan-collections.*') || request()->routeIs('admin.member-capitals.*') ? 'active' : '' }}">AARACC Loan</a>
                </nav>
            </div>
            
            <div style="display: flex; align-items: center;">
                @php
                    $navHasVehicles = \App\Models\Vehicle::query()->where('user_id', Auth::id())->exists();
                    $navOwnerAvg = 0;
                    $navOwnerCount = 0;
                    if ($navHasVehicles) {
                        $ownerReviewRow = \App\Models\Review::query()
                            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
                            ->where('owner_id', Auth::id())
                            ->first();
                        $navOwnerAvg = $ownerReviewRow?->avg_rating ? round((float) $ownerReviewRow->avg_rating, 1) : 0;
                        $navOwnerCount = (int) ($ownerReviewRow?->total_reviews ?? 0);
                    }
                @endphp
                @if($navHasVehicles)
                    <div class="nav-owner-rating" title="Owner rating based on reviews">
                        <span class="dot"></span>
                        <span>Owner Rating:</span>
                        @if($navOwnerCount > 0)
                            <span class="muted">★ {{ number_format($navOwnerAvg, 1) }} ({{ $navOwnerCount }})</span>
                        @else
                            <span class="muted">No reviews yet</span>
                        @endif
                    </div>
                @endif
                <div class="login-btn-wrapper" x-data="{ open: false }" style="margin-left: 0;">
                    <button @click="open = !open" @click.away="open = false" class="btn btn-outline" style="display: flex; align-items: center; gap: 8px;">
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="Profile Photo" style="width: 28px; height: 28px; border-radius: 999px; object-fit: cover; border: 2px solid rgba(245, 158, 11, 0.6);">
                        @else
                            <span style="width: 28px; height: 28px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); border: 2px solid rgba(245, 158, 11, 0.6); display:inline-flex; align-items:center; justify-content:center; font-weight: 900; color: var(--accent);">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                        <span class="login-user-name">{{ Auth::user()->name }}</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    
                    <div class="dropdown-menu" :class="{'active': open}">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

                <button class="mobile-menu-btn" style="margin-left: 20px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main>
        @isset($header)
            <div class="page-header">
                <div class="container" style="padding: 0 50px;">
                    {{ $header }}
                </div>
            </div>
        @endisset
        {{ $slot }}
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col about">
                    <a class="logo" style="margin-bottom: 15px; cursor: default;">AARACC <span>.</span></a>
                    <p>Auto Amegos Rent A Car Co.<br>Providing top-tier rental solutions in Butuan City. Your journey is our priority.</p>
                </div>
                
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('vehicles.index') }}">All Vehicles</a></li>
                        <li><a href="#pricing">Price per Location</a></li>
                        <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <ul class="footer-links" style="color: #94a3b8;">
                        <li style="display: flex; gap: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            8th Street, Village 2, Libertad, Butuan City, Agusan Del Norte Philippines 8600
                        </li>
                        <li style="display: flex; gap: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            +63 900 123 4567
                        </li>
                        <li style="display: flex; gap: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            bookings@aaracc.com
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Auto Amegos Rent A Car Co. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        const header = document.querySelector('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.style.background = 'rgba(15, 23, 42, 0.95)';
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
            } else {
                header.style.background = 'rgba(15, 23, 42, 0.85)';
                header.style.boxShadow = 'none';
            }
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>
    <script>
        $(document).ready(function() {
            $('body').on('submit', 'form.confirm-delete', function(e) {
                e.preventDefault();
                let form = this;
                $.confirm({
                    title: 'Confirm Deletion',
                    content: 'Are you sure you want to perform this action? This cannot be undone.',
                    type: 'red',
                    theme: 'modern',
                    icon: 'fa fa-warning',
                    buttons: {
                        confirm: {
                            text: 'Yes, Delete',
                            btnClass: 'btn-red',
                            action: function () {
                                form.submit();
                            }
                        },
                        cancel: function () {}
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('bookingsNavTrigger');
            if (!trigger) return;
            const container = trigger.closest('.nav-dropdown');
            if (!container) return;

            const setExpanded = (value) => trigger.setAttribute('aria-expanded', value ? 'true' : 'false');
            const isMobile = () => window.matchMedia('(max-width: 900px)').matches;

            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                if (isMobile()) {
                    setExpanded(trigger.getAttribute('aria-expanded') !== 'true');
                    return;
                }

                setExpanded(trigger.getAttribute('aria-expanded') !== 'true');
            });

            container.addEventListener('mouseenter', () => {
                if (!isMobile()) setExpanded(true);
            });
            container.addEventListener('focusin', () => {
                if (!isMobile()) setExpanded(true);
            });
            container.addEventListener('mouseleave', () => {
                if (!isMobile()) setExpanded(false);
            });

            document.addEventListener('click', (e) => {
                if (!container.contains(e.target)) {
                    setExpanded(false);
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') setExpanded(false);
            });

            window.addEventListener('resize', () => {
                if (!isMobile()) setExpanded(false);
            });
        });
    </script>
    @include('partials.global-loader')
    @include('partials.tawk')
    @include('partials.cookie-consent')
</body>
</html>
