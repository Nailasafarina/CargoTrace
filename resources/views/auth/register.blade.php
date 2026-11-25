@extends('layouts.guest')

@section('title', __('auth.register_title'))

@section('content')
<div class="nex-card p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-[#1F171B] tracking-tight">{{ __('auth.register_title') }}</h1>
        <p class="text-xs text-zinc-500 mt-1">{{ __('auth.register_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="nex-input text-sm py-2.5" placeholder="e.g. PT Global Maritime" />
            @error('name')
                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="nex-input text-sm py-2.5" placeholder="name@company.com" />
            @error('email')
                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.password_label') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="nex-input text-sm py-2.5" placeholder="Minimum 8 characters" />
            @error('password')
                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('auth.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="nex-input text-sm py-2.5" placeholder="Re-enter password" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-sm font-semibold shadow-sm transition duration-200 cursor-pointer">
                {{ __('auth.sign_up_button') }}
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-[#F5F0F3] text-center text-xs text-zinc-500">
        {{ __('auth.already_registered') }}
        <a href="{{ route('login') }}" class="font-semibold text-[#C93565] hover:underline transition">{{ __('auth.sign_in_button') }}</a>
    </div>
</div>
@endsection
