<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - {{ __('welcome.page_title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans antialiased bg-slate-50 text-slate-800 selection:bg-blue-600 selection:text-white">
    <!-- Subtle Background Gradient Accent -->
    <div class="fixed inset-0 pointer-events-none -z-10 bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(37,99,235,0.08),rgba(255,255,255,0))]"></div>

    <!-- Navigation -->
    <header class="w-full border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Cargo<span class="text-blue-600">Trace</span></span>
                    <span class="hidden sm:block text-[10px] tracking-wider uppercase text-slate-500 font-medium">{{ __('welcome.tagline') }}</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <!-- Language Switcher (ID | EN) -->
                <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-xl bg-slate-100 p-0.5 border border-slate-200 text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                    <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-1 rounded-lg transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-white text-blue-600 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-900' }}" aria-label="Bahasa Indonesia">
                        ID
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-1 rounded-lg transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-white text-blue-600 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-900' }}" aria-label="English">
                        EN
                    </a>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.dashboard') }} &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        {{ __('common.navigation.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        {{ __('common.navigation.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-medium mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                {{ __('welcome.badge') }}
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-slate-900 max-w-4xl mx-auto leading-tight">
                {{ __('welcome.hero_title_prefix') }} <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ __('welcome.hero_title_highlight') }}</span> {{ __('welcome.hero_title_suffix') }}
            </h1>
            <p class="mt-6 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                {{ __('welcome.hero_subtitle') }}
            </p>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm hover:shadow transition">
                    {{ __('welcome.access_platform') }} &rarr;
                </a>
                <a href="#demo-accounts" class="px-6 py-3 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-sm font-semibold shadow-2xs transition">
                    {{ __('welcome.view_demo_roles') }}
                </a>
            </div>

            <!-- Demo Roles Cards -->
            <div id="demo-accounts" class="mt-16 pt-12 border-t border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <!-- Role: Admin -->
                <div class="p-6 rounded-2xl bg-white border border-purple-200 hover:border-purple-300 shadow-xs hover:shadow transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                            {{ __('common.roles.admin') }}
                        </span>
                        <span class="text-xs text-slate-500">{{ __('welcome.roles.admin_scope') }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">{{ __('welcome.roles.admin_title') }}</h3>
                    <p class="text-xs text-slate-600 mt-1 mb-4">{{ __('welcome.roles.admin_desc') }}</p>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1 font-mono text-slate-700">
                        <div>{{ __('welcome.roles.email_label') }}: <span class="text-purple-700 font-semibold">admin@cargotrace.test</span></div>
                        <div>{{ __('welcome.roles.pass_label') }}: <span class="text-slate-500">Password123!</span></div>
                    </div>
                </div>

                <!-- Role: Operator -->
                <div class="p-6 rounded-2xl bg-white border border-amber-200 hover:border-amber-300 shadow-xs hover:shadow transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                            {{ __('common.roles.operator') }}
                        </span>
                        <span class="text-xs text-slate-500">{{ __('welcome.roles.operator_scope') }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">{{ __('welcome.roles.operator_title') }}</h3>
                    <p class="text-xs text-slate-600 mt-1 mb-4">{{ __('welcome.roles.operator_desc') }}</p>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1 font-mono text-slate-700">
                        <div>{{ __('welcome.roles.email_label') }}: <span class="text-amber-800 font-semibold">operator@cargotrace.test</span></div>
                        <div>{{ __('welcome.roles.pass_label') }}: <span class="text-slate-500">Password123!</span></div>
                    </div>
                </div>

                <!-- Role: Customer -->
                <div class="p-6 rounded-2xl bg-white border border-emerald-200 hover:border-emerald-300 shadow-xs hover:shadow transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ __('common.roles.customer') }}
                        </span>
                        <span class="text-xs text-slate-500">{{ __('welcome.roles.customer_scope') }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">{{ __('welcome.roles.customer_title') }}</h3>
                    <p class="text-xs text-slate-600 mt-1 mb-4">{{ __('welcome.roles.customer_desc') }}</p>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1 font-mono text-slate-700">
                        <div>{{ __('welcome.roles.email_label') }}: <span class="text-emerald-700 font-semibold">customer1@cargotrace.test</span></div>
                        <div>{{ __('welcome.roles.pass_label') }}: <span class="text-slate-500">Password123!</span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800">CargoTrace</span>
                <span>&bull; {{ __('welcome.footer_tagline') }}</span>
            </div>
            <div>
                {{ __('welcome.footer_academic') }}
            </div>
        </div>
    </footer>
</body>
</html>
