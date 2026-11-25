@extends('layouts.guest')

@section('title', __('auth.login_title'))

@section('content')
<div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 tracking-tight">{{ __('auth.login_title') }}</h1>
        <p class="text-xs text-zinc-500 mt-1">{{ __('auth.login_subtitle') }}</p>
    </div>

    <!-- Quick Demo Accounts Selector -->
    <div class="mb-6 p-3.5 bg-[#F2F3F5] rounded-2xl">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-zinc-600">{{ __('auth.demo_accounts') }}</span>
            <span class="text-[10px] text-[#FF6B1A] font-semibold">{{ __('auth.click_autofill') }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <button type="button" onclick="fillDemo('admin@cargotrace.test', 'Password123!')" class="px-2 py-2 rounded-full bg-[#18181B] hover:bg-zinc-800 text-white text-xs font-semibold transition cursor-pointer text-center">
                {{ __('common.roles.admin') }}
            </button>
            <button type="button" onclick="fillDemo('operator@cargotrace.test', 'Password123!')" class="px-2 py-2 rounded-full bg-[#FF6B1A] hover:bg-[#e5560a] text-white text-xs font-semibold transition cursor-pointer text-center">
                {{ __('common.roles.operator') }}
            </button>
            <button type="button" onclick="fillDemo('customer1@cargotrace.test', 'Password123!')" class="px-2 py-2 rounded-full bg-white hover:bg-zinc-50 text-zinc-800 border border-zinc-200 text-xs font-semibold transition cursor-pointer text-center">
                {{ __('common.roles.customer') }}
            </button>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.email') }}</label>
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full px-4 py-2.5 bg-[#F7F8FA] border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#FF6B1A]/20 focus:border-[#FF6B1A] transition"
                    placeholder="name@cargotrace.test"
                />
            </div>
            @error('email')
                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-zinc-700">{{ __('auth.password_label') }}</label>
            </div>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-2.5 bg-[#F7F8FA] border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#FF6B1A]/20 focus:border-[#FF6B1A] transition"
                    placeholder="••••••••••••"
                />
            </div>
            @error('password')
                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-zinc-600">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-zinc-300 text-[#FF6B1A] focus:ring-[#FF6B1A]/20"
                />
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-3 px-4 rounded-full bg-[#18181B] hover:bg-[#FF6B1A] text-white text-sm font-semibold shadow-sm transition duration-200 cursor-pointer"
            >
                {{ __('auth.sign_in_button') }}
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-zinc-100 text-center text-xs text-zinc-500">
        {{ __('auth.dont_have_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-[#FF6B1A] hover:underline transition">{{ __('auth.sign_up_button') }}</a>
    </div>
</div>

<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
