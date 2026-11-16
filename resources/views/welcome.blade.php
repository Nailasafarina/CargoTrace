<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - Global Shipment Visibility & Tracking Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans antialiased bg-slate-950 text-slate-100 selection:bg-sky-500 selection:text-white">
    <!-- Background Gradient Accent -->
    <div class="fixed inset-0 pointer-events-none -z-10 bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(14,165,233,0.18),rgba(255,255,255,0))]"></div>

    <!-- Navigation -->
    <header class="w-full border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-sky-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-white">Cargo<span class="text-sky-400">Trace</span></span>
                    <span class="block text-[10px] tracking-wider uppercase text-slate-400 font-medium">Global Visibility Platform</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition">
                        Go to Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sky-500/10 border border-sky-500/30 text-sky-400 text-xs font-medium mb-6">
                <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                UAS Web Programming 2 &middot; Production-Grade Laravel 12 Architecture
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white max-w-4xl mx-auto leading-tight">
                End-to-End <span class="bg-gradient-to-r from-sky-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent">Shipment Visibility</span> with Real-Time Tracking
            </h1>
            <p class="mt-6 text-base sm:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Mission-critical freight tracking platform featuring dynamic ETA recalculation, route corridor visibility, automated exception triggers, and role-based operational governance.
            </p>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-sm font-semibold shadow-xl shadow-sky-500/25 transition">
                    Access Platform &rarr;
                </a>
                <a href="#demo-accounts" class="px-6 py-3 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 text-sm font-semibold transition">
                    View Demo Roles
                </a>
            </div>

            <!-- Demo Roles Cards -->
            <div id="demo-accounts" class="mt-16 pt-12 border-t border-slate-900 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <!-- Role: Admin -->
                <div class="p-6 rounded-2xl bg-slate-900/80 border border-purple-500/30 hover:border-purple-500/50 transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-500/10 text-purple-400 border border-purple-500/30">
                            Administrator
                        </span>
                        <span class="text-xs text-slate-500">Full System Scope</span>
                    </div>
                    <h3 class="text-base font-semibold text-white">Platform Governance</h3>
                    <p class="text-xs text-slate-400 mt-1 mb-4">Manage users, carriers, hubs, view global overview and compliance audit logs.</p>
                    <div class="p-3 bg-slate-950 rounded-xl text-xs space-y-1 font-mono text-slate-300">
                        <div>Email: <span class="text-purple-300">admin@cargotrace.test</span></div>
                        <div>Pass: <span class="text-slate-400">Password123!</span></div>
                    </div>
                </div>

                <!-- Role: Operator -->
                <div class="p-6 rounded-2xl bg-slate-900/80 border border-amber-500/30 hover:border-amber-500/50 transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/30">
                            Operator
                        </span>
                        <span class="text-xs text-slate-500">Dispatch & Milestones</span>
                    </div>
                    <h3 class="text-base font-semibold text-white">Logistics Operations</h3>
                    <p class="text-xs text-slate-400 mt-1 mb-4">Create shipments, record milestone waypoint events, and flag/resolve customs holds.</p>
                    <div class="p-3 bg-slate-950 rounded-xl text-xs space-y-1 font-mono text-slate-300">
                        <div>Email: <span class="text-amber-300">operator@cargotrace.test</span></div>
                        <div>Pass: <span class="text-slate-400">Password123!</span></div>
                    </div>
                </div>

                <!-- Role: Customer -->
                <div class="p-6 rounded-2xl bg-slate-900/80 border border-emerald-500/30 hover:border-emerald-500/50 transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            Customer
                        </span>
                        <span class="text-xs text-slate-500">Private Cargo Portal</span>
                    </div>
                    <h3 class="text-base font-semibold text-white">Customer Visibility</h3>
                    <p class="text-xs text-slate-400 mt-1 mb-4">Access only own shipments with real-time ETA, interactive Leaflet route map, and alerts.</p>
                    <div class="p-3 bg-slate-950 rounded-xl text-xs space-y-1 font-mono text-slate-300">
                        <div>Email: <span class="text-emerald-300">customer1@cargotrace.test</span></div>
                        <div>Pass: <span class="text-slate-400">Password123!</span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950 py-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-300">CargoTrace</span>
                <span>&bull; Global Shipment Visibility & Tracking Platform</span>
            </div>
            <div>
                Web Programming 2 &middot; Laravel 12 &middot; MySQL 8 &middot; Tailwind CSS
            </div>
        </div>
    </footer>
</body>
</html>
