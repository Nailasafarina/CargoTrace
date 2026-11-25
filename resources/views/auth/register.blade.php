@extends('layouts.guest')

@section('title', __('auth.register_title'))

@section('content')
<div class="bg-white rounded-[28px] border border-[#EFE6EA] p-6 sm:p-8 shadow-[0_12px_30px_-10px_rgba(26,23,24,0.05)]">
    <div class="mb-5">
        <h1 class="text-2xl sm:text-[26px] font-extrabold text-[#4A2C35] tracking-tight">{{ __('auth.register_title') }}</h1>
        <p class="text-xs sm:text-sm text-[#826871] mt-1 leading-relaxed">{{ __('auth.register_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold text-[#4A2C35] mb-1.5">{{ __('auth.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-xl bg-white border border-[#E5DCE0] px-3.5 py-2.5 text-sm text-[#4A2C35] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition" placeholder="e.g. PT Global Maritime" />
            @error('name')
                <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-bold text-[#4A2C35] mb-1.5">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-xl bg-white border border-[#E5DCE0] px-3.5 py-2.5 text-sm text-[#4A2C35] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition" placeholder="name@company.com" />
            @error('email')
                <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-[#4A2C35] mb-1.5">{{ __('auth.password_label') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-xl bg-white border border-[#E5DCE0] px-3.5 py-2.5 text-sm text-[#4A2C35] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition" placeholder="Minimum 8 characters" />
            @error('password')
                <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-[#4A2C35] mb-1.5">{{ __('auth.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-xl bg-white border border-[#E5DCE0] px-3.5 py-2.5 text-sm text-[#4A2C35] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition" placeholder="Re-enter password" />
        </div>

        <div class="pt-1.5">
            <button type="submit" class="w-full py-3 px-5 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-sm font-bold shadow-sm transition duration-200 cursor-pointer inline-flex items-center justify-center gap-2">
                <span>{{ __('auth.sign_up_button') }}</span>
                <span aria-hidden="true">&rarr;</span>
            </button>
        </div>
    </form>

    <div class="mt-5 pt-4 border-t border-[#EFE6EA] text-center text-xs text-[#826871]">
        {{ __('auth.already_registered') }}
        <a href="{{ route('login') }}" class="font-bold text-[#BE2B5B] hover:text-[#A0224B] hover:underline transition ml-0.5">{{ __('auth.sign_in_button') }}</a>
    </div>
</div>
@endsection
