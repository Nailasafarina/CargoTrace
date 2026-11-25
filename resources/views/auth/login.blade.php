<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAF7F8] text-[#4A2C35]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.login_title') }} - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.i18n = @json(__('js'));
    </script>
    <style>
        /* Override browser blue/yellow autofill background to keep inputs clean white/soft-blush */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 32px #FFFFFF inset !important;
            -webkit-text-fill-color: #4A2C35 !important;
            caret-color: #4A2C35;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-[#FAF7F8] text-[#4A2C35] selection:bg-[#BE2B5B] selection:text-white overflow-x-hidden">
    @php
        $isId = app()->getLocale() === 'id';
    @endphp

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- 1. Left Visual & Branding Panel (~50% Desktop, Unified Light Blush Gradient #FAF7F8 -> #FCECF1) -->
        <aside class="hidden lg:flex lg:w-1/2 xl:w-[52%] relative bg-gradient-to-br from-[#FAF7F8] via-[#FCECF1]/65 to-[#FCECF1] text-[#4A2C35] border-r border-[#EFE6EA] px-8 py-6 xl:px-12 xl:py-8 flex-col justify-between overflow-hidden select-none">
            <!-- Soft Ambient Blush Accents & Subtle Grid Pattern -->
            <div class="pointer-events-none absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#BE2B5B]/[0.07] blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-[#BE2B5B]/[0.08] blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 opacity-[0.35]" style="background-image: linear-gradient(to right, #EFE6EA 1px, transparent 1px), linear-gradient(to bottom, #EFE6EA 1px, transparent 1px); background-size: 44px 44px;"></div>

            <!-- Decorative Subtle Corridor Arc SVG -->
            <svg class="pointer-events-none absolute inset-x-0 top-1/3 w-full h-56 opacity-25" viewBox="0 0 600 200" fill="none" preserveAspectRatio="none" aria-hidden="true">
                <path d="M -20 160 Q 180 20, 340 100 T 620 40" stroke="#BE2B5B" stroke-width="2" stroke-dasharray="6 6" fill="none" />
            </svg>

            <!-- Top Bar: Brand Logo + Back to Home Button -->
            <div class="relative z-10 flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="h-10 w-10 rounded-xl bg-[#BE2B5B] group-hover:bg-[#A0224B] flex items-center justify-center text-white shadow-xs transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-[#4A2C35] block leading-none">CargoTrace</span>
                        <span class="text-[10px] font-medium text-[#826871] tracking-wider uppercase block mt-1">{{ __('welcome.tagline') }}</span>
                    </div>
                </a>

                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white hover:bg-[#FCECF1] border border-[#EFE6EA] text-xs font-semibold text-[#4A2C35] hover:text-[#BE2B5B] shadow-2xs transition">
                    <span>&larr;</span>
                    <span>{{ $isId ? 'Kembali ke Beranda' : 'Back to Home' }}</span>
                </a>
            </div>

            <!-- Center Showcase: Headline + White Truck & Telemetry Card -->
            <div class="relative z-10 my-auto py-3 space-y-4 max-w-xl">
                <!-- Clean Telemetry Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white border border-[#EFE6EA] text-[#BE2B5B] text-xs font-bold tracking-tight shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#BE2B5B] animate-pulse"></span>
                    <span>{{ $isId ? 'Live Telemetry · Koridor Global' : 'Live Telemetry · Global Corridors' }}</span>
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl xl:text-[32px] font-extrabold tracking-tight text-[#4A2C35] leading-[1.18]">
                        {{ $isId ? 'Kendali Penuh atas Setiap Koridor Pengiriman' : 'Full Control Over Every Shipment Corridor' }}
                    </h2>
                    <p class="text-xs xl:text-sm text-[#826871] leading-relaxed">
                        {{ $isId ? 'Pantau pergerakan armada, evaluasi ETA waktu-nyata, dan kelola kepatuhan operasional lintas pelabuhan dalam satu konsol terpadu.' : 'Monitor fleet movement, evaluate real-time ETAs, and manage cross-port operational compliance in one unified console.' }}
                    </p>
                </div>

                <!-- White Truck & Telemetry Showcase Card (BAY A9, CT-LIVE-OSM, Singapore PSA - Port of Rotterdam) -->
                <div class="rounded-2xl bg-white border border-[#EFE6EA] p-4 xl:p-5 shadow-[0_14px_34px_-12px_rgba(26,23,24,0.07)] space-y-3.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-[#4A2C35] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $isId ? 'Telemetri Armada Aktif' : 'Active Fleet Telemetry' }}</span>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#FCECF1] border border-[#EFE6EA] text-[#BE2B5B] text-[10px] font-mono font-bold">
                            OpenStreetMap · WGS84
                        </span>
                    </div>

                    <div class="rounded-2xl bg-[#FAF7F8] p-3.5 border border-[#EFE6EA] flex flex-col sm:flex-row items-center justify-between gap-3.5">
                        <img
                            src="{{ asset('images/scania-cab.png') }}"
                            alt="CargoTrace transport tractor"
                            class="w-36 xl:w-44 h-auto object-contain shrink-0 select-none drop-shadow-[0_10px_14px_rgba(26,23,24,0.12)]"
                        />
                        <div class="w-full sm:flex-1 rounded-2xl p-3 bg-white border-2 border-[#BE2B5B] relative overflow-hidden space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-extrabold text-[#BE2B5B]">BAY A9</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#BE2B5B]">
                                    <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                                    LIVE
                                </span>
                            </div>
                            <img
                                src="{{ asset('images/cargo-boxes.png') }}"
                                alt="Cargo units"
                                class="w-14 h-10 object-contain mx-auto select-none drop-shadow-xs"
                            />
                            <div class="text-xs font-extrabold text-[#4A2C35] text-center font-mono tracking-tight">
                                CT-LIVE-OSM
                            </div>
                        </div>
                    </div>

                    <!-- Corridor Progress Bar: Singapore PSA -> Port of Rotterdam (High-Contrast on Light Theme) -->
                    <div class="space-y-1.5 pt-0.5">
                        <div class="flex items-center justify-between text-xs font-bold text-[#4A2C35]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                                <span>Singapore PSA</span>
                            </div>
                            <span class="text-[11px] font-mono font-semibold text-[#BE2B5B]">IN_TRANSIT · 68%</span>
                            <div class="flex items-center gap-1.5">
                                <span>Port of Rotterdam</span>
                                <span class="w-2 h-2 rounded-full bg-[#BE2B5B]/40"></span>
                            </div>
                        </div>
                        <div class="h-2.5 rounded-full bg-[#FCECF1] border border-[#EFE6EA] overflow-hidden p-0.5">
                            <div class="h-full bg-[#BE2B5B] rounded-full w-2/3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Enterprise Indicators (Compact & Never Clipped) -->
            <div class="relative z-10 pt-4 border-t border-[#EFE6EA] grid grid-cols-3 gap-3">
                <div class="flex items-center gap-2.5 bg-white/80 border border-[#EFE6EA] rounded-xl px-3 py-2 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-extrabold text-[#4A2C35] leading-tight">99.9% Uptime</div>
                        <div class="text-[10px] text-[#826871] truncate">{{ $isId ? 'Keandalan SLA' : 'SLA Reliability' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 bg-white/80 border border-[#EFE6EA] rounded-xl px-3 py-2 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-extrabold text-[#4A2C35] leading-tight">Real-Time ETA</div>
                        <div class="text-[10px] text-[#826871] truncate">{{ $isId ? 'Kalkulasi Dinamis' : 'Dynamic Engine' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 bg-white/80 border border-[#EFE6EA] rounded-xl px-3 py-2 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-extrabold text-[#4A2C35] leading-tight">Multi-Role Access</div>
                        <div class="text-[10px] text-[#826871] truncate">{{ $isId ? 'Keamanan RBAC' : 'Audited RBAC' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 2. Right Login Form Panel (~50% Desktop, Soft Blush #FAF7F8) -->
        <div class="flex-1 flex flex-col justify-between px-4 py-4 sm:px-10 sm:py-6 lg:px-12 lg:py-8 bg-[#FAF7F8]">
            <!-- Top Right Language Switcher & Mobile Brand Header -->
            <div class="flex items-center justify-between gap-2 sm:gap-4">
                <!-- Mobile Brand + Back Link (visible when left panel is hidden) -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 min-w-0">
                        <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-[#BE2B5B] flex items-center justify-center text-white shadow-xs shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="text-base sm:text-lg font-extrabold tracking-tight text-[#4A2C35] truncate">CargoTrace</span>
                    </a>
                </div>
                <div class="hidden lg:block"></div>

                <!-- Language Switcher (ID | EN) + Mobile Back Button -->
                <div class="flex items-center gap-1.5 sm:gap-2.5 ml-auto shrink-0">
                    <a href="{{ route('home') }}" class="lg:hidden px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-full bg-white border border-[#EFE6EA] text-[11px] sm:text-xs font-semibold text-[#4A2C35] hover:bg-[#FCECF1]/50 transition whitespace-nowrap">
                        &larr; {{ __('common.actions.back') }}
                    </a>

                    <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-full bg-white border border-[#EFE6EA] p-0.5 sm:p-1 shadow-2xs text-[11px] sm:text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                        <a href="{{ route('locale.set', 'id') }}" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-[#BE2B5B] text-white font-bold' : 'text-[#826871] hover:text-[#4A2C35]' }}" aria-label="Bahasa Indonesia">
                            ID
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-[#BE2B5B] text-white font-bold' : 'text-[#826871] hover:text-[#4A2C35]' }}" aria-label="English">
                            EN
                        </a>
                    </div>
                </div>
            </div>

            <!-- Centered Form Card Container -->
            <div class="w-full max-w-[460px] mx-auto my-4">
                @if (session('status'))
                    <div class="mb-4 p-4 rounded-2xl bg-[#FCECF1] border border-[#EFE6EA] text-[#4A2C35] text-sm flex items-start gap-3 shadow-2xs">
                        <svg class="w-5 h-5 text-[#BE2B5B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-2xs">
                        <p class="font-semibold mb-1">{{ __('auth.check_errors') }}</p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-[24px] sm:rounded-[28px] border border-[#EFE6EA] p-5 sm:p-8 shadow-[0_12px_30px_-10px_rgba(26,23,24,0.05)]">
                    <!-- Heading & Subtitle -->
                    <div class="mb-5">
                        <h1 class="text-xl sm:text-[26px] font-extrabold text-[#4A2C35] tracking-tight">
                            {{ __('auth.login_title') }}
                        </h1>
                        <p class="text-xs sm:text-sm text-[#826871] mt-1 leading-relaxed">
                            {{ __('auth.login_subtitle') }}
                        </p>
                    </div>

                    <!-- Sleek Demo Account Credentials Box (3-Column Grid without Truncation) -->
                    <div class="mb-5 p-3 sm:p-3.5 bg-[#FAF7F8] border border-[#EFE6EA] rounded-2xl">
                        <div class="flex flex-wrap items-center justify-between gap-1.5 mb-2.5">
                            <span class="text-[11px] font-bold text-[#4A2C35]">{{ __('auth.demo_accounts') }}</span>
                            <span class="text-[10px] text-[#BE2B5B] font-bold">{{ __('auth.click_autofill') }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <button
                                type="button"
                                onclick="fillDemo('admin@cargotrace.test', 'Password123!')"
                                class="px-2.5 py-2 rounded-xl bg-[#FCECF1] hover:bg-[#BE2B5B] text-[#BE2B5B] hover:text-white border border-[#F3D5DF] text-[11px] font-semibold leading-tight transition cursor-pointer text-center flex items-center justify-center min-h-[38px] shadow-2xs"
                            >
                                {{ __('common.roles.admin') }}
                            </button>
                            <button
                                type="button"
                                onclick="fillDemo('operator@cargotrace.test', 'Password123!')"
                                class="px-2.5 py-2 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-[11px] font-semibold leading-tight transition cursor-pointer text-center flex items-center justify-center min-h-[38px] shadow-2xs"
                            >
                                {{ __('common.roles.operator') }}
                            </button>
                            <button
                                type="button"
                                onclick="fillDemo('customer1@cargotrace.test', 'Password123!')"
                                class="px-2.5 py-2 rounded-xl bg-white hover:bg-[#FCECF1] text-[#4A2C35] hover:text-[#BE2B5B] border border-[#EFE4E8] text-[11px] font-semibold leading-tight transition cursor-pointer text-center flex items-center justify-center min-h-[38px] shadow-2xs"
                            >
                                {{ __('common.roles.customer') }}
                            </button>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email Input with Envelope Icon -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-[#4A2C35] mb-1.5">
                                {{ __('auth.email') }}
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#826871]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="w-full rounded-xl bg-white border border-[#E5DCE0] pl-10 pr-4 py-2.5 text-sm text-[#4A2C35] placeholder-[#6B6467]/60 focus:bg-white focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                                    placeholder="name@cargotrace.test"
                                />
                            </div>
                            @error('email')
                                <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input with Lock Icon & Show/Hide Toggle -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-[#4A2C35] mb-1.5">
                                {{ __('auth.password_label') }}
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#826871]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full rounded-xl bg-white border border-[#E5DCE0] pl-10 pr-11 py-2.5 text-sm text-[#4A2C35] placeholder-[#6B6467]/60 focus:bg-white focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                                    placeholder="••••••••••••"
                                />
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#826871] hover:text-[#BE2B5B] transition cursor-pointer"
                                    aria-label="Toggle password visibility"
                                >
                                    <svg id="eye-open-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eye-closed-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Device Checkbox -->
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-[#826871] hover:text-[#4A2C35] transition">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="w-4 h-4 rounded border-[#E5DCE0] text-[#BE2B5B] focus:ring-[#BE2B5B]/20"
                                />
                                <span>{{ __('auth.remember_me') }}</span>
                            </label>
                        </div>

                        <!-- Primary Submit Button (Berry Pink #BE2B5B with Arrow) -->
                        <div class="pt-1.5">
                            <button
                                type="submit"
                                class="w-full py-3 px-5 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-sm font-bold shadow-sm transition duration-200 cursor-pointer inline-flex items-center justify-center gap-2"
                            >
                                <span>{{ __('auth.sign_in_button') }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </button>
                        </div>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-5 pt-4 border-t border-[#EFE6EA] text-center text-xs text-[#826871]">
                        {{ __('auth.dont_have_account') }}
                        <a href="{{ route('register') }}" class="font-bold text-[#BE2B5B] hover:text-[#A0224B] hover:underline transition ml-0.5">
                            {{ __('auth.sign_up_button') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Subtle Bottom Copyright -->
            <div class="text-center text-[11px] text-[#826871]">
                &copy; {{ date('Y') }} CargoTrace &bull; {{ __('welcome.tagline') }}
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open-icon');
            const eyeClosed = document.getElementById('eye-closed-icon');
            if (!passwordInput) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
