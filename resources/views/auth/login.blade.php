@extends('layouts.guest')

@section('title', __('auth.login_title'))

@section('content')
<div class="nex-card p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-[#1F171B] tracking-tight">{{ __('auth.login_title') }}</h1>
        <p class="text-xs text-zinc-500 mt-1">{{ __('auth.login_subtitle') }}</p>
    </div>

    <!-- Quick Demo Accounts Selector -->
    <div class="mb-6 p-3.5 bg-[#FAF7F9] border border-[#ECE4E8] rounded-2xl">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-zinc-600">{{ __('auth.demo_accounts') }}</span>
            <span class="text-[10px] text-[#C93565] font-semibold">{{ __('auth.click_autofill') }}</span>
        </div>
        <div class="grid grid-cols-3 gap-1.5">
            <button type="button" onclick="fillDemo('admin@cargotrace.test', 'Password123!')" class="px-2 py-2 rounded-full bg-[#1F171B] hover:bg-zinc-800 text-white text-[11px] font-semibold transition cursor-pointer text-center whitespace-nowrap truncate" title="{{ __('common.roles.admin') }}">
                {{ __('common.roles.admin') }}
            </button>
            <button type="button" onclick="fillDemo('operator@cargotrace.test', 'Password123!')" class="px-2 py-2 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-[11px] font-semibold transition cursor-pointer text-center whitespace-nowrap truncate" title="{{ __('common.roles.operator') }}">
                {{ __('common.roles.operator') }}
            </button>
            <button type="button" onclick="fillDemo('customer1@cargotrace.test', 'Password123!')" class="px-2 py-2 rounded-full bg-white hover:bg-[#FDF2F6] text-zinc-800 border border-[#ECE4E8] text-[11px] font-semibold transition cursor-pointer text-center whitespace-nowrap truncate" title="{{ __('common.roles.customer') }}">
                {{ __('common.roles.customer') }}
            </button>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="nex-input text-sm py-2.5"
                placeholder="name@cargotrace.test"
            />
            @error('email')
                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.password_label') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="nex-input text-sm py-2.5"
                placeholder="••••••••••••"
            />
            @error('password')
                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-zinc-600">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-[#E6DCE1] text-[#C93565] focus:ring-[#E04879]/20"
                />
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-3 px-4 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-sm font-semibold shadow-sm transition duration-200 cursor-pointer"
            >
                {{ __('auth.sign_in_button') }}
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-[#F5F0F3] text-center text-xs text-zinc-500">
        {{ __('auth.dont_have_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-[#C93565] hover:underline transition">{{ __('auth.sign_up_button') }}</a>
    </div>
</div>

<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
