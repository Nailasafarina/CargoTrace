<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-950 text-slate-100 selection:bg-sky-500 selection:text-white">
    <!-- Background Gradient Accent -->
    <div class="fixed inset-0 pointer-events-none -z-10 bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,rgba(14,165,233,0.12),rgba(255,255,255,0))]"></div>

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo & Role Badge -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight text-white">Cargo<span class="text-sky-400">Trace</span></span>
                    </a>

                    <!-- Desktop Nav Links based on Role -->
                    <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Dashboard
                            </a>
                        @elseif (Auth::user()->isOperator())
                            <a href="{{ route('operator.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('operator.dashboard') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('customer.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('customer.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Dashboard
                            </a>
                        @endif

                        <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('shipments.*') ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                            Shipments
                        </a>

                        @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                            <a href="{{ route('carriers.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('carriers.*') ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Carriers
                            </a>
                            <a href="{{ route('locations.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('locations.*') ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Locations
                            </a>
                        @endif

                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.audit-logs.*') ? 'bg-purple-500/10 text-purple-400 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Audit Logs
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-purple-500/10 text-purple-400 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                                Settings
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right: User Info & Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Notification Bell -->
                    @php
                        $navUnreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Notification Center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($navUnreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm shadow-rose-500/50 animate-pulse">
                                {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                            </span>
                        @endif
                    </a>
                    <!-- Role Pill -->
                    @php
                        $roleColors = [
                            'admin' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                            'operator' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                            'customer' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                        ];
                        $badgeColor = $roleColors[Auth::user()->role] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border {{ $badgeColor }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-purple-400' : (Auth::user()->isOperator() ? 'bg-amber-400' : 'bg-emerald-400') }}"></span>
                        {{ Auth::user()->role }}
                    </span>

                    <!-- User Name & Email -->
                    <div class="text-right">
                        <div class="text-sm font-semibold text-white leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 leading-tight">{{ Auth::user()->email }}</div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800/80 text-xs font-medium text-slate-300 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/40 transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>

                <!-- Mobile menu button -->
                <div class="flex md:hidden">
                    <button type="button" id="mobile-menu-btn" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu dropdown -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 px-4 pt-3 pb-4 space-y-3 bg-slate-900">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <div>
                    <div class="text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $badgeColor }}">
                    {{ Auth::user()->role }}
                </span>
            </div>

            <div class="space-y-1">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-slate-800">Admin Console</a>
                @elseif (Auth::user()->isOperator())
                    <a href="{{ route('operator.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-slate-800">Operations Control</a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-slate-800">Customer Portal</a>
                @endif
                <a href="{{ route('shipments.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800">Shipments</a>
                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Notifications
                    </span>
                    @if($navUnreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white">
                            {{ $navUnreadCount }}
                        </span>
                    @endif
                </a>
                @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                    <a href="{{ route('carriers.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800">Carriers</a>
                    <a href="{{ route('locations.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800">Locations</a>
                @endif
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.audit-logs.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-purple-400 hover:text-white hover:bg-slate-800">Audit Logs</a>
                    <a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-purple-400 hover:text-white hover:bg-slate-800">Settings</a>
                @endif
            </div>

            <div class="pt-2 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-rose-400 hover:bg-rose-500/10">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-sky-500/10 border border-sky-500/30 text-sky-300 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-sky-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950/80 py-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-300">CargoTrace</span>
                <span>&bull; Global Shipment Visibility & Tracking Platform</span>
            </div>
            <div>
                Built for UAS Web Programming 2 &middot; Laravel 12 &amp; Tailwind CSS
            </div>
        </div>
    </footer>

    <!-- Vanilla JS for Mobile Menu Toggle -->
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
