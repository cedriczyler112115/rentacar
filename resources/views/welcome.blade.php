<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="scroll-behavior: smooth;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Auto Amegos Rent-a-Car - Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('images/carousel_6.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary: #0f172a; /* Slate 900 */
            --primary-light: #1e293b; /* Slate 800 */
            --accent: #f59e0b; /* Amber 500 */
            --accent-hover: #d97706; /* Amber 600 */
            --text-main: #f8fafc; /* Slate 50 */
            --shadow-sm: 0 4px 6px -1px rgba(0,0,0,0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --bg-color: #f1f5f9;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--bg-color); margin: 0; padding: 0; color: var(--primary); }
        
        /* Full Width Header */
        .public-header {
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(15, 23, 42, 0.95); padding: 20px 4%; box-shadow: var(--shadow-sm);
            position: sticky; top: 0; z-index: 50; backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header-logo { font-size: clamp(1.1rem, 3vw, 1.8rem); letter-spacing: 0.5px; font-weight: 800; color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .header-links { display: flex; align-items: center; }
        .header-links a { margin-left: 20px; font-weight: 600; color: #cbd5e1; text-decoration: none; transition: var(--transition); }
        .header-links a:hover { color: var(--accent); }
        
        /* Mobile Menu */
        .mobile-menu-btn { display: none; background: none; border: none; color: white; cursor: pointer; padding: 0; outline: none; }
        .mobile-menu-btn:hover { color: var(--accent); }
        .mobile-menu { display: none; flex-direction: column; position: absolute; top: 100%; left: 0; width: 100%; background: rgba(15, 23, 42, 0.98); padding: 20px 4%; box-sizing: border-box; border-bottom: 1px solid rgba(255,255,255,0.1); z-index: 40; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5); }
        .mobile-menu.active { display: flex; animation: slideDown 0.3s ease forwards; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .mobile-menu a { color: #cbd5e1; text-decoration: none; font-weight: 600; font-size: 1.1rem; padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.05); transition: var(--transition); }
        .mobile-menu a:hover { color: var(--accent); padding-left: 10px; }
        .mobile-menu .auth-buttons { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; border-bottom: none; }
        .mobile-menu .auth-buttons a { padding: 12px; text-align: center; border-bottom: none; }
        .mobile-menu .auth-buttons a:hover { padding-left: 0; background: rgba(255,255,255,0.05); border-radius: 8px; }
        
        @media (max-width: 1024px) {
            .header-links { display: none; }
            .mobile-menu-btn { display: block; }
        }
        
        /* Full Width Hero with Slideshow */
        .hero-section {
            position: relative;
            padding: 100px 4%; text-align: center; color: white; box-sizing: border-box;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.95));
            overflow: hidden;
            min-height: calc(100vh - 78px);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        .hero-slider { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; }
        .hero-slide {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover; background-position: center;
            opacity: 0; transition: opacity 1.5s ease-in-out;
        }
        .hero-slide.active { opacity: 1; transform: scale(1.02); transition: opacity 1.5s ease-in-out, transform 10s linear; }
        
        .hero-content { position: relative; z-index: 10; width: 100%; max-width: 900px; margin: 0 auto; }
        
        /* Full Width Features Grid */
        .features-grid {
            width: 100%; padding: 80px 4%; background: white; box-sizing: border-box;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 50px;
        }
        
        .feature-card { text-align: center; padding: 20px; }
        .feature-icon { width: 70px; height: 70px; background: #eff6ff; color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; transition: transform 0.3s; }
        .feature-card:hover .feature-icon { transform: translateY(-8px); background: var(--accent); color: white; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }

        /* Full Width Fleet Layout */
        .fleet-container { width: 100%; padding: 80px 4%; box-sizing: border-box; background: #f1f5f9; }
        .vehicle-layout { display: flex; gap: 40px; }
        .filter-sidebar { flex: 0 0 300px; background: white; padding: 30px; border-radius: 12px; box-shadow: var(--shadow-sm); align-self: flex-start; position: sticky; top: 120px; }
        .vehicle-main { flex: 1; min-width: 0; }
        
        /* Vehicle Grid (Matching Member Area Responsive) */
        .vehicle-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        
        @media (max-width: 1024px) {
            .vehicle-layout { flex-direction: column; }
            .filter-sidebar { flex: auto; width: 100%; position: static; }
            .location-reviews-grid { grid-template-columns: 1fr !important; gap: 40px !important; }
        }
        
        /* Buttons */
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: var(--transition); border: none; text-decoration: none; display: inline-block; font-family: 'Outfit', sans-serif; }
        .btn-primary { background: var(--accent); color: var(--primary); box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4); }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6); color: var(--primary); }
        .btn-outline { background: transparent; border: 2px solid var(--accent); color: var(--accent); }
        .btn-outline:hover { background: var(--accent); color: var(--primary); }
        .btn-outline-light { background: transparent; border: 2px solid rgba(255,255,255,0.8); color: white; }
        .btn-outline-light:hover { background: white; color: var(--primary); }
        .btn-block { width: 100%; text-align: center; box-sizing: border-box; }
        
        /* Inputs */
        .filter-custom-input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Outfit', sans-serif; transition: var(--transition); box-sizing: border-box; background: var(--bg-color); }
        .filter-custom-input:focus { outline: none; border-color: var(--accent); background: white; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }
        .filter-label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary); font-size: 0.95rem; }
        
        footer { background: var(--primary); color: #94a3b8; padding: 80px 4% 30px; margin-top: 0; }
        
        /* Vehicle Card (Member UI Clone) */
        .vehicle-card { background: white; border-radius: 12px; overflow: visible; box-shadow: var(--shadow-sm); transition: var(--transition); border: 1px solid #e2e8f0; }
        .vehicle-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .card-img-wrapper { position: relative; height: 220px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; }
        .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
        .card-img-wrapper:hover img { transform: scale(1.05); }
        
        .status-badge { position: absolute; top: 15px; right: 15px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: white; }
        .availability-tooltip { position: relative; display: inline-flex; align-items: center; }
        .availability-tooltip:hover::after { content: attr(data-tooltip); position: absolute; bottom: calc(100% + 10px); left: 0; background: rgba(2, 6, 23, 0.95); color: white; padding: 10px 12px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; white-space: pre-line; min-width: 220px; max-width: 280px; box-shadow: 0 15px 40px rgba(0,0,0,0.25); z-index: 9999; }
        .availability-tooltip:hover::before { content: ''; position: absolute; bottom: calc(100% + 4px); left: 14px; border: 6px solid transparent; border-top-color: rgba(2, 6, 23, 0.95); z-index: 10000; }
    </style>
</head>
<body>
    <header class="public-header">
        <a class="header-logo" style="cursor: default;">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Auto Amegos" style="height: 35px;" onerror="this.style.display='none'">
            Auto Amegos Rent-A-Car Co<span style="color: var(--accent);">.</span>
        </a>
        <div class="header-links">
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#fleet">Our Fleet</a>
            <a href="#location" style="margin-right: 100px;">Our Location</a>
            @auth
                <a href="{{ route('loans.index') }}" class="btn btn-primary" style="padding: 10px 24px; margin-left: 0; margin-right: 10px;">AARACC Loan</a>
                <a href="{{ url('/my-bookings') }}" class="btn btn-outline" style="padding: 10px 24px; margin-left: 0;">Go to Bookings</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 10px 24px; margin-right: 10px; margin-left: 0;">Log In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 10px 24px; margin-left: 0;">Sign Up</a>
                @endif
            @endauth
        </div>
        
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <div id="mobileMenu" class="mobile-menu">
            <a href="#features" onclick="toggleMobileMenu()">Features</a>
            <a href="#about" onclick="toggleMobileMenu()">About</a>
            <a href="#how-it-works" onclick="toggleMobileMenu()">How It Works</a>
            <a href="#fleet" onclick="toggleMobileMenu()">Our Fleet</a>
            <a href="#location" onclick="toggleMobileMenu()">Location</a>
            <div class="auth-buttons">
                @auth
                    <a href="{{ route('loans.index') }}" class="btn btn-primary btn-block" style="color: white;">AARACC Loan</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline btn-block" style="color: var(--accent);">Go to Bookings</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-block" style="color: var(--accent);">Log In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-block" style="color: var(--primary);">Sign Up</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <div class="hero-section">
        <div class="hero-slider" id="heroSlider">
            <div class="hero-slide active" style="background-image: url('{{ asset('images/carousel_6.jpg') }}'); opacity: 1; transform: scale(1.02);"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/carousel_7.jpg') }}');"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/carousel_8.jpg') }}');"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/carousel_9.jpg') }}');"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/carousel_10.jpg') }}');"></div>
        </div>
        
        <div class="hero-content">
            <h1 style="font-size: clamp(3.2rem, 5vw, 5rem); font-weight: 800; margin-bottom: 25px; letter-spacing: -1.5px; line-height: 1.1; text-shadow: 0 4px 15px rgba(0,0,0,0.6);">Drive Your Dreams <br>With <span style="color: var(--accent);">AARACC</span> Services.</h1>
            <p style="font-size: 1.3rem; color: #f1f5f9; max-width: 680px; margin: 0 auto 40px; line-height: 1.7; text-shadow: 0 2px 5px rgba(0,0,0,0.6);">Unlock pure freedom and luxury. Whether you need an elite SUV for the weekend or a reliable sedan for the week, our world-class, fully insured fleet is ready to elevate your journey.</p>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="#fleet" class="btn btn-primary" style="padding: 16px 45px; font-size: 1.15rem; border-radius: 30px; letter-spacing: 0.5px;">Book Now!</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline" style="padding: 16px 45px; font-size: 1.15rem; border-radius: 30px; background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.3); backdrop-filter: blur(8px); letter-spacing: 0.5px;">Join Us</a>
                @endguest
            </div>
        </div>
    </div>

    <div id="features" class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary); margin-bottom: 15px;">Easy Booking Process</h3>
            <p style="color: #64748b; line-height: 1.7; font-size: 1.05rem;">Experience a seamless, hassle-free platform designed to secure your vehicle and get you quickly on the road.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 10v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V10M3 10h18M12 2v8M8 6h8"/></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary); margin-bottom: 15px;">Wide Range of Vehicles</h3>
            <p style="color: #64748b; line-height: 1.7; font-size: 1.05rem;">From compact daily drivers to luxury SUVs, choose the perfect ride tailored specifically for your journey.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary); margin-bottom: 15px;">Affordable Pricing</h3>
            <p style="color: #64748b; line-height: 1.7; font-size: 1.05rem;">Highly competitive rates and transparent contracts mean you receive premium vehicles without breaking your budget.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary); margin-bottom: 15px;">24/7 Support</h3>
            <p style="color: #64748b; line-height: 1.7; font-size: 1.05rem;">Our dedicated roadside and customer assistance team is available continuously to ensure you're never stranded.</p>
        </div>
    </div>

    <!-- About AARACC Section -->
    <div id="about" style="width: 100%; padding: 80px 4%; box-sizing: border-box; background: white; border-top: 1px solid #e2e8f0;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px;">
            <div style="text-align: center; margin-bottom: 10px;">
                <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary); letter-spacing: -0.5px; margin-bottom: 20px;">About AARACC</h2>
                <p style="color: #64748b; font-size: 1.15rem; max-width: 800px; margin: 0 auto; line-height: 1.8;">Established with a passion for mobility and excellence, AARACC has grown into a premier vehicle rental provider dedicated to delivering uncompromising quality and unmatched customer experiences.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                <!-- Our Mission -->
                <div style="background: var(--bg-color); padding: 40px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm);">
                    <div style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1); color: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 15px;">Our Mission</h3>
                    <p style="color: #64748b; font-size: 1.05rem; line-height: 1.7;">To empower your journeys by providing safe, reliable, and premium transportation solutions while setting the standard for exceptional service in the rental industry.</p>
                </div>

                <!-- Why Choose Us -->
                <div style="background: var(--bg-color); padding: 40px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm);">
                    <div style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1); color: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 15px;">Why Choose Us?</h3>
                    <p style="color: #64748b; font-size: 1.05rem; line-height: 1.7;">We prioritize your peace of mind with meticulously maintained fleets, absolutely transparent pricing, and a customer-first philosophy that turns first-time renters into lifelong clients.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="how-it-works" style="background: var(--bg-color); padding: 80px 4%; width: 100%; box-sizing: border-box; text-align: center; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
        <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 50px; letter-spacing: -0.5px;">How It Works</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto;">
            <!-- Step 1 -->
            <div>
                <div style="width: 80px; height: 80px; background: white; border: 4px solid var(--accent); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 20px; box-shadow: var(--shadow-sm);">1</div>
                <h4 style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Choose a car</h4>
                <p style="color: #64748b; margin-top: 10px; line-height: 1.6; font-size: 1.05rem;">Browse our premium fleet and select the perfect vehicle for your journey.</p>
            </div>
            <!-- Step 2 -->
            <div>
                <div style="width: 80px; height: 80px; background: white; border: 4px solid var(--accent); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 20px; box-shadow: var(--shadow-sm);">2</div>
                <h4 style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Select dates</h4>
                <p style="color: #64748b; margin-top: 10px; line-height: 1.6; font-size: 1.05rem;">Pick your desired pickup and return dates with our flexible scheduling.</p>
            </div>
            <!-- Step 3 -->
            <div>
                <div style="width: 80px; height: 80px; background: white; border: 4px solid var(--accent); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 20px; box-shadow: var(--shadow-sm);">3</div>
                <h4 style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Book & pay</h4>
                <p style="color: #64748b; margin-top: 10px; line-height: 1.6; font-size: 1.05rem;">Confirm your reservation through our secure and transparent payment system.</p>
            </div>
            <!-- Step 4 -->
            <div>
                <div style="width: 80px; height: 80px; background: white; border: 4px solid var(--accent); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 20px; box-shadow: var(--shadow-sm);">4</div>
                <h4 style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Pick up / delivery</h4>
                <p style="color: #64748b; margin-top: 10px; line-height: 1.6; font-size: 1.05rem;">Collect your freshly detailed car or have it delivered right to your door.</p>
            </div>
        </div>
    </div>

    <div id="fleet" class="fleet-container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 15px; letter-spacing: -0.5px;">Available Vehicles</h2>
            <p style="font-size: 1.15rem; color: #64748b; max-width: 700px; margin: 0 auto;">Find the perfect match for your requirements from our diverse selection of meticulously maintained vehicles.</p>
        </div>
        
        <div class="vehicle-layout">
            <!-- Sidebar for Filtering -->
            <aside class="filter-sidebar">
                <h3 style="margin-bottom: 25px; font-size: 1.3rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter Vehicles
                </h3>
                
                <form id="fleetFilterForm" action="{{ url('/') }}" method="GET" data-no-loader>
                    
                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Search Keyword</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Description..." class="filter-custom-input">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Vehicle Brand</label>
                        <select name="lib_brand_id" class="filter-custom-input">
                            <option value="">All Brands</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ request('lib_brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Price Range (₱)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="filter-custom-input">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="filter-custom-input">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Vehicle Type</label>
                        <select name="type" class="filter-custom-input">
                            <option value="">All Types</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" {{ request('type') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Transmission</label>
                        <select name="transmission" class="filter-custom-input">
                            <option value="">Any</option>
                            @foreach($transmissions as $tr)
                                <option value="{{ $tr->id }}" {{ request('transmission') == $tr->id ? 'selected' : '' }}>{{ $tr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Fuel Type</label>
                        <select name="fuel_type" class="filter-custom-input">
                            <option value="">Any</option>
                            @foreach($fuels as $f)
                                <option value="{{ $f->id }}" {{ request('fuel_type') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Minimum Seats</label>
                        <input type="number" name="seating_capacity" value="{{ request('seating_capacity') }}" placeholder="e.g. 4" class="filter-custom-input">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="filter-label">Availability</label>
                        <select name="availability_status" class="filter-custom-input">
                            <option value="">Any Status</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->id }}" {{ request('availability_status') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label class="filter-label">Sort By</label>
                        <select name="sort" class="filter-custom-input">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Listings</option>
                            <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 15px; flex-direction: column;">
                        <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1.05rem;">Apply Filters</button>
                        <a href="{{ url('/') }}#fleet" id="fleetClearFilters" class="btn btn-outline btn-block" style="padding: 14px; font-size: 1.05rem;" data-no-loader>Clear All</a>
                    </div>
                </form>
            </aside>

            <!-- Main Listing Area -->
            <div class="vehicle-main">
                
                <div id="fleetVehiclesContainer">
                    @include('partials.public_vehicle_grid', ['vehicles' => $vehicles])
                </div>
                
            </div>
        </div>
    </div>

    <div id="ownerProfileModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.85); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div id="ownerProfileBackdrop" style="position:absolute; inset:0;"></div>
        <div style="position: relative; z-index: 1; width: 100%; max-width: 1080px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.35); max-height: 85vh; display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900; letter-spacing: 0.2px;">Owner Profile</div>
                <button type="button" id="ownerProfileClose" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="padding: 16px; background: #f8fafc; overflow:auto;">
                <div style="display:grid; grid-template-columns: 1fr; gap: 14px;">
                    <div id="ownerProfileGrid" style="display:grid; grid-template-columns: 1fr; gap: 14px;">
                        <div id="ownerLeftCard" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;"></div>
                        <div id="ownerRightCard" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 980px) {
            #ownerProfileGrid { grid-template-columns: 1fr 1fr !important; }
        }
        .owner-profile-link--muted { color: #64748b; text-decoration: none; cursor: pointer; }
        .owner-profile-link--muted:hover { color: var(--accent); }
    </style>

    <div id="location" style="width: 100%; padding: 80px 4%; box-sizing: border-box; background: white;">
        <div class="location-reviews-grid" style="max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
            
            <!-- Column 1: Map & Location -->
            <div>
                <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-bottom: 30px; letter-spacing: -0.5px;">Our Location</h2>
                <div style="width: 100%; height: 400px; background: #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0;">
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=8.950657821650697,125.50821924972001&hl=en&z=15&amp;output=embed" style="border:0;"></iframe>
                </div>
                <div style="margin-top: 25px; display: flex; align-items: center; gap: 15px; padding: 20px; background: var(--bg-color); border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--primary); margin: 0 0 5px 0;">AARACC Headquarters</h4>
                        <p style="color: #64748b; margin: 0; line-height: 1.5; font-size: 0.95rem;">8th Street, Village 2, Libertad, Butuan City, Agusan Del Norte <br> Philippines 8600</p>
                    </div>
                </div>
            </div>

            <!-- Column 2: Testimonials -->
            <div style="display: flex; flex-direction: column;">
                <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-bottom: 30px; letter-spacing: -0.5px;">Testimonials & Reviews</h2>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Review 1 -->
                    <div style="background: var(--bg-color); padding: 30px; border-radius: 12px; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; position: relative;">
                        <!-- Quote Icon -->
                        <svg style="position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; color: #cbd5e1; opacity: 0.3;" viewBox="0 0 24 24" fill="currentColor"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        
                        <div style="display: flex; gap: 4px; color: var(--accent); margin-bottom: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <p style="color: #475569; font-style: italic; line-height: 1.6; margin-bottom: 20px; font-size: 1.05rem;">"Absolutely flawless experience. The SUV was in pristine condition, the pickup was incredibly smooth, and there were zero hidden fees. I highly recommend AARACC for anyone valuing premium service!"</p>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.3rem; background-color: var(--primary);">MC</div>
                            <div>
                                <h5 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--primary);">Michael Chen</h5>
                                <span style="font-size: 0.9rem; color: #64748b; display: flex; align-items: center; gap: 5px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Verified Customer
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Review 2 -->
                    <div style="background: var(--bg-color); padding: 30px; border-radius: 12px; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; position: relative;">
                        <!-- Quote Icon -->
                        <svg style="position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; color: #cbd5e1; opacity: 0.3;" viewBox="0 0 24 24" fill="currentColor"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>

                        <div style="display: flex; gap: 4px; color: var(--accent); margin-bottom: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <p style="color: #475569; font-style: italic; line-height: 1.6; margin-bottom: 20px; font-size: 1.05rem;">"The 24/7 customer support was an absolute lifesaver when my scheduled flight got severely delayed. They accommodated my booking with zero hassle."</p>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.3rem; background-color: var(--accent);">SR</div>
                            <div>
                                <h5 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--primary);">Sarah Rodriguez</h5>
                                <span style="font-size: 0.9rem; color: #64748b; display: flex; align-items: center; gap: 5px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Verified Customer
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($faqs) && $faqs->count() > 0)
        <div id="faqs" style="width: 100%; padding: 0 4% 70px; box-sizing: border-box; background: #ffffff;">
            <div style="max-width: 1200px; margin: 0 auto;">
                <div id="landingFaqPanel" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                        <div>
                            <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem;">Frequently Asked Questions</div>
                            <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.9rem;">Tap a question to expand.</div>
                        </div>
                    </div>

                    <div style="margin-top: 12px; display:flex; flex-direction: column; gap: 10px;">
                        @foreach($faqs as $faq)
                            <div class="aar-faq-item" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow:hidden;">
                                <button type="button" class="aar-faq-btn" aria-expanded="false" style="width:100%; text-align:left; background: #f8fafc; border:none; padding: 12px 14px; display:flex; justify-content:space-between; align-items:center; gap: 12px; cursor:pointer;">
                                    <span style="font-weight: 900; color:#0f172a;">{{ $faq->question }}</span>
                                    <span class="aar-faq-icon" style="width: 34px; height: 34px; border-radius: 999px; display:flex; align-items:center; justify-content:center; border: 1px solid rgba(245,158,11,0.28); background: rgba(245,158,11,0.14); color: var(--accent); font-weight: 900;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                                    </span>
                                </button>
                                <div class="aar-faq-panel" style="display:none; padding: 12px 14px; background: white; color:#0f172a; font-weight: 300; line-height: 1.55; white-space: pre-wrap;">{{ $faq->answer }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <footer style="width: 100%; box-sizing: border-box;">
        <div style="width: 100%; padding: 0 4%; box-sizing: border-box; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 50px; margin-bottom: 50px;">
            <div style="grid-column: span 2;">
                <a style="font-size: 2rem; font-weight: 800; color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; margin-bottom: 25px; cursor: default;">
                    <img src="{{ asset('images/logo.png') }}" alt="AARACC" style="height: 40px; filter: brightness(0) invert(1);" onerror="this.style.display='none'">
                    AARACC<span style="color: var(--accent);">.</span>
                </a>
                <p style="line-height: 1.7; font-size: 1.05rem; color: #94a3b8; max-width: 400px;">Providing premium car rental experiences with an extensive fleet of meticulously maintained vehicles for every occasion.</p>
            </div>
            <div>
                <h4 style="color: white; font-weight: 700; font-size: 1.25rem; margin-bottom: 25px;">Company</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; font-size: 1.05rem;">
                    <li><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">About Us</a></li>
                    <li><a href="#fleet" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Our Fleet</a></li>
                    <li><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Locations</a></li>
                    <li><a href="#faqs" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; font-weight: 700; font-size: 1.25rem; margin-bottom: 25px;">Members</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; font-size: 1.05rem;">
                    <li><a href="{{ route('login') }}" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Sign In</a></li>
                    <li><a href="{{ route('register') }}" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Create Account</a></li>
                    <li><a href="{{ url('/my-bookings') }}" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Dashboard</a></li>
                    <li><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Rewards Program</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; font-weight: 700; font-size: 1.25rem; margin-bottom: 25px;">Legal</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; font-size: 1.05rem;">
                    <li><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Terms of Service</a></li>
                    <li><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Privacy Policy</a></li>
                    <li><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;">Cancellation Policy</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; font-weight: 700; font-size: 1.25rem; margin-bottom: 25px;">Contact Support</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px; font-size: 1.05rem; color: #94a3b8;">
                    <li style="display: flex; align-items: center; gap: 12px;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> (555) 123-4567</li>
                    <li style="display: flex; align-items: center; gap: 12px;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> support@aaraccrentals.com</li>
                </ul>
            </div>
        </div>
        <div style="width: 100%; border-top: 1px solid #1e293b; padding: 30px 4% 0; text-align: center; font-size: 0.95rem; box-sizing: border-box;">
            &copy; {{ date('Y') }} AARACC Premium Rentals. All rights reserved.
        </div>
    </footer>

    <!-- View Images Modal (matching logic to avoid errors) -->
    <div id="viewImagesModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.95); z-index: 1000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(8px);">
        <div style="background: transparent; width: 100%; max-width: 1000px; border-radius: 12px; position: relative; display: flex; align-items: center; justify-content: center;">
            <button onclick="closeImagesModal()" style="position: absolute; top: -45px; right: 0; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); cursor: pointer; color: white; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            
            <button id="prevImageBtn" onclick="prevImage()" style="position: absolute; left: -60px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); cursor: pointer; color: white; border-radius: 50%; width: 50px; height: 50px; display: none; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </button>

            <button id="nextImageBtn" onclick="nextImage()" style="position: absolute; right: -60px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); cursor: pointer; color: white; border-radius: 50%; width: 50px; height: 50px; display: none; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            
            <div id="imagesContainer" style="display: flex; width: 100%; height: 80vh; align-items: center; justify-content: center; background: black; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); overflow: hidden;">
                <img id="modalMainImage" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none; transition: opacity 0.3s;">
            </div>
            
            <div id="imagesFallback" style="padding: 60px; text-align: center; color: white; display: none; width: 100%; height: 80vh; flex-direction: column; align-items: center; justify-content: center; background: #1e293b; border-radius: 12px;">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5" style="margin-bottom: 20px;"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/></svg>
                <p style="font-size: 1.2rem; color: #94a3b8;">No images available for this vehicle.</p>
            </div>
            
            <div id="imageCounter" style="position: absolute; bottom: 20px; background: rgba(15, 23, 42, 0.8); padding: 8px 16px; border-radius: 20px; color: white; border: 1px solid rgba(255,255,255,0.1); font-weight: 600; font-size: 0.95rem; display: none; backdrop-filter: blur(4px);"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
        }

        // Hero Carousel Slideshow Logic
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('.hero-slide');
            if (slides.length > 1) {
                let currentSlide = 0;
                setInterval(() => {
                    slides[currentSlide].classList.remove('active');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');
                }, 5000); // 5 sec per slide
            }
        });

        // Vehicle Image Modal Logic
        let modalImageSet = [];
        let modalImageIndex = 0;

        function viewImages(id, imagesArray) {
            const fallback = document.getElementById('imagesFallback');
            const mainImg = document.getElementById('modalMainImage');
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');
            const counter = document.getElementById('imageCounter');
            
            modalImageSet = imagesArray || [];
            modalImageIndex = 0;
            
            if (modalImageSet.length === 0) {
                mainImg.style.display = 'none';
                fallback.style.display = 'flex';
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                counter.style.display = 'none';
            } else {
                fallback.style.display = 'none';
                mainImg.style.display = 'block';
                updateModalImage();
                
                if (modalImageSet.length > 1) {
                    prevBtn.style.display = 'flex';
                    nextBtn.style.display = 'flex';
                    counter.style.display = 'block';
                } else {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    counter.style.display = 'none';
                }
            }
            
            document.getElementById('viewImagesModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; 
        }

        function updateModalImage() {
            const mainImg = document.getElementById('modalMainImage');
            const counter = document.getElementById('imageCounter');
            
            if (modalImageSet.length > 0) {
                const imgObj = modalImageSet[modalImageIndex];
                
                mainImg.style.opacity = '0.5';
                mainImg.src = imgObj.url;
                setTimeout(() => { mainImg.style.opacity = '1'; }, 50);
                
                counter.textContent = `${modalImageIndex + 1} / ${modalImageSet.length}`;
            }
        }

        function prevImage() {
            if (modalImageSet.length > 0) {
                modalImageIndex = (modalImageIndex - 1 + modalImageSet.length) % modalImageSet.length;
                updateModalImage();
            }
        }

        function nextImage() {
            if (modalImageSet.length > 0) {
                modalImageIndex = (modalImageIndex + 1) % modalImageSet.length;
                updateModalImage();
            }
        }

        function closeImagesModal() {
            document.getElementById('viewImagesModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('viewImagesModal').style.display === 'flex') {
                if (e.key === 'Escape') closeImagesModal();
                if (e.key === 'ArrowLeft') prevImage();
                if (e.key === 'ArrowRight') nextImage();
            }
        });
    </script>
    <script>
        (function () {
            if (!window.jQuery) return;
            const $container = $('#fleetVehiclesContainer');
            const $form = $('#fleetFilterForm');
            const $clear = $('#fleetClearFilters');
            let pendingRequest = null;
            let activeRequestId = 0;

            function setLoading(isLoading) {
                if (isLoading) {
                    $container.css('opacity', '0.55');
                    if ($('#fleetLoading').length === 0) {
                        $container.prepend('<div id="fleetLoading" style="padding: 14px 16px; margin-bottom: 14px; border-radius: 12px; background: rgba(15,23,42,0.04); border: 1px solid #e2e8f0; font-weight: 800; color: #0f172a;">Loading vehicles...</div>');
                    }
                } else {
                    $container.css('opacity', '1');
                    $('#fleetLoading').remove();
                }
            }

            function normalizeUrl(url) {
                return (url || '').split('#')[0];
            }

            function resetGlobalLoader() {
                if (window.AARLoading && typeof window.AARLoading.reset === 'function') {
                    window.AARLoading.reset();
                }
            }

            function prepareFleetLinks() {
                $container.find('.fleet-pagination a').attr('data-no-loader', 'true');
            }

            function stopPendingRequest() {
                if (pendingRequest && typeof pendingRequest.abort === 'function') {
                    pendingRequest.abort();
                }
                pendingRequest = null;
            }

            function loadVehicles(url, pushUrl) {
                const cleanUrl = normalizeUrl(url);
                const ajaxUrl = cleanUrl + (cleanUrl.includes('?') ? '&' : '?') + 'ajax=1';
                const requestId = ++activeRequestId;
                stopPendingRequest();
                resetGlobalLoader();
                setLoading(true);
                pendingRequest = $.get(ajaxUrl)
                    .done(function (html) {
                        if (requestId !== activeRequestId) return;
                        $container.html(html);
                        prepareFleetLinks();
                        if (pushUrl) {
                            window.history.replaceState({}, '', normalizeUrl(pushUrl) + '#fleet');
                        }
                        const fleetEl = document.getElementById('fleet');
                        if (fleetEl) fleetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    })
                    .fail(function (_xhr, status) {
                        if (status === 'abort') return;
                        $container.html('<div style="padding: 18px; border-radius: 12px; background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; font-weight: 800;">We could not load the available vehicles right now. Please try again.</div>');
                    })
                    .always(function () {
                        if (requestId === activeRequestId) {
                            setLoading(false);
                            resetGlobalLoader();
                            pendingRequest = null;
                        }
                    });
            }

            $form.on('submit', function (e) {
                e.preventDefault();
                const baseUrl = normalizeUrl($form.attr('action'));
                const qs = $form.serializeArray()
                    .filter(function (item) { return item.name !== 'page' && String(item.value || '').trim() !== ''; })
                    .map(function (item) { return encodeURIComponent(item.name) + '=' + encodeURIComponent(item.value); })
                    .join('&');
                const targetUrl = qs ? (baseUrl + '?' + qs) : baseUrl;
                loadVehicles(targetUrl, targetUrl);
            });

            $container.on('click', '.fleet-pagination a', function (e) {
                const href = $(this).attr('href');
                if (!href) return;
                e.preventDefault();
                loadVehicles(href, href);
            });

            $clear.on('click', function (e) {
                e.preventDefault();
                $form[0].reset();
                const baseUrl = normalizeUrl($form.attr('action'));
                loadVehicles(baseUrl, baseUrl);
            });

            prepareFleetLinks();

            function esc(s) {
                return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }
            function stars(r) {
                const v = Number(r || 0);
                let out = '';
                for (let i = 1; i <= 5; i++) {
                    out += '<span style="font-weight:900; color:' + (i <= v ? 'var(--accent)' : '#cbd5e1') + ';">★</span>';
                }
                return out;
            }

            const $modal = $('#ownerProfileModal');
            const $left = $('#ownerLeftCard');
            const $right = $('#ownerRightCard');
            let loading = false;

            function openModal() { $modal.css('display', 'flex'); $('body').css('overflow', 'hidden'); }
            function closeModal() { $modal.hide(); $('body').css('overflow', 'auto'); }

            $('#ownerProfileBackdrop, #ownerProfileClose').on('click', closeModal);
            $(document).on('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

            $container.on('click', '.owner-profile-link', function (e) {
                e.preventDefault();
                const ownerId = $(this).data('ownerId');
                const vehicleId = $(this).data('vehicleId');
                if (!ownerId || loading) return;
                loading = true;
                openModal();
                $left.html('<div style="color:#64748b; font-weight: 900;">Loading profile…</div>');
                $right.html('<div style="color:#64748b; font-weight: 900;">Loading reviews…</div>');

                let perPage = 10;
                let showSelectedOnly = false;
                let nextUrl = null;
                let prevUrl = null;

                function selectedVehicleTitle(v) {
                    if (!v) return 'Selected Vehicle';
                    const plate = v.license_plate ? (' • ' + v.license_plate) : '';
                    return (v.name || 'Vehicle') + plate;
                }

                function renderRightControls(p) {
                    const total = Number(p?.total || 0);
                    const cur = Number(p?.current_page || 1);
                    const last = Number(p?.last_page || 1);
                    const pp = p?.per_page === 'all' ? 'all' : Number(p?.per_page || perPage);
                    const pageText = pp === 'all' ? (total + ' total') : ('Page ' + cur + ' of ' + last + ' • ' + total + ' total');
                    const opt = (v, label) => '<option value="' + v + '"' + (String(pp) === String(v) ? ' selected' : '') + '>' + label + '</option>';
                    const prevDisabled = p?.prev_page_url ? '' : 'disabled';
                    const nextDisabled = p?.next_page_url ? '' : 'disabled';
                    const hideBtns = pp === 'all' ? 'display:none;' : '';
                    return (
                        '<div style="margin-top: 12px; display:flex; justify-content:space-between; gap: 10px; flex-wrap: wrap; align-items:center;">' +
                            '<div style="color:#64748b; font-weight: 900; font-size: 0.9rem;">' + esc(pageText) + '</div>' +
                            '<div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">' +
                                '<span style="color:#64748b; font-weight: 900; font-size: 0.85rem;">Page size</span>' +
                                '<select id="ownerModalPageSize" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-weight: 800;">' +
                                    opt(10, '10') + opt(20, '20') + opt(50, '50') + opt('all', 'ALL') +
                                '</select>' +
                                '<button type="button" id="ownerModalPrev" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; ' + hideBtns + '" ' + prevDisabled + '>Prev</button>' +
                                '<button type="button" id="ownerModalNext" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; ' + hideBtns + '" ' + nextDisabled + '>Next</button>' +
                            '</div>' +
                        '</div>'
                    );
                }

                function renderOwnerReviewsRight(reviews, pagination, selectedVehicle) {
                    const items = Array.isArray(reviews) ? reviews : [];
                    const title = showSelectedOnly ? 'Reviews of this vehicle' : 'All Reviews';
                    const toggleLabel = showSelectedOnly ? 'Show all reviews' : 'Show Reviews of this vehicle';
                    const toggleDisabled = vehicleId ? '' : 'disabled';
                    const toggleStyle = vehicleId
                        ? (!showSelectedOnly ? 'background: rgba(16, 185, 129, 0.14); border: 1px solid rgba(16, 185, 129, 0.28); color: #10b981;' : 'background: rgba(245, 158, 11, 0.14); border: 1px solid rgba(245, 158, 11, 0.28); color: var(--accent);')
                        : 'background: rgba(15, 23, 42, 0.04); border: 1px solid rgba(15, 23, 42, 0.08); color:#94a3b8; cursor: not-allowed; opacity: 0.8;';
                    const showVehicleMeta = !showSelectedOnly && selectedVehicle && vehicleId;
                    const metaColor = selectedVehicle?.color ? (' • ' + selectedVehicle.color) : '';
                    const metaYear = selectedVehicle?.year_model ? (' • ' + selectedVehicle.year_model) : '';
                    let html = '<div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap: wrap;">' +
                        '<div style="font-weight: 800; color: var(--primary); font-size: 1.2rem;">' + esc(title) + '</div>' +
                        '<button type="button" id="ownerModalFilterToggle" ' + toggleDisabled + ' aria-pressed="' + (showSelectedOnly ? 'true' : 'false') + '" style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; font-weight: 900; cursor: pointer; ' + toggleStyle + '">' +
                            '<span style="display:flex; flex-direction:column; align-items:flex-start; gap: 2px;">' +
                                '<span>' + esc(toggleLabel) + '</span>' +
                                (showVehicleMeta ? ('<span style="font-weight: 800; font-size: 0.85rem; color: #64748b;">' + esc((selectedVehicle?.name || 'Vehicle') + metaColor + metaYear) + '</span>') : '') +
                            '</span>' +
                        '</button>' +
                    '</div>';
                    if (items.length === 0) {
                        html += '<div style="margin-top: 10px; color:#94a3b8; font-weight: 800;">No reviews yet.</div>';
                        html += renderRightControls(pagination);
                        return html;
                    }
                    const grouped = {};
                    items.forEach(rv => {
                        const vid = rv.vehicle?.id || '0';
                        if (!grouped[vid]) grouped[vid] = { vehicle: rv.vehicle, items: [] };
                        grouped[vid].items.push(rv);
                    });
                    Object.keys(grouped).forEach(k => {
                        const g = grouped[k];
                        const vname = g.vehicle?.name || 'Vehicle';
                        const color = g.vehicle?.color ? (' • ' + g.vehicle.color) : '';
                        const year = g.vehicle?.year_model ? (' • ' + g.vehicle.year_model) : '';
                        html += '<div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">' +
                            '<div style="font-weight: 600; color:#0f172a;">' + esc(vname + color + year) + '</div>' +
                            '<div style="margin-top: 10px; display:flex; flex-direction:column; gap: 10px;">';
                        g.items.forEach(rv => {
                            const who = rv.reviewer?.name || 'User';
                            const when = rv.created_at ? new Date(rv.created_at).toLocaleDateString() : '';
                            html += '<div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #f8fafc;">' +
                                '<div style="display:flex; justify-content:space-between; gap:10px; flex-wrap: wrap;">' +
                                    '<div style="font-weight: 400; color:#0f172a; display:flex; align-items:center; gap:8px; flex-wrap: wrap;"><b>' + esc(who) + '</b><span style="display:inline-flex; gap:4px; align-items:center;">' + stars(rv.rating) + '</span></div>' +
                                    '<div style="font-weight: 400; color:#94a3b8; font-size: 0.85rem;">' + esc(when) + '</div>' +
                                '</div>' +
                                '<div style="margin-top: 10px; color:#64748b; font-weight: 500; font-size: 0.9rem; white-space: pre-wrap;">' + esc(rv.comment) + '</div>' +
                            '</div>';
                        });
                        html += '</div></div>';
                    });
                    html += renderRightControls(pagination);
                    return html;
                }

                function buildBaseUrl() {
                    const base = '{{ route('owners.profile', ['user' => '__ID__']) }}'.replace('__ID__', ownerId);
                    const u = new URL(base, window.location.origin);
                    if (vehicleId) u.searchParams.set('vehicle_id', String(vehicleId));
                    u.searchParams.set('per_page', String(perPage));
                    if (showSelectedOnly && vehicleId) u.searchParams.set('only_vehicle', '1');
                    return u.toString();
                }

                function fetchOwner(url) {
                    const finalUrl = url || buildBaseUrl();
                    return fetch(finalUrl, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.ok ? r.json() : Promise.reject(new Error('Unable to load owner profile.')))
                        .then(data => {
                            const o = data.owner || {};
                            const photo = o.profile_photo_url ? ('<img src="' + esc(o.profile_photo_url) + '" alt="" style="width:72px; height:72px; border-radius: 16px; object-fit: cover; border: 1px solid #e2e8f0;">') : ('<div style="width:72px; height:72px; border-radius: 16px; background:#f1f5f9; border: 1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; font-weight:900; color:#94a3b8;">N/A</div>');
                            const avg = Number(o.avg_rating || 0).toFixed(1);
                            const cnt = Number(o.total_reviews || 0);
                            $left.html(
                                '<div style="display:flex; gap: 12px; align-items:center;">' +
                                    photo +
                                    '<div style="min-width:0;">' +
                                        '<div style="font-weight: 900; color:#0f172a; font-size: 1.1rem;">' + esc(o.name) + '</div>' +
                                        '<div style="margin-top: 4px; color:#64748b; font-weight: 700;">' + esc(o.email) + '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div style="margin-top: 12px; display:flex; flex-wrap:wrap; gap: 10px;">' +
                                    '<span style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); color: #b45309; font-weight: 900;">Owner Average Rating ★' + avg + ' (' + cnt + ')</span>' +
                                    '<span style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; background: rgba(15, 23, 42, 0.06); border: 1px solid rgba(15, 23, 42, 0.12); color: #0f172a; font-weight: 900;">Vehicles: ' + (o.vehicles_count || 0) + '</span>' +
                                '</div>' +
                                '<div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">' +
                                    '<div style="color:#64748b; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.45px;">Address</div>' +
                                    '<div style="margin-top: 6px; font-weight: 400; color:#0f172a; white-space: pre-wrap;">' + esc(o.address || '—') + '</div>' +
                                '</div>' +
                                '<div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">' +
                                    '<div style="color:#64748b; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.45px;">Proof of Legitimacy</div>' +
                                    ((Array.isArray(data.legitimacy_proofs) && data.legitimacy_proofs.length > 0)
                                        ? ('<div style="margin-top: 8px; display:grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">' +
                                            data.legitimacy_proofs.map(p => '<a href=\"' + esc(p.url) + '\" target=\"_blank\" style=\"display:block; border-radius:10px; overflow:hidden; border:1px solid #e2e8f0; background:white;\"><img src=\"' + esc(p.url) + '\" alt=\"Proof\" style=\"width:100%; height:90px; object-fit:cover;\"></a>').join('') +
                                          '</div>')
                                        : '<div style=\"margin-top: 8px; color:#94a3b8; font-weight:800;\">No proof images.</div>') +
                                '</div>' +
                                '<div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">' +
                                    '<div style="color:#64748b; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.45px;">About the owner</div>' +
                                    '<div style="margin-top: 8px; font-weight: 700; color:#0f172a;">' + String(o.about_owner || '').replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '') + '</div>' +
                                '</div>'
                            );

                            const pag = data.pagination || {};
                            nextUrl = pag.next_page_url || null;
                            prevUrl = pag.prev_page_url || null;
                            $right.html(renderOwnerReviewsRight(data.reviews, pag, data.selected_vehicle));

                            $('#ownerModalPrev').off('click').on('click', function () { if (prevUrl) fetchOwner(prevUrl); });
                            $('#ownerModalNext').off('click').on('click', function () { if (nextUrl) fetchOwner(nextUrl); });
                            $('#ownerModalPageSize').off('change').on('change', function () {
                                const v = String($(this).val() || '10');
                                perPage = v === 'all' ? 'all' : parseInt(v, 10);
                                nextUrl = null;
                                prevUrl = null;
                                fetchOwner(null);
                            });
                            $('#ownerModalFilterToggle').off('click').on('click', function () {
                                if (!vehicleId) return;
                                showSelectedOnly = !showSelectedOnly;
                                nextUrl = null;
                                prevUrl = null;
                                fetchOwner(null);
                            });
                        });
                }

                fetchOwner(null)
                    .catch(err => {
                        $left.html('<div style="color:#b91c1c; font-weight: 900;">' + esc(err?.message || 'Unable to load.') + '</div>');
                        $right.html('');
                    })
                    .finally(() => { loading = false; });
            });
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('#landingFaqPanel .aar-faq-item');
            if (!items || items.length === 0) return;
            items.forEach((item) => {
                const btn = item.querySelector('.aar-faq-btn');
                const panel = item.querySelector('.aar-faq-panel');
                const icon = item.querySelector('.aar-faq-icon');
                if (!btn || !panel) return;
                btn.addEventListener('click', () => {
                    const isOpen = btn.getAttribute('aria-expanded') === 'true';
                    items.forEach((other) => {
                        const ob = other.querySelector('.aar-faq-btn');
                        const op = other.querySelector('.aar-faq-panel');
                        const oi = other.querySelector('.aar-faq-icon');
                        if (!ob || !op) return;
                        ob.setAttribute('aria-expanded', 'false');
                        op.style.display = 'none';
                        if (oi) oi.style.transform = 'rotate(0deg)';
                    });
                    if (!isOpen) {
                        btn.setAttribute('aria-expanded', 'true');
                        panel.style.display = 'block';
                        if (icon) icon.style.transform = 'rotate(180deg)';
                    }
                });
            });
        });
    </script>
    @include('partials.global-loader')
</body>
</html>
