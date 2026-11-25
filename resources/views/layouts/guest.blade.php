<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F6F3F5] text-zinc-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('auth.login_title')) - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.i18n = @json(__('js'));
    </script>
</head>
<body class="min-h-full font-sans antialiased bg-[#F6F3F5] text-zinc-800 flex flex-col justify-between selection:bg-[#E04879] selection:text-white">
    <!-- Header Navigation -->
    <header class="relative z-40 w-full px-6 py-4 bg-[#F6F3F5]/95 backdrop-blur-md">
        <div class="max-w-[1440px] mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0">
                <div class="h-10 w-10 rounded-xl bg-[#1F171B] flex items-center justify-center text-white shadow-sm group-hover:bg-[#C93565] transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="2.5" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3.5m0 11V21M5.64 5.64l2.47 2.47m7.78 7.78l2.47 2.47M3 12h3.5m11 0H21M5.64 18.36l2.47-2.47m7.78-7.78l2.47-2.47" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-[#1F171B]">CargoTrace</span>
                    <span class="hidden sm:block text-[10px] tracking-wider uppercase text-zinc-500 font-medium">{{ __('welcome.tagline') }}</span>
                </div>
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

                <a href="{{ route('home') }}" class="px-4 py-2 rounded-full bg-white hover:bg-[#FDF2F6] text-xs font-semibold text-zinc-700 shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('common.actions.back') }}
                </a>
            </div>
        </div>
    </header>

    <!-- Main 2-Column Studio Auth Container -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Studio Fleet Showcase -->
            <div class="hidden lg:block lg:col-span-6 nex-card p-6 space-y-5">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold">
                        {{ __('welcome.badge') }}
                    </span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#E04879]"></span>
                </div>

                <div class="relative rounded-2xl bg-[#FAF7F9] border border-[#ECE4E8] p-5 flex items-center justify-between gap-4">
                    <img src="{{ asset('images/scania-cab.png') }}" alt="CargoTrace transport tractor" class="w-48 h-auto object-contain shrink-0 select-none drop-shadow-[0_10px_14px_rgba(31,23,27,0.1)]" />
                    <div class="cargo-bay-hatch flex-1 rounded-2xl p-3.5 border-[1.5px] border-[#E04879] relative overflow-hidden space-y-2">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-[#9E224B]">BAY A9</span>
                            <span class="w-2 h-2 rounded-full bg-[#E04879]"></span>
                        </div>
                        <img src="{{ asset('images/cargo-boxes.png') }}" alt="Cargo units" class="w-14 h-12 object-contain mx-auto select-none" />
                        <div class="text-xs font-bold text-[#1F171B] text-center font-mono">CT-LIVE-OSM</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-[#1F171B]">
                        <span>Singapore PSA</span>
                        <span>Port of Rotterdam</span>
                    </div>
                    <div class="h-2.5 rounded-full route-track-hatch overflow-hidden">
                        <div class="h-full bg-[#E04879] rounded-full w-2/3"></div>
                    </div>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="lg:col-span-6 w-full max-w-md mx-auto">
                @if (session('status'))
                    <div class="mb-5 p-4 rounded-2xl bg-[#FDF2F6] border border-[#F8C9DC] text-zinc-800 text-sm flex items-start gap-3 shadow-2xs">
                        <svg class="w-5 h-5 text-[#E04879] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-2xs">
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
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="w-full py-4 text-center text-xs text-zinc-500 border-t border-[#1F171B]/[0.05] bg-white/70">
        {{ __('common.footer.academic_project', ['year' => date('Y')]) }}
    </footer>
</body>
</html>
