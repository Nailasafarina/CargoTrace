<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F2F3F5] text-zinc-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.i18n = @json(__('js'));
    </script>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col font-sans antialiased bg-[#F2F3F5] text-zinc-800 selection:bg-[#FF6B1A] selection:text-white">
    <!-- NexCargo-Style Floating Pill Top Navigation Bar (Seamless on #F2F3F5 Canvas) -->
    <header class="sticky top-0 z-50 bg-[#F2F3F5]/95 backdrop-blur-md">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Left Zone: Dark Rounded Square Emblem + Brand Wordmark -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="h-10 w-10 rounded-xl bg-[#18181B] flex items-center justify-center text-white shadow-xs group-hover:bg-[#FF6B1A] transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="2.5" stroke-width="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3.5m0 11V21M5.64 5.64l2.47 2.47m7.78 7.78l2.47 2.47M3 12h3.5m11 0H21M5.64 18.36l2.47-2.47m7.78-7.78l2.47-2.47" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-zinc-900">CargoTrace</span>
                    </a>
                </div>

                <!-- Center Zone: Floating Pill Tabs (Matching Reference Screenshot) -->
                <nav class="hidden lg:flex items-center gap-2.5 text-sm font-medium" aria-label="Primary Navigation">
                    @php
                        $isDashboardActive = request()->routeIs('admin.dashboard', 'operator.dashboard', 'customer.dashboard', 'dashboard');
                        $dashboardRoute = Auth::user()->isAdmin()
                            ? route('admin.dashboard')
                            : (Auth::user()->isOperator() ? route('operator.dashboard') : route('customer.dashboard'));
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="px-6 py-2.5 rounded-full transition-all duration-150 whitespace-nowrap {{ $isDashboardActive ? 'bg-[#FF6B1A] text-white font-semibold shadow-sm shadow-[#FF6B1A]/20' : 'bg-white text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 shadow-[0_2px_10px_rgba(0,0,0,0.02)]' }}">
                        {{ __('common.navigation.dashboard') }}
                    </a>

                    @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                        <a href="{{ route('carriers.index') }}" class="px-6 py-2.5 rounded-full transition-all duration-150 whitespace-nowrap {{ request()->routeIs('carriers.*') ? 'bg-[#FF6B1A] text-white font-semibold shadow-sm shadow-[#FF6B1A]/20' : 'bg-white text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 shadow-[0_2px_10px_rgba(0,0,0,0.02)]' }}">
                            {{ __('common.navigation.carriers') }}
                        </a>
                    @endif

                    <a href="{{ route('shipments.index') }}" class="px-6 py-2.5 rounded-full transition-all duration-150 whitespace-nowrap {{ request()->routeIs('shipments.*') ? 'bg-[#FF6B1A] text-white font-semibold shadow-sm shadow-[#FF6B1A]/20' : 'bg-white text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 shadow-[0_2px_10px_rgba(0,0,0,0.02)]' }}">
                        {{ __('common.navigation.shipments') }}
                    </a>

                    @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                        <a href="{{ route('locations.index') }}" class="px-6 py-2.5 rounded-full transition-all duration-150 whitespace-nowrap {{ request()->routeIs('locations.*') ? 'bg-[#FF6B1A] text-white font-semibold shadow-sm shadow-[#FF6B1A]/20' : 'bg-white text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 shadow-[0_2px_10px_rgba(0,0,0,0.02)]' }}">
                            {{ __('common.navigation.locations') }}
                        </a>
                    @endif

                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.audit-logs.index') }}" class="px-6 py-2.5 rounded-full transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#FF6B1A] text-white font-semibold shadow-sm shadow-[#FF6B1A]/20' : 'bg-white text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 shadow-[0_2px_10px_rgba(0,0,0,0.02)]' }}">
                            {{ __('common.navigation.audit_logs') }}
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="px-6 py-2.5 rounded-full transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.settings.*') ? 'bg-[#FF6B1A] text-white font-semibold shadow-sm shadow-[#FF6B1A]/20' : 'bg-white text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 shadow-[0_2px_10px_rgba(0,0,0,0.02)]' }}">
                            {{ __('common.navigation.settings') }}
                        </a>
                    @endif
                </nav>

                <!-- Right Zone: Circular Utility Icons, Language Switcher & Profile Avatar -->
                <div class="hidden md:flex items-center gap-2.5 shrink-0">
                    <!-- Quick Search Button -->
                    <a href="{{ route('shipments.index') }}" class="w-10 h-10 rounded-full bg-white text-zinc-700 hover:text-zinc-950 flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition" title="{{ __('common.navigation.shipments') }}" aria-label="{{ __('common.navigation.shipments') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>

                    <!-- Language Switcher (ID | EN) -->
                    <div class="relative z-20 pointer-events-auto flex items-center rounded-full bg-white p-1 shadow-[0_2px_10px_rgba(0,0,0,0.03)] text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                        <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-[#18181B] text-white font-bold' : 'text-zinc-500 hover:text-zinc-900' }}" aria-label="Bahasa Indonesia">
                            ID
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-1 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-[#18181B] text-white font-bold' : 'text-zinc-500 hover:text-zinc-900' }}" aria-label="English">
                            EN
                        </a>
                    </div>

                    <!-- Notification Bell -->
                    @php
                        $navUnreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" class="relative w-10 h-10 rounded-full bg-white text-zinc-700 hover:text-zinc-950 flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition" title="{{ __('notifications.title') }}" aria-label="{{ __('notifications.title') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($navUnreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-[#FF6B1A] text-[10px] font-bold text-white shadow-xs">
                                {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- User Profile Avatar & Sign Out -->
                    <div class="flex items-center gap-2 bg-white rounded-full pl-1.5 pr-2.5 py-1 shadow-[0_2px_10px_rgba(0,0,0,0.03)]">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#18181B] to-zinc-700 text-white flex items-center justify-center text-xs font-bold uppercase shrink-0" title="{{ Auth::user()->name }} ({{ Auth::user()->role }})">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="text-left hidden 2xl:block">
                            <div class="text-xs font-bold text-zinc-900 leading-tight">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-zinc-500 font-mono leading-tight">
                                role: {{ Auth::user()->role }}
                            </div>
                        </div>
                        <span class="sr-only">role: {{ Auth::user()->role }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1.5 rounded-full text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="{{ __('common.navigation.logout') }}" aria-label="{{ __('common.navigation.logout') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center gap-2 lg:hidden">
                    <div class="relative z-20 pointer-events-auto flex items-center rounded-full bg-white p-1 shadow-2xs text-xs font-semibold shrink-0">
                        <a href="{{ route('locale.set', 'id') }}" class="px-2 py-0.5 rounded-full transition {{ app()->getLocale() === 'id' ? 'bg-[#18181B] text-white font-bold' : 'text-zinc-500' }}" aria-label="Bahasa Indonesia">ID</a>
                        <a href="{{ route('locale.set', 'en') }}" class="px-2 py-0.5 rounded-full transition {{ app()->getLocale() === 'en' ? 'bg-[#18181B] text-white font-bold' : 'text-zinc-500' }}" aria-label="English">EN</a>
                    </div>
                    <button type="button" id="mobile-menu-btn" class="p-2.5 rounded-full bg-white text-zinc-700 hover:text-zinc-950 shadow-2xs focus:outline-none" aria-label="Toggle Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-zinc-200 px-4 pt-3 pb-4 space-y-3 bg-white shadow-lg">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-100">
                <div>
                    <div class="text-sm font-bold text-zinc-900">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-zinc-500">{{ Auth::user()->email }} &bull; role: {{ Auth::user()->role }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ __('common.navigation.dashboard') }}</a>
                <a href="{{ route('shipments.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ __('common.navigation.shipments') }}</a>
                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">
                    <span>{{ __('common.navigation.notifications') }}</span>
                    @if($navUnreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#FF6B1A] text-white">{{ $navUnreadCount }}</span>
                    @endif
                </a>
                @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                    <a href="{{ route('carriers.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ __('common.navigation.carriers') }}</a>
                    <a href="{{ route('locations.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ __('common.navigation.locations') }}</a>
                @endif
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.audit-logs.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ __('common.navigation.audit_logs') }}</a>
                    <a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ __('common.navigation.settings') }}</a>
                @endif
            </div>

            <div class="pt-2 border-t border-zinc-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 cursor-pointer">
                        {{ __('common.navigation.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-2">
        @if (session('status'))
            <div class="mb-5 p-4 rounded-2xl bg-white border border-zinc-200 text-zinc-800 text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-[#FF6B1A] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Clean Minimal Footer -->
    <footer class="border-t border-black/[0.04] bg-white/60 py-5 text-xs text-zinc-500 mt-10">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-zinc-900">CargoTrace</span>
                <span>&bull; {{ __('common.brand_tagline') }}</span>
            </div>
            <div>
                {{ __('common.footer.academic_credit') }}
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (btn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
