<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F6F3F5] text-zinc-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('errors.422.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-6 bg-[#F6F3F5] text-zinc-800 selection:bg-[#E04879] selection:text-white">
    <div class="max-w-md w-full p-8 rounded-[24px] bg-white border border-[#F8C9DC] text-center shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] mx-auto flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">
            {{ __('errors.422.badge') }}
        </span>
        <h1 class="text-xl font-bold text-[#1F171B] mt-3">{{ __('errors.422.heading') }}</h1>
        <p class="text-xs text-zinc-600 mt-2 leading-relaxed">
            {{ $exception?->getMessage() ?? __('errors.422.default_message') }}
        </p>
        <div class="mt-6">
            <a href="javascript:history.back()" class="inline-block px-5 py-2.5 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-xs font-semibold text-white transition">
                &larr; {{ __('errors.422.back') }}
            </a>
        </div>
    </div>
</body>
</html>
