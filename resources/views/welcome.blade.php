<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-[#FAF7F8] text-[#4A2C35]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - {{ __('welcome.page_title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans antialiased bg-[#FAF7F8] text-[#4A2C35] selection:bg-[#BE2B5B] selection:text-white">
    @php
        $isId = app()->getLocale() === 'id';
    @endphp

    <!-- 1. Clean Sticky Navbar (SwiftDrop Layout + Berry Pink Palette) -->
    <header class="w-full bg-[#FAF7F8]/95 backdrop-blur-md sticky top-0 z-50 border-b border-[#EFE6EA]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo & Tagline -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                <div class="h-10 w-10 rounded-xl bg-[#BE2B5B] group-hover:bg-[#A0224B] flex items-center justify-center text-white shadow-xs transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-[#4A2C35] block leading-none">CargoTrace</span>
                    <span class="text-[10px] font-medium text-[#826871] tracking-tight block mt-1">{{ __('welcome.tagline') }}</span>
                </div>
            </a>

            <!-- Center Section Anchor Navigation -->
            <nav class="hidden lg:flex items-center gap-7 text-xs font-semibold text-[#826871]" aria-label="Landing Page Sections">
                <a href="#hero" class="text-[#4A2C35] relative py-1 after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-5 after:h-0.5 after:rounded-full after:bg-[#BE2B5B]">
                    {{ __('common.navigation.home') }}
                </a>
                <a href="#services" class="hover:text-[#BE2B5B] transition">
                    {{ $isId ? 'Layanan' : 'Services' }}
                </a>
                <a href="#telemetry" class="hover:text-[#BE2B5B] transition">
                    {{ $isId ? 'Telemetri' : 'Tracking' }}
                </a>
                <a href="#why-cargotrace" class="hover:text-[#BE2B5B] transition">
                    {{ $isId ? 'Keunggulan' : 'About' }}
                </a>
            </nav>

            <!-- Right Controls: Language Switcher (ID | EN) + Auth Buttons -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <!-- Language Switcher (ID | EN) -->
                <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-full bg-white border border-[#EFE6EA] p-1 shadow-2xs text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                    <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-[#BE2B5B] text-white font-bold' : 'text-[#826871] hover:text-[#4A2C35]' }}" aria-label="Bahasa Indonesia">
                        ID
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-[#BE2B5B] text-white font-bold' : 'text-[#826871] hover:text-[#4A2C35]' }}" aria-label="English">
                        EN
                    </a>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.dashboard') }} &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 sm:px-5 py-2.5 rounded-full bg-white border border-[#EFE6EA] text-xs font-semibold text-[#4A2C35] hover:border-[#BE2B5B]/40 hover:bg-[#FCECF1]/50 transition">
                        {{ __('common.navigation.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <!-- 2. Hero Section + 4-Item Feature Strip (Clean & Calm Composition) -->
        <section id="hero" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 pb-12 space-y-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Left Editorial Column -->
                <div class="lg:col-span-6 space-y-6">
                    <!-- Hero Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-[50px] font-extrabold tracking-tight text-[#4A2C35] leading-[1.14]">
                        {{ __('welcome.hero_title_prefix') }}
                        <span class="text-[#BE2B5B]">{{ __('welcome.hero_title_highlight') }}</span>
                        {{ __('welcome.hero_title_suffix') }}
                    </h1>

                    <!-- Hero Subtitle -->
                    <p class="text-sm sm:text-base text-[#826871] max-w-xl leading-relaxed">
                        {{ __('welcome.hero_subtitle') }}
                    </p>

                    <!-- Hero Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-sm font-semibold shadow-sm transition">
                            {{ __('welcome.access_platform') }} &rarr;
                        </a>
                        <a href="#services" class="px-6 py-3 rounded-full bg-white text-[#4A2C35] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA] text-sm font-semibold shadow-2xs transition">
                            {{ __('welcome.explore_services') }}
                        </a>
                    </div>
                </div>

                <!-- Right Hero Visual Stage: Clean Soft Blush Stage + Studio Truck + Telemetry Card -->
                <div class="lg:col-span-6 relative">
                    <div class="relative rounded-[28px] bg-gradient-to-b from-[#FCECF1]/75 via-[#FCECF1]/35 to-white border border-[#EFE6EA] p-6 sm:p-7 shadow-[0_12px_30px_-12px_rgba(26,23,24,0.05)] overflow-hidden">
                        <!-- Top Header Bar Inside Stage -->
                        <div class="relative z-10 flex flex-wrap items-center justify-between gap-2 mb-5">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-white text-[#BE2B5B] border border-[#EFE6EA] flex items-center justify-center shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </span>
                                <span class="text-sm font-bold text-[#4A2C35]">{{ __('common.navigation.shipments') }}</span>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-white text-[#BE2B5B] border border-[#EFE6EA] text-[11px] font-bold shadow-2xs">
                                OpenStreetMap + Live Telemetry
                            </span>
                        </div>

                        <!-- Truck + Cargo Boxes Illustration Stage -->
                        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
                            <div class="relative flex items-end justify-center">
                                <img
                                    src="{{ asset('images/scania-cab.png') }}"
                                    alt="CargoTrace freight tractor"
                                    class="w-56 sm:w-64 h-auto object-contain select-none drop-shadow-[0_14px_20px_rgba(26,23,24,0.14)]"
                                />
                                <img
                                    src="{{ asset('images/cargo-boxes.png') }}"
                                    alt="Cargo packages"
                                    class="w-24 sm:w-28 h-auto object-contain -ml-10 -mb-1 select-none drop-shadow-[0_10px_14px_rgba(120,75,35,0.18)]"
                                />
                            </div>

                            <!-- Mandatory A1 & A9 Telemetry Bay Cards -->
                            <div class="grid grid-cols-2 sm:grid-cols-1 gap-2.5 w-full sm:w-48 shrink-0">
                                <div class="bg-white rounded-2xl p-3.5 border border-[#EFE6EA] shadow-2xs space-y-1.5">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-[#4A2C35]">A1</span>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Ready
                                        </span>
                                    </div>
                                    <div class="text-xs font-bold text-[#4A2C35] font-mono">CT-ROT-SIN</div>
                                </div>

                                <div class="bg-white rounded-2xl p-3.5 border-2 border-[#BE2B5B] shadow-2xs relative overflow-hidden space-y-1.5">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-[#BE2B5B]">A9</span>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-[#BE2B5B]">
                                            <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                                            Live
                                        </span>
                                    </div>
                                    <div class="text-xs font-bold text-[#4A2C35] font-mono relative z-10">IN_TRANSIT</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mandatory Jakarta -> Rotterdam Route Telemetry Bar -->
                        <div class="relative z-10 mt-4 bg-white rounded-2xl p-3.5 border border-[#EFE6EA] flex items-center justify-between gap-3 shadow-2xs">
                            <div class="text-left">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#826871] block">{{ $isId ? 'Asal' : 'Origin' }}</span>
                                <span class="text-xs font-bold text-[#4A2C35]">Jakarta</span>
                            </div>
                            <div class="flex-1 h-2.5 rounded-full route-track-hatch relative flex items-center justify-center">
                                <span class="w-5 h-5 rounded-full bg-[#BE2B5B] text-white flex items-center justify-center shadow-xs">
                                    <svg class="w-2.5 h-2.5 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#826871] block">{{ $isId ? 'Tujuan' : 'Destination' }}</span>
                                <span class="text-xs font-bold text-[#4A2C35]">Rotterdam</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4-Item Value Proposition Strip (SwiftDrop Sub-Hero Bar) -->
            <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-5 sm:p-6 shadow-[0_6px_24px_-8px_rgba(26,23,24,0.05)] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Item 1 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-full bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Evaluasi ETA Dinamis' : 'Dynamic ETA Evaluation' }}</h2>
                        <p class="text-xs text-[#826871] mt-0.5 leading-relaxed">{{ $isId ? 'Deteksi keterlambatan otomatis terhadap ambang batas SLA.' : 'Automated delay detection against configurable SLA thresholds.' }}</p>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-full bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Keamanan RBAC & Audit' : 'Safe & Audited RBAC' }}</h2>
                        <p class="text-xs text-[#826871] mt-0.5 leading-relaxed">{{ $isId ? 'Isolasi data ketat dengan jejak audit JSON sebelum/sesudah.' : 'Strict role isolation with immutable before/after JSON audit trails.' }}</p>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-full bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Pelacakan Peta Waktu-Nyata' : 'Real-Time Corridor Map' }}</h2>
                        <p class="text-xs text-[#826871] mt-0.5 leading-relaxed">{{ $isId ? 'Peta interaktif OpenStreetMap & telemetri cuaca pelabuhan.' : 'Interactive OpenStreetMap waypoints & hub weather telemetry.' }}</p>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-full bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Peringatan Insiden 24/7' : '24/7 Exception Alerts' }}</h2>
                        <p class="text-xs text-[#826871] mt-0.5 leading-relaxed">{{ $isId ? 'Notifikasi instan untuk penahanan bea cukai & gangguan rute.' : 'Instant in-app notifications for customs holds & route disruptions.' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. "Our Services" 4-Card Section (SwiftDrop Service Cards Layout) -->
        <section id="services" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <div class="w-8 h-1 rounded-full bg-[#BE2B5B] mb-3"></div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#4A2C35]">
                        {{ $isId ? 'Layanan & Modul Utama' : 'Our Core Services' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[#826871] mt-1.5 max-w-md">
                        {{ $isId ? 'Solusi visibilitas pengiriman kargo terintegrasi yang dirancang untuk keandalan operasional.' : 'Integrated freight visibility solutions tailored for end-to-end operational reliability.' }}
                    </p>
                </div>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#BE2B5B] hover:text-[#A0224B] transition">
                    <span>{{ __('welcome.access_platform') }}</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Service Card 1 -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] hover:border-[#BE2B5B]/40 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#4A2C35]">{{ $isId ? 'Manajemen Manifes Kargo' : 'Container Freight Booking' }}</h3>
                            <p class="text-xs text-[#826871] mt-1.5 leading-relaxed">
                                {{ $isId ? 'Pencatatan pengiriman multi-item dengan kalkulasi berat & volume CBM tervalidasi.' : 'Multi-item cargo manifests with validated net weight and CBM volume tracking.' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#BE2B5B] hover:text-[#A0224B]">
                        <span>{{ $isId ? 'Pelajari' : 'Learn More' }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Service Card 2 -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] hover:border-[#BE2B5B]/40 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#4A2C35]">{{ $isId ? 'Mesin SLA & Keterlambatan' : 'Express SLA Monitoring' }}</h3>
                            <p class="text-xs text-[#826871] mt-1.5 leading-relaxed">
                                {{ $isId ? 'Klasifikasi keterlambatan otomatis (Low, Medium, High) dengan evaluasi ulang instan.' : 'Automated delay severity classification (Low, Medium, High) with instant re-evaluation.' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#BE2B5B] hover:text-[#A0224B]">
                        <span>{{ $isId ? 'Pelajari' : 'Learn More' }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Service Card 3 -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] hover:border-[#BE2B5B]/40 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#4A2C35]">{{ $isId ? 'Milestone & Insiden Rute' : 'Waypoint & Incident Ops' }}</h3>
                            <p class="text-xs text-[#826871] mt-1.5 leading-relaxed">
                                {{ $isId ? 'Transisi siklus hidup pengiriman ketat dengan alur investigasi & resolusi insiden.' : 'Strict state-machine lifecycle transitions with incident investigation and resolution.' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#BE2B5B] hover:text-[#A0224B]">
                        <span>{{ $isId ? 'Pelajari' : 'Learn More' }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Service Card 4 -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] hover:border-[#BE2B5B]/40 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#4A2C35]">{{ $isId ? 'Koridor Pelabuhan Global' : 'Global Hub Corridors' }}</h3>
                            <p class="text-xs text-[#826871] mt-1.5 leading-relaxed">
                                {{ $isId ? 'Direktori pelabuhan, bandara, dan gudang transit dengan geocoding koordinat WGS84.' : 'International ports, airports, and transit hubs with automated WGS84 coordinate geocoding.' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#BE2B5B] hover:text-[#A0224B]">
                        <span>{{ $isId ? 'Pelajari' : 'Learn More' }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 4. SwiftDrop Feature Highlight Banner + Live Package Tracking Card -->
        <section id="telemetry" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="rounded-[28px] bg-[#FCECF1] border border-[#EFE6EA] p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative overflow-hidden">
                <!-- Left Promo / Highlight Copy -->
                <div class="lg:col-span-6 space-y-4 relative z-10">
                    <span class="inline-block px-3 py-1 rounded-full bg-white text-[#BE2B5B] border border-[#EFE6EA] text-[11px] font-bold uppercase tracking-wider">
                        {{ $isId ? 'VISIBILITAS KORIDOR LANGSUNG' : 'REAL-TIME CORRIDOR VISIBILITY' }}
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#4A2C35] leading-tight">
                        {{ $isId ? 'Pantau Pergerakan Kargo & Milestone di Setiap Titik Transit' : 'Track Every Shipment Milestone from Departure to Delivery' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[#826871] max-w-lg leading-relaxed">
                        {{ $isId ? 'Integrasi peta koridor OpenStreetMap, pembaruan cuaca pelabuhan non-kausal, dan timeline peristiwa kronologis dalam satu tampilan terpadu.' : 'Seamless OpenStreetMap corridor polylines, non-causal port weather context, and chronological milestone telemetry in one unified workspace.' }}
                    </p>
                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-bold shadow-sm transition">
                            <span>{{ __('common.navigation.register') }}</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white hover:bg-[#FAF7F8] text-[#4A2C35] border border-[#EFE6EA] text-xs font-bold transition">
                            <span>{{ __('common.navigation.login') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Right SwiftDrop "Track Your Package" Preview Card -->
                <div class="lg:col-span-6 relative z-10">
                    <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-5 shadow-[0_14px_32px_-12px_rgba(26,23,24,0.08)] space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Lacak Pengiriman Anda' : 'Track Your Shipment' }}</div>
                                <div class="text-[11px] text-[#826871]">CT-ROT-SIN &bull; Port of Tanjung Priok &rarr; Port of Rotterdam</div>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-[#FCECF1] text-[#BE2B5B] text-[10px] font-bold uppercase">
                                IN_TRANSIT
                            </span>
                        </div>

                        <!-- Simulated Corridor Map Canvas with Curved Route Line -->
                        <div class="relative h-32 rounded-2xl bg-[#FAF7F8] border border-[#EFE6EA] overflow-hidden p-3 flex flex-col justify-between">
                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 128" fill="none" preserveAspectRatio="none" aria-hidden="true">
                                <path d="M 40 96 Q 160 30, 250 64 T 360 34" stroke="#BE2B5B" stroke-width="3" stroke-linecap="round" fill="none" />
                                <circle cx="40" cy="96" r="5" fill="#4A2C35" />
                                <circle cx="250" cy="64" r="6" fill="#BE2B5B" stroke="#FFFFFF" stroke-width="2" />
                                <circle cx="360" cy="34" r="5" fill="#4A2C35" />
                            </svg>
                            <div class="relative z-10 inline-flex items-center gap-2 bg-white/95 border border-[#EFE6EA] rounded-xl px-3 py-1.5 shadow-2xs self-start">
                                <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                                <span class="text-[11px] font-bold text-[#4A2C35]">PSA Singapore Terminal</span>
                                <span class="text-[10px] text-[#826871] font-mono">28°C</span>
                            </div>
                            <div class="relative z-10 flex items-center justify-between text-[10px] font-mono text-[#826871]">
                                <span>IDJKT (Jakarta)</span>
                                <span>NLRTM (Rotterdam)</span>
                            </div>
                        </div>

                        <!-- 4-Step Horizontal Milestone Bar (Mirrors SwiftDrop Track Card) -->
                        <div class="grid grid-cols-4 gap-2 pt-1 text-center">
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center text-xs font-bold">
                                    ✓
                                </div>
                                <div class="text-[11px] font-bold text-[#4A2C35]">Booked</div>
                                <div class="text-[9.5px] text-[#826871]">Jakarta</div>
                            </div>
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center text-xs font-bold">
                                    ✓
                                </div>
                                <div class="text-[11px] font-bold text-[#4A2C35]">Picked Up</div>
                                <div class="text-[9.5px] text-[#826871]">Tanjung Priok</div>
                            </div>
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full bg-[#BE2B5B] text-white flex items-center justify-center text-xs font-bold shadow-xs">
                                    &bull;
                                </div>
                                <div class="text-[11px] font-bold text-[#BE2B5B]">In Transit</div>
                                <div class="text-[9.5px] text-[#826871]">Singapore Hub</div>
                            </div>
                            <div class="space-y-1">
                                <div class="w-7 h-7 mx-auto rounded-full bg-[#FAF7F8] text-[#826871] border border-[#EFE6EA] flex items-center justify-center text-xs font-bold">
                                    &rarr;
                                </div>
                                <div class="text-[11px] font-semibold text-[#826871]">Delivered</div>
                                <div class="text-[9.5px] text-[#826871]">Rotterdam</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Role-Based Operational Governance Bento Section -->
        <section id="role-governance" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <div class="w-8 h-1 rounded-full bg-[#BE2B5B] mb-3"></div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#4A2C35]">
                        {{ __('welcome.roles.heading') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[#826871] mt-1">
                        {{ __('welcome.roles.subheading') }}
                    </p>
                </div>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#BE2B5B] hover:text-[#A0224B] transition">
                    <span>{{ __('common.navigation.login') }}</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Role: Admin -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#BE2B5B] text-white">
                                {{ __('common.roles.admin') }}
                            </span>
                            <span class="text-xs text-[#826871] font-medium">{{ __('welcome.roles.admin_scope') }}</span>
                        </div>
                        <h3 class="text-base font-bold text-[#4A2C35]">{{ __('welcome.roles.admin_title') }}</h3>
                        <p class="text-xs text-[#826871] leading-relaxed">{{ __('welcome.roles.admin_desc') }}</p>
                    </div>
                </div>

                <!-- Role: Operator -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#BE2B5B] text-white">
                                {{ __('common.roles.operator') }}
                            </span>
                            <span class="text-xs text-[#826871] font-medium">{{ __('welcome.roles.operator_scope') }}</span>
                        </div>
                        <h3 class="text-base font-bold text-[#4A2C35]">{{ __('welcome.roles.operator_title') }}</h3>
                        <p class="text-xs text-[#826871] leading-relaxed">{{ __('welcome.roles.operator_desc') }}</p>
                    </div>
                </div>

                <!-- Role: Customer -->
                <div class="bg-white rounded-[24px] border border-[#EFE6EA] p-6 shadow-[0_6px_20px_-8px_rgba(26,23,24,0.04)] flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#FCECF1] text-[#BE2B5B] border border-[#EFE6EA]">
                                {{ __('common.roles.customer') }}
                            </span>
                            <span class="text-xs text-[#826871] font-medium">{{ __('welcome.roles.customer_scope') }}</span>
                        </div>
                        <h3 class="text-base font-bold text-[#4A2C35]">{{ __('welcome.roles.customer_title') }}</h3>
                        <p class="text-xs text-[#826871] leading-relaxed">{{ __('welcome.roles.customer_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. "Why Choose CargoTrace?" 4-Column Section (SwiftDrop Bottom Feature Row) -->
        <section id="why-cargotrace" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-16 space-y-8">
            <div>
                <div class="w-8 h-1 rounded-full bg-[#BE2B5B] mb-3"></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#4A2C35]">
                    {{ $isId ? 'Mengapa Memilih CargoTrace?' : 'Why Choose CargoTrace?' }}
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-[22px] border border-[#EFE6EA] p-5 space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Standar SLA Terukur' : 'Deterministic SLA Rules' }}</h3>
                    <p class="text-xs text-[#826871] leading-relaxed">{{ $isId ? 'Ambang keterlambatan yang dapat dikonfigurasi langsung oleh administrator.' : 'Configurable delay thresholds with automated fleet-wide recalculation.' }}</p>
                </div>

                <div class="bg-white rounded-[22px] border border-[#EFE6EA] p-5 space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Kontrol Akses Multi-Peran' : 'Granular Role Access Control' }}</h3>
                    <p class="text-xs text-[#826871] leading-relaxed">{{ $isId ? 'Ruang kerja terpisah untuk Administrator, Operator Logistik, dan Pelanggan.' : 'Dedicated operational workspaces for Administrators, Operators, and Customers.' }}</p>
                </div>

                <div class="bg-white rounded-[22px] border border-[#EFE6EA] p-5 space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Pelacakan Geospasial' : 'Live Geospatial Tracking' }}</h3>
                    <p class="text-xs text-[#826871] leading-relaxed">{{ $isId ? 'Visualisasi rute lengkung OpenStreetMap dengan fokus titik waypoint.' : 'Smooth curved OpenStreetMap transit arcs with interactive waypoint focus.' }}</p>
                </div>

                <div class="bg-white rounded-[22px] border border-[#EFE6EA] p-5 space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#4A2C35]">{{ $isId ? 'Dukungan Dwibahasa' : 'Bilingual ID & EN Parity' }}</h3>
                    <p class="text-xs text-[#826871] leading-relaxed">{{ $isId ? 'Antarmuka lengkap Bahasa Indonesia dan Inggris dengan paritas penuh.' : 'Full Indonesian and English localization across web views and REST API.' }}</p>
                </div>
            </div>
        </section>
    </main>

    <!-- 7. Soft Blush Footer (#FCECF1 with Berry Pink #BE2B5B Accents — Zero Black) -->
    <footer class="bg-[#FCECF1]/85 border-t border-[#EFE4E8] text-[#4A2C35] pt-12 pb-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <!-- Col 1: Brand & Tagline -->
                <div class="md:col-span-4 space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-[#BE2B5B] flex items-center justify-center text-white shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-extrabold tracking-tight text-[#4A2C35] block leading-none">CargoTrace</span>
                            <span class="text-[11px] text-[#826871] block mt-1">{{ __('welcome.tagline') }}</span>
                        </div>
                    </a>
                    <p class="text-xs text-[#826871] leading-relaxed max-w-xs">
                        {{ __('welcome.footer_tagline') }}
                    </p>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="md:col-span-2 space-y-2.5 text-xs">
                    <div class="font-bold text-[#4A2C35]">{{ $isId ? 'Navigasi Cepat' : 'Quick Links' }}</div>
                    <ul class="space-y-2 text-[#826871]">
                        <li><a href="#hero" class="hover:text-[#BE2B5B] transition">{{ __('common.navigation.home') }}</a></li>
                        <li><a href="#services" class="hover:text-[#BE2B5B] transition">{{ $isId ? 'Layanan' : 'Services' }}</a></li>
                        <li><a href="#telemetry" class="hover:text-[#BE2B5B] transition">{{ $isId ? 'Telemetri' : 'Tracking' }}</a></li>
                        <li><a href="#why-cargotrace" class="hover:text-[#BE2B5B] transition">{{ $isId ? 'Keunggulan' : 'About' }}</a></li>
                    </ul>
                </div>

                <!-- Col 3: Services -->
                <div class="md:col-span-3 space-y-2.5 text-xs">
                    <div class="font-bold text-[#4A2C35]">{{ $isId ? 'Kapabilitas Platform' : 'Platform Modules' }}</div>
                    <ul class="space-y-2 text-[#826871]">
                        <li><a href="{{ route('login') }}" class="hover:text-[#BE2B5B] transition">{{ __('welcome.roles.admin_title') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-[#BE2B5B] transition">{{ __('welcome.roles.operator_title') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-[#BE2B5B] transition">{{ __('welcome.roles.customer_title') }}</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-[#BE2B5B] transition">{{ __('common.navigation.register') }}</a></li>
                    </ul>
                </div>

                <!-- Col 4: Quick Portal Access Pill -->
                <div class="md:col-span-3 space-y-3 text-xs">
                    <div class="font-bold text-[#4A2C35]">{{ __('welcome.access_platform') }}</div>
                    <p class="text-[#826871] leading-relaxed">
                        {{ $isId ? 'Masuk ke portal operasional CargoTrace untuk memantau pengiriman dan telemetri koridor.' : 'Sign in to the CargoTrace operations portal to monitor shipments and corridor telemetry.' }}
                    </p>
                    <div class="flex items-center justify-between rounded-full bg-white border border-[#EFE4E8] p-1 pl-4 shadow-2xs">
                        <span class="text-[11px] text-[#4A2C35] font-medium truncate">{{ $isId ? 'Portal Operasional CargoTrace' : 'CargoTrace Operations Portal' }}</span>
                        <a href="{{ route('login') }}" class="w-8 h-8 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white flex items-center justify-center shrink-0 transition" aria-label="{{ __('common.navigation.login') }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="pt-6 border-t border-[#EFE4E8] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#826871]">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                    <span class="font-bold text-[#4A2C35]">CargoTrace</span>
                    <span>&bull; {{ __('welcome.footer_tagline') }}</span>
                </div>
                <div>
                    &copy; {{ date('Y') }} CargoTrace. {{ $isId ? 'Hak cipta dilindungi.' : 'All rights reserved.' }}
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
