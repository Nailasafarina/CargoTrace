<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F2F3F5] text-zinc-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - {{ __('welcome.page_title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans antialiased bg-[#F2F3F5] text-zinc-800 selection:bg-[#FF6B1A] selection:text-white">
    <!-- Navigation -->
    <header class="w-full bg-[#F2F3F5]/95 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                <div class="h-10 w-10 rounded-xl bg-[#18181B] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="2.5" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3.5m0 11V21M5.64 5.64l2.47 2.47m7.78 7.78l2.47 2.47M3 12h3.5m11 0H21M5.64 18.36l2.47-2.47m7.78-7.78l2.47-2.47" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-zinc-900">CargoTrace</span>
            </a>

            <div class="flex items-center gap-3">
                <!-- Language Switcher (ID | EN) -->
                <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-full bg-white p-1 shadow-2xs text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                    <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-[#18181B] text-white font-bold' : 'text-zinc-500 hover:text-zinc-900' }}" aria-label="Bahasa Indonesia">
                        ID
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-[#18181B] text-white font-bold' : 'text-zinc-500 hover:text-zinc-900' }}" aria-label="English">
                        EN
                    </a>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full bg-[#FF6B1A] hover:bg-[#e5560a] text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.dashboard') }} &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-white text-xs font-semibold text-zinc-800 hover:bg-zinc-50 shadow-2xs transition">
                        {{ __('common.navigation.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-[#FF6B1A] hover:bg-[#e5560a] text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white text-zinc-700 text-xs font-medium mb-6 shadow-2xs">
                <span>{{ __('welcome.badge') }}</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-zinc-900 max-w-4xl mx-auto leading-tight">
                {{ __('welcome.hero_title_prefix') }} <span class="text-[#FF6B1A]">{{ __('welcome.hero_title_highlight') }}</span> {{ __('welcome.hero_title_suffix') }}
            </h1>
            <p class="mt-6 text-base sm:text-lg text-zinc-600 max-w-2xl mx-auto leading-relaxed">
                {{ __('welcome.hero_subtitle') }}
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3.5">
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-[#18181B] hover:bg-zinc-800 text-white text-sm font-semibold shadow-sm transition">
                    {{ __('welcome.access_platform') }} &rarr;
                </a>
                <a href="#demo-accounts" class="px-6 py-3 rounded-full bg-white text-zinc-800 hover:bg-zinc-50 text-sm font-semibold shadow-2xs transition">
                    {{ __('welcome.view_demo_roles') }}
                </a>
            </div>

            <!-- Demo Roles Cards -->
            <div id="demo-accounts" class="mt-16 pt-12 border-t border-zinc-200/80 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <!-- Role: Admin -->
                <div class="p-6 rounded-[24px] bg-white shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#18181B] text-white">
                            {{ __('common.roles.admin') }}
                        </span>
                        <span class="text-xs text-zinc-400">{{ __('welcome.roles.admin_scope') }}</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-900">{{ __('welcome.roles.admin_title') }}</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed">{{ __('welcome.roles.admin_desc') }}</p>
                    <div class="p-3.5 bg-[#F2F3F5] rounded-2xl text-xs space-y-1 font-mono text-zinc-700">
                        <div>{{ __('welcome.roles.email_label') }}: <span class="text-zinc-900 font-semibold">admin@cargotrace.test</span></div>
                        <div>{{ __('welcome.roles.pass_label') }}: <span class="text-zinc-500">Password123!</span></div>
                    </div>
                </div>

                <!-- Role: Operator -->
                <div class="p-6 rounded-[24px] bg-white shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#FF6B1A] text-white">
                            {{ __('common.roles.operator') }}
                        </span>
                        <span class="text-xs text-zinc-400">{{ __('welcome.roles.operator_scope') }}</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-900">{{ __('welcome.roles.operator_title') }}</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed">{{ __('welcome.roles.operator_desc') }}</p>
                    <div class="p-3.5 bg-[#F2F3F5] rounded-2xl text-xs space-y-1 font-mono text-zinc-700">
                        <div>{{ __('welcome.roles.email_label') }}: <span class="text-[#FF6B1A] font-semibold">operator@cargotrace.test</span></div>
                        <div>{{ __('welcome.roles.pass_label') }}: <span class="text-zinc-500">Password123!</span></div>
                    </div>
                </div>

                <!-- Role: Customer -->
                <div class="p-6 rounded-[24px] bg-white shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white">
                            {{ __('common.roles.customer') }}
                        </span>
                        <span class="text-xs text-zinc-400">{{ __('welcome.roles.customer_scope') }}</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-900">{{ __('welcome.roles.customer_title') }}</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed">{{ __('welcome.roles.customer_desc') }}</p>
                    <div class="p-3.5 bg-[#F2F3F5] rounded-2xl text-xs space-y-1 font-mono text-zinc-700">
                        <div>{{ __('welcome.roles.email_label') }}: <span class="text-emerald-700 font-semibold">customer1@cargotrace.test</span></div>
                        <div>{{ __('welcome.roles.pass_label') }}: <span class="text-zinc-500">Password123!</span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-black/[0.04] bg-white/60 py-6 text-xs text-zinc-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-zinc-900">CargoTrace</span>
                <span>&bull; {{ __('welcome.footer_tagline') }}</span>
            </div>
            <div>
                {{ __('welcome.footer_academic') }}
            </div>
        </div>
    </footer>
</body>
</html>
