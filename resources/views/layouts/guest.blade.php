<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('auth.sign_in_title')) - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.i18n = @json(__('js'));
    </script>
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-blue-600 selection:text-white">
    <!-- Subtle Background Gradient Accent -->
    <div class="fixed inset-0 pointer-events-none -z-10 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(37,99,235,0.06),rgba(255,255,255,0))]"></div>

    <!-- Header Navigation -->
    <header class="relative z-40 w-full px-6 py-5 border-b border-slate-200 bg-white/90 backdrop-blur-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Cargo<span class="text-blue-600">Trace</span></span>
                    <span class="hidden sm:block text-[10px] tracking-wider uppercase text-slate-500 font-medium">{{ __('welcome.tagline') }}</span>
                </div>
            </a>
            <div class="flex items-center gap-4">
                <!-- Language Switcher (ID | EN) -->
                <div class="relative z-20 pointer-events-auto inline-flex items-center rounded-xl bg-slate-100 p-0.5 border border-slate-200 text-xs font-semibold shrink-0" title="{{ __('common.labels.quick_switch') }}">
                    <a href="{{ route('locale.set', 'id') }}" class="px-2.5 py-1 rounded-lg transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'id' ? 'bg-white text-blue-600 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-900' }}" aria-label="Bahasa Indonesia">
                        ID
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" class="px-2.5 py-1 rounded-lg transition cursor-pointer select-none inline-flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-white text-blue-600 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-900' }}" aria-label="English">
                        EN
                    </a>
                </div>

                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('common.actions.back') }}
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">
            @if (session('status'))
                <div class="mb-5 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-start gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-2xs">
                    <p class="font-semibold mb-1">{{ __('auth.check_errors') }}</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="w-full py-4 text-center text-xs text-slate-500 border-t border-slate-200 bg-white">
        {{ __('common.footer.academic_project', ['year' => date('Y')]) }}
    </footer>
</body>
</html>
