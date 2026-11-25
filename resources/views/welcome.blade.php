<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F6F3F5] text-zinc-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - {{ __('welcome.page_title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans antialiased bg-[#F6F3F5] text-zinc-800 selection:bg-[#E04879] selection:text-white">
    <!-- Floating Pill Header -->
    <header class="w-full bg-[#F6F3F5]/95 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                <div class="h-10 w-10 rounded-xl bg-[#1F171B] group-hover:bg-[#C93565] flex items-center justify-center text-white shadow-xs transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="2.5" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3.5m0 11V21M5.64 5.64l2.47 2.47m7.78 7.78l2.47 2.47M3 12h3.5m11 0H21M5.64 18.36l2.47-2.47m7.78-7.78l2.47-2.47" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-[#1F171B]">CargoTrace</span>
            </a>

            <div class="flex items-center gap-3">
                <!-- Language Switcher (ID | EN) -->
                <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-full bg-white p-1 shadow-[0_2px_10px_rgba(31,23,27,0.03)] text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                    <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-[#1F171B] text-white font-bold' : 'text-zinc-500 hover:text-[#1F171B]' }}" aria-label="Bahasa Indonesia">
                        ID
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-[#1F171B] text-white font-bold' : 'text-zinc-500 hover:text-[#1F171B]' }}" aria-label="English">
                        EN
                    </a>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.dashboard') }} &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-white text-xs font-semibold text-zinc-800 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition">
                        {{ __('common.navigation.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-sm shadow-[#C93565]/20 transition">
                        {{ __('common.navigation.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Asymmetric NexCargo Hero & Demo Workspace -->
    <main class="flex-grow max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-14">
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Editorial Thesis -->
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold shadow-[0_2px_10px_rgba(31,23,27,0.03)]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#E04879]"></span>
                    <span>{{ __('welcome.badge') }}</span>
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-[#1F171B] leading-[1.1]">
                    {{ __('welcome.hero_title_prefix') }} <span class="text-[#C93565]">{{ __('welcome.hero_title_highlight') }}</span> {{ __('welcome.hero_title_suffix') }}
                </h1>
                <p class="text-base text-zinc-600 max-w-xl leading-relaxed">
                    {{ __('welcome.hero_subtitle') }}
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-sm font-semibold shadow-sm transition">
                        {{ __('welcome.access_platform') }} &rarr;
                    </a>
                    <a href="#demo-accounts" class="px-6 py-3 rounded-full bg-white text-zinc-800 hover:bg-[#FDF2F6] border border-[#ECE4E8] text-sm font-semibold shadow-xs transition">
                        {{ __('welcome.view_demo_roles') }}
                    </a>
                </div>
            </div>

            <!-- Right Studio Logistics Cutaway Card -->
            <div class="lg:col-span-6 nex-card p-6 space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-full bg-[#FDF2F6] text-[#C93565] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <span class="text-sm font-bold text-[#1F171B]">{{ __('common.navigation.shipments') }}</span>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] text-[11px] font-semibold">
                        OpenStreetMap + Live Telemetry
                    </span>
                </div>

                <!-- Studio Truck + Cutaway Bays Showcase -->
                <div class="relative rounded-2xl bg-[#FAF7F9] border border-[#ECE4E8] p-4 overflow-hidden">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <img src="{{ asset('images/scania-cab.png') }}" alt="CargoTrace freight tractor" class="w-52 h-auto object-contain shrink-0 select-none drop-shadow-[0_10px_14px_rgba(31,23,27,0.1)]" />
                        <div class="flex-1 grid grid-cols-2 gap-2.5 w-full">
                            <div class="cargo-bay-hatch rounded-xl p-3 border border-[#ECE4E8] space-y-2">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-zinc-800">A1</span>
                                    <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                                </div>
                                <div class="text-xs font-bold text-zinc-900 font-mono">CT-ROT-SIN</div>
                            </div>
                            <div class="cargo-bay-hatch rounded-xl p-3 border-[1.5px] border-[#E04879] relative overflow-hidden space-y-2">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-[#9E224B]">A9</span>
                                    <span class="w-2 h-2 rounded-full bg-[#E04879]"></span>
                                </div>
                                <img src="{{ asset('images/cargo-boxes.png') }}" alt="Cargo bay units" class="absolute -right-1 -bottom-1 w-12 h-12 object-contain pointer-events-none select-none opacity-90" />
                                <div class="text-xs font-bold text-[#1F171B] font-mono relative z-10">IN_TRANSIT</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corridor Track Strip -->
                <div class="flex items-center justify-between gap-3 pt-1">
                    <span class="text-xs font-bold text-[#1F171B]">Jakarta</span>
                    <div class="flex-1 h-2.5 rounded-full route-track-hatch relative flex items-center justify-center">
                        <span class="w-5 h-5 rounded-full bg-[#E04879] text-white flex items-center justify-center shadow-xs">
                            <svg class="w-2.5 h-2.5 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                        </span>
                    </div>
                    <span class="text-xs font-bold text-[#1F171B]">Rotterdam</span>
                </div>
            </div>
        </section>

        <!-- Demo Roles Bento Grid -->
        <section id="demo-accounts" class="pt-8 border-t border-[#ECE4E8] grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Role: Admin -->
            <div class="nex-card p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#1F171B] text-white">
                        {{ __('common.roles.admin') }}
                    </span>
                    <span class="text-xs text-zinc-400">{{ __('welcome.roles.admin_scope') }}</span>
                </div>
                <h3 class="text-base font-bold text-[#1F171B]">{{ __('welcome.roles.admin_title') }}</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">{{ __('welcome.roles.admin_desc') }}</p>
                <div class="nex-inset p-3.5 text-xs space-y-1 font-mono text-zinc-700">
                    <div>{{ __('welcome.roles.email_label') }}: <span class="text-[#1F171B] font-semibold">admin@cargotrace.test</span></div>
                    <div>{{ __('welcome.roles.pass_label') }}: <span class="text-zinc-500">Password123!</span></div>
                </div>
            </div>

            <!-- Role: Operator -->
            <div class="nex-card p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#C93565] text-white">
                        {{ __('common.roles.operator') }}
                    </span>
                    <span class="text-xs text-zinc-400">{{ __('welcome.roles.operator_scope') }}</span>
                </div>
                <h3 class="text-base font-bold text-[#1F171B]">{{ __('welcome.roles.operator_title') }}</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">{{ __('welcome.roles.operator_desc') }}</p>
                <div class="nex-inset p-3.5 text-xs space-y-1 font-mono text-zinc-700">
                    <div>{{ __('welcome.roles.email_label') }}: <span class="text-[#C93565] font-semibold">operator@cargotrace.test</span></div>
                    <div>{{ __('welcome.roles.pass_label') }}: <span class="text-zinc-500">Password123!</span></div>
                </div>
            </div>

            <!-- Role: Customer -->
            <div class="nex-card p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white">
                        {{ __('common.roles.customer') }}
                    </span>
                    <span class="text-xs text-zinc-400">{{ __('welcome.roles.customer_scope') }}</span>
                </div>
                <h3 class="text-base font-bold text-[#1F171B]">{{ __('welcome.roles.customer_title') }}</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">{{ __('welcome.roles.customer_desc') }}</p>
                <div class="nex-inset p-3.5 text-xs space-y-1 font-mono text-zinc-700">
                    <div>{{ __('welcome.roles.email_label') }}: <span class="text-emerald-700 font-semibold">customer1@cargotrace.test</span></div>
                    <div>{{ __('welcome.roles.pass_label') }}: <span class="text-zinc-500">Password123!</span></div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#1F171B]/[0.05] bg-white/70 py-6 text-xs text-zinc-500">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#1F171B]">CargoTrace</span>
                <span>&bull; {{ __('welcome.footer_tagline') }}</span>
            </div>
            <div>
                {{ __('welcome.footer_academic') }}
            </div>
        </div>
    </footer>
</body>
</html>
