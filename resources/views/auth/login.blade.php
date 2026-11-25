@extends('layouts.guest')

@section('title', __('auth.login_title'))

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('auth.login_title') }}</h1>
        <p class="text-xs text-slate-500 mt-1">{{ __('auth.login_subtitle') }}</p>
    </div>

    <!-- Quick Demo Accounts Selector (Vanilla JS Quick Fill) -->
    <div class="mb-6 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider">{{ __('auth.demo_accounts') }}</span>
            <span class="text-[10px] text-blue-600 font-medium">{{ __('auth.click_autofill') }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <button type="button" onclick="fillDemo('admin@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-semibold transition cursor-pointer text-center">
                {{ __('common.roles.admin') }}
            </button>
            <button type="button" onclick="fillDemo('operator@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold transition cursor-pointer text-center">
                {{ __('common.roles.operator') }}
            </button>
            <button type="button" onclick="fillDemo('customer1@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold transition cursor-pointer text-center">
                {{ __('common.roles.customer') }}
            </button>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('auth.email') }}</label>
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition shadow-2xs"
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
                <label for="password" class="block text-xs font-semibold text-slate-700">{{ __('auth.password_label') }}</label>
            </div>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition shadow-2xs"
                    placeholder="••••••••••••"
                />
            </div>
            @error('password')
                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20"
                />
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm hover:shadow transition duration-200 cursor-pointer"
            >
                {{ __('auth.sign_in_button') }}
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
        {{ __('auth.dont_have_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">{{ __('auth.sign_up_button') }}</a>
    </div>
</div>

<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
