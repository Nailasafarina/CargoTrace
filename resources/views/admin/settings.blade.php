@extends('layouts.app')

@section('title', __('settings.title'))

@section('content')
<div class="space-y-6">
    <!-- Top Identity & Navigation Bar -->
    <div class="nex-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#1F171B] tracking-tight">{{ __('settings.title') }}</h1>
                <p class="text-xs text-zinc-500 mt-0.5">{{ __('settings.subtitle') }}</p>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-full bg-[#FAF7F9] border border-[#ECE4E8] hover:bg-[#FDF2F6] text-xs font-semibold text-zinc-700 transition self-start sm:self-auto">
            &larr; {{ __('common.actions.back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Form Studio Card -->
        <div class="lg:col-span-7 nex-card p-6 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#F5F0F3]">
                <div>
                    <h2 class="text-base font-bold text-zinc-900">{{ __('settings.card_title') }}</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('settings.card_subtitle') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-[#1F171B] text-white text-[10px] font-mono font-semibold">
                    {{ $delayThreshold }} {{ __('settings.hours_suffix') }}
                </span>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="delay_threshold_hours" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-2">
                        {{ __('settings.threshold_label') }}
                    </label>
                    <div class="relative max-w-xs">
                        <input type="number" id="delay_threshold_hours" name="delay_threshold_hours"
                            value="{{ old('delay_threshold_hours', $delayThreshold) }}"
                            min="1" max="168" required
                            class="nex-input font-mono text-sm pr-16 py-2.5">
                        <span class="absolute right-4 top-2.5 text-xs text-zinc-500 font-medium">{{ __('settings.hours_suffix') }}</span>
                    </div>
                    @error('delay_threshold_hours')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror

                    <!-- Quick Preset Buttons -->
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="text-[11px] text-zinc-500 font-medium">{{ __('settings.group_presets') }}</span>
                        <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 12" class="px-3 py-1 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 text-xs font-mono font-medium transition cursor-pointer">
                            12h
                        </button>
                        <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 18" class="px-3 py-1 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 text-xs font-mono font-medium transition cursor-pointer">
                            18h
                        </button>
                        <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 24" class="px-3 py-1 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] border border-[#F8C9DC] text-[#9E224B] text-xs font-mono font-medium transition cursor-pointer">
                            {{ __('settings.group_standard') }}
                        </button>
                    </div>
                </div>

                <div class="nex-inset p-4">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" name="re_evaluate_now" value="1" checked
                            class="mt-0.5 rounded border-[#E6DCE1] bg-white text-[#C93565] focus:ring-[#E04879]/20">
                        <div>
                            <span class="text-xs font-semibold text-zinc-800">{{ __('settings.re_evaluate_now') }}</span>
                            <p class="text-[11px] text-zinc-500 mt-0.5">{{ __('settings.re_evaluate_desc') }}</p>
                        </div>
                    </label>
                </div>

                <div class="pt-4 border-t border-[#F5F0F3] flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white font-semibold text-xs shadow-sm transition cursor-pointer">
                        {{ __('settings.save_button') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Reference Criteria Studio Card -->
        <div class="lg:col-span-5 nex-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">{{ __('settings.scale_title') }}</h2>
                <span class="w-2.5 h-2.5 rounded-full bg-[#E04879]"></span>
            </div>
            <p class="text-xs text-zinc-600 leading-relaxed">
                {{ __('settings.scale_intro') }}
            </p>

            <!-- Visual Corridor SLA Hatched Bar -->
            <div class="h-2.5 rounded-full route-track-hatch overflow-hidden flex">
                <div class="h-full bg-amber-400 w-1/4"></div>
                <div class="h-full bg-[#E04879] w-1/2"></div>
                <div class="h-full bg-red-600 w-1/4"></div>
            </div>

            <div class="space-y-3 pt-1">
                <div class="nex-inset p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">LOW</span>
                        <span class="text-xs font-mono text-zinc-700 font-semibold">0 – 6 {{ __('settings.hours_suffix') }}</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-1.5">{{ __('settings.low_desc') }}</p>
                </div>

                <div class="nex-inset p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">MEDIUM</span>
                        <span class="text-xs font-mono text-zinc-700 font-semibold">&gt;6 – 24 {{ __('settings.hours_suffix') }}</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-1.5">{{ __('settings.medium_desc') }}</p>
                </div>

                <div class="nex-inset p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-red-50 text-red-700 border border-red-200">HIGH</span>
                        <span class="text-xs font-mono text-zinc-700 font-semibold">&gt;24 {{ __('settings.hours_suffix') }}</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-1.5">{{ __('settings.high_desc') }}</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-[#FDF2F6] border border-[#F8C9DC]">
                <div class="text-[11px] text-[#9E224B] font-semibold uppercase tracking-wider mb-1">{{ __('settings.automation_title') }}</div>
                <p class="text-[11px] text-zinc-700 leading-relaxed">
                    {{ __('settings.automation_desc') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
