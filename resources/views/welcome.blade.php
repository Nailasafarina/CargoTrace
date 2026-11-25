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
    <header class="w-full border-b border-black/[0.05] bg-[#F3F4F6]/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                <div class="h-10 w-10 rounded-xl bg-[#18181B] flex items-center justify-center text-white shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-zinc-900">CargoTrace</span>
                    <span class="hidden sm:block text-[10px] tracking-wider uppercase text-zinc-500 font-medium">{{ __('welcome.tagline') }}</span>
                </div>
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
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-white text-xs font-semibold text-zinc-700 hover:text-zinc-950 shadow-2xs transition">
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
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold mb-6 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-[#FF6B1A] animate-pulse"></span>
                {{ __('welcome.badge') }}
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-zinc-900 max-w-4xl mx-auto leading-tight">
                {{ __('welcome.hero_title_prefix') }} <span class="text-[#FF6B1A]">{{ __('welcome.hero_title_highlight') }}</span> {{ __('welcome.hero_title_suffix') }}
            </h1>
            <p class="mt-6 text-base sm:text-lg text-zinc-600 max-w-2xl mx-auto leading-relaxed">
                {{ __('welcome.hero_subtitle') }}
            </p>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-[#18181B] hover:bg-zinc-800 text-white text-sm font-semibold shadow-sm transition">
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
