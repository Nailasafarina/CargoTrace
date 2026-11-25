<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAF3F6] text-[#4A2C35]">
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
<body class="min-h-screen font-sans antialiased bg-[#FAF3F6] text-[#4A2C35] selection:bg-[#BE2B5B] selection:text-white">
    @php
        $isDashboardActive = request()->routeIs('admin.dashboard', 'operator.dashboard', 'customer.dashboard', 'dashboard');
        $dashboardRoute = Auth::user()->isAdmin()
            ? route('admin.dashboard')
            : (Auth::user()->isOperator() ? route('operator.dashboard') : route('customer.dashboard'));
        $navUnreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
    @endphp

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Sidebar Navigation (Desktop lg+) -->
        <aside class="hidden lg:flex lg:w-60 xl:w-64 shrink-0 bg-white border-r border-[#EFE4E8] flex-col justify-between sticky top-0 h-screen z-40 select-none">
            <!-- Top Brand + Vertical Navigation Links -->
            <div class="p-6 space-y-7 overflow-y-auto">
                <!-- Brand Logo (Soft Rose Rounded Square + CargoTrace) -->
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3.5 px-2 group">
                    <div class="h-10 w-10 rounded-xl bg-[#E487A5] group-hover:bg-[#BE2B5B] flex items-center justify-center text-white shadow-2xs transition-colors duration-200 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-[#4A2C35]">CargoTrace</span>
                </a>

                <!-- Vertical Sidebar Buttons (Matching Reference Screenshot) -->
                <nav class="space-y-1.5 text-sm" aria-label="Primary Sidebar Navigation">
                    <a
                        href="{{ $dashboardRoute }}"
                        class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ $isDashboardActive ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                    >
                        <span>{{ __('common.navigation.dashboard') }}</span>
                    </a>

                    <a
                        href="{{ route('shipments.index') }}"
                        class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ request()->routeIs('shipments.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                    >
                        <span>{{ __('common.navigation.shipments') }}</span>
                    </a>

                    @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                        <a
                            href="{{ route('carriers.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ request()->routeIs('carriers.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                        >
                            <span>{{ __('common.navigation.carriers') }}</span>
                        </a>

                        <a
                            href="{{ route('locations.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ request()->routeIs('locations.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                        >
                            <span>{{ __('common.navigation.locations') }}</span>
                        </a>
                    @endif

                    <a
                        href="{{ route('notifications.index') }}"
                        class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ request()->routeIs('notifications.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                    >
                        <span>{{ __('common.navigation.notifications') }}</span>
                        @if($navUnreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-[#BE2B5B] text-white text-[11px] font-bold">
                                {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                            </span>
                        @endif
                    </a>

                    @if (Auth::user()->isAdmin())
                        <a
                            href="{{ route('admin.audit-logs.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                        >
                            <span>{{ __('common.navigation.audit_logs') }}</span>
                        </a>

                        <a
                            href="{{ route('admin.settings.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.settings.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold shadow-2xs' : 'text-[#8A6D76] hover:text-[#4A2C35] hover:bg-[#FAF3F6] font-medium' }}"
                        >
                            <span>{{ __('common.navigation.settings') }}</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Bottom Sidebar Controls: Language Switcher + User Profile + Logout -->
            <div class="p-4 border-t border-[#EFE4E8] space-y-3 bg-[#FAF6F8]/60">
                <!-- Language Switcher (ID | EN) -->
                <div class="flex items-center justify-between px-2">
                    <span class="text-[11px] font-semibold text-[#8A6D76]">{{ __('common.labels.quick_switch') }}</span>
                    <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-full bg-white border border-[#EFE4E8] p-0.5 text-[11px] font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                        <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-0.5 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] hover:text-[#4A2C35]' }}" aria-label="Bahasa Indonesia">
                            ID
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-0.5 rounded-full transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] hover:text-[#4A2C35]' }}" aria-label="English">
                            EN
                        </a>
                    </div>
                </div>

                <!-- User Profile Card & Logout (With Generous Vertical Padding & Leading so Descenders Never Clip) -->
                <div class="flex items-center justify-between gap-2.5 bg-white border border-[#EFE6EA] rounded-2xl px-3 py-3 shadow-2xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-[#FCECF1] text-[#BE2B5B] border border-[#F3D5DF] flex items-center justify-center text-xs font-extrabold uppercase shrink-0" title="{{ Auth::user()->name }} (role: {{ Auth::user()->role }})">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="min-w-0 py-0.5">
                            <div class="text-xs font-bold text-[#1A1718] truncate leading-normal">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-[#6B6467] font-mono truncate leading-normal mt-0.5">
                                role: {{ Auth::user()->role }}
                            </div>
                        </div>
                        <span class="sr-only">role: {{ Auth::user()->role }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-[#6B6467] hover:text-[#BE2B5B] hover:bg-[#FCECF1] transition cursor-pointer" title="{{ __('common.navigation.logout') }}" aria-label="{{ __('common.navigation.logout') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Top Bar (< lg) -->
        <header class="lg:hidden sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-[#EFE4E8]">
            <div class="px-4 h-16 flex items-center justify-between gap-3">
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-[#E487A5] flex items-center justify-center text-white shadow-2xs">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight text-[#4A2C35]">CargoTrace</span>
                </a>

                <div class="flex items-center gap-2">
                    <div class="relative z-20 pointer-events-auto flex items-center rounded-full bg-[#FAF6F8] border border-[#EFE4E8] p-1 text-xs font-semibold shrink-0">
                        <a href="{{ route('locale.set', 'id') }}" class="px-2 py-0.5 rounded-full transition {{ app()->getLocale() === 'id' ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76]' }}" aria-label="Bahasa Indonesia">ID</a>
                        <a href="{{ route('locale.set', 'en') }}" class="px-2 py-0.5 rounded-full transition {{ app()->getLocale() === 'en' ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76]' }}" aria-label="English">EN</a>
                    </div>
                    <button type="button" id="mobile-menu-btn" class="p-2 rounded-xl bg-[#FAF6F8] border border-[#EFE4E8] text-[#4A2C35] focus:outline-none" aria-label="Toggle Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Drawer Dropdown -->
            <div id="mobile-menu" class="hidden border-t border-[#EFE4E8] px-4 pt-3 pb-4 space-y-2 bg-white shadow-lg">
                <div class="pb-2 border-b border-[#EFE4E8]">
                    <div class="text-sm font-bold text-[#4A2C35]">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-[#8A6D76]">{{ Auth::user()->email }} &bull; role: {{ Auth::user()->role }}</div>
                </div>
                <div class="space-y-1">
                    <a href="{{ $dashboardRoute }}" class="block px-3.5 py-2.5 rounded-xl text-sm {{ $isDashboardActive ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">{{ __('common.navigation.dashboard') }}</a>
                    <a href="{{ route('shipments.index') }}" class="block px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('shipments.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">{{ __('common.navigation.shipments') }}</a>
                    @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                        <a href="{{ route('carriers.index') }}" class="block px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('carriers.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">{{ __('common.navigation.carriers') }}</a>
                        <a href="{{ route('locations.index') }}" class="block px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('locations.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">{{ __('common.navigation.locations') }}</a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('notifications.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">
                        <span>{{ __('common.navigation.notifications') }}</span>
                        @if($navUnreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#BE2B5B] text-white">{{ $navUnreadCount }}</span>
                        @endif
                    </a>
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.audit-logs.index') }}" class="block px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">{{ __('common.navigation.audit_logs') }}</a>
                        <a href="{{ route('admin.settings.index') }}" class="block px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-[#F7C9D9] text-[#4A2C35] font-bold' : 'text-[#8A6D76] font-medium hover:bg-[#FAF3F6]' }}">{{ __('common.navigation.settings') }}</a>
                    @endif
                </div>
                <div class="pt-2 border-t border-[#EFE4E8]">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl text-sm font-semibold text-[#BE2B5B] hover:bg-[#FCECF1] cursor-pointer">
                            {{ __('common.navigation.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Right Main Content Area -->
        <div class="flex-1 min-w-0 flex flex-col justify-between">
            <main class="flex-grow max-w-[1320px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @if (isset($header))
                    <div class="mb-5">
                        {{ $header }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-5 p-4 rounded-2xl bg-white border border-[#EFE4E8] text-[#4A2C35] text-sm flex items-start gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-[#BE2B5B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Clean Minimal Footer -->
            <footer class="border-t border-[#EFE4E8] bg-white/70 py-4 text-xs text-[#826871] mt-8">
                <div class="max-w-[1320px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#E487A5]"></span>
                        <span class="font-bold text-[#4A2C35]">CargoTrace</span>
                        <span>&bull; {{ __('common.brand_tagline') }}</span>
                    </div>
                    <div>
                        &copy; {{ date('Y') }} CargoTrace
                    </div>
                </div>
            </footer>
        </div>
    </div>

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
