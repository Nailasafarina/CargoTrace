<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAF7F8] text-[#4A2C35]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('auth.login_title')) - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.i18n = @json(__('js'));
    </script>
</head>
<body class="min-h-screen font-sans antialiased bg-[#FAF7F8] text-[#4A2C35] selection:bg-[#BE2B5B] selection:text-white overflow-x-hidden">
    @php
        $isId = app()->getLocale() === 'id';
    @endphp

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Unified Light Visual & Branding Panel -->
        <aside class="hidden lg:flex lg:w-1/2 xl:w-[52%] relative bg-gradient-to-br from-[#FAF7F8] via-[#FCECF1]/65 to-[#FCECF1] text-[#4A2C35] border-r border-[#EFE6EA] px-8 py-6 xl:px-12 xl:py-8 flex-col justify-between overflow-hidden select-none">
            <div class="pointer-events-none absolute inset-0 opacity-[0.35]" style="background-image: linear-gradient(to right, #EFE6EA 1px, transparent 1px), linear-gradient(to bottom, #EFE6EA 1px, transparent 1px); background-size: 44px 44px;"></div>

            <!-- Top Bar: Brand Logo + Back to Home -->
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

            <!-- Center Showcase -->
            <div class="relative z-10 my-auto py-3 space-y-4 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white border border-[#EFE6EA] text-[#BE2B5B] text-xs font-bold tracking-tight shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
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

                    <div class="rounded-2xl bg-[#FAF7F8] p-3.5 border border-[#EFE6EA] flex items-center justify-between gap-3.5">
                        <img src="{{ asset('images/scania-cab.png') }}" alt="CargoTrace transport tractor" class="w-36 xl:w-44 h-auto object-contain shrink-0 select-none drop-shadow-[0_10px_14px_rgba(26,23,24,0.12)]" />
                        <div class="flex-1 rounded-2xl p-3 bg-white border-2 border-[#BE2B5B] relative overflow-hidden space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-extrabold text-[#BE2B5B]">BAY A9</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#BE2B5B]">
                                    <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                                    LIVE
                                </span>
                            </div>
                            <img src="{{ asset('images/cargo-boxes.png') }}" alt="Cargo units" class="w-14 h-10 object-contain mx-auto select-none" />
                            <div class="text-xs font-extrabold text-[#4A2C35] text-center font-mono">CT-LIVE-OSM</div>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-0.5">
                        <div class="flex items-center justify-between text-xs font-bold text-[#4A2C35]">
                            <span>Singapore PSA</span>
                            <span class="text-[11px] font-mono font-semibold text-[#BE2B5B]">IN_TRANSIT · 68%</span>
                            <span>Port of Rotterdam</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-[#FCECF1] border border-[#EFE6EA] overflow-hidden p-0.5">
                            <div class="h-full bg-[#BE2B5B] rounded-full w-2/3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Enterprise Indicators -->
            <div class="relative z-10 pt-4 border-t border-[#EFE6EA] grid grid-cols-3 gap-3">
                <div class="flex items-center gap-2.5 bg-white/80 border border-[#EFE6EA] rounded-xl px-3 py-2 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-extrabold text-[#4A2C35] leading-tight">99.9% Uptime</div>
                        <div class="text-[10px] text-[#826871] truncate">{{ $isId ? 'Keandalan SLA' : 'SLA Reliability' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 bg-white/80 border border-[#EFE6EA] rounded-xl px-3 py-2 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-extrabold text-[#4A2C35] leading-tight">Real-Time ETA</div>
                        <div class="text-[10px] text-[#826871] truncate">{{ $isId ? 'Kalkulasi Dinamis' : 'Dynamic Engine' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 bg-white/80 border border-[#EFE6EA] rounded-xl px-3 py-2 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-[#FCECF1] text-[#BE2B5B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-extrabold text-[#4A2C35] leading-tight">Multi-Role Access</div>
                        <div class="text-[10px] text-[#826871] truncate">{{ $isId ? 'Keamanan RBAC' : 'Audited RBAC' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Form Panel -->
        <div class="flex-1 flex flex-col justify-between px-4 py-4 sm:px-10 sm:py-6 lg:px-12 lg:py-8 bg-[#FAF7F8]">
            <div class="flex items-center justify-between gap-2 sm:gap-4">
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

                {{ $slot ?? '' }}
                @yield('content')
            </div>

            <div class="text-center text-[11px] text-[#826871]">
                &copy; {{ date('Y') }} CargoTrace &bull; {{ __('welcome.tagline') }}
            </div>
        </div>
    </div>
</body>
</html>
