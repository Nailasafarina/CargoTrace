<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ __('settings.title') }}</h2>
                <p class="text-xs text-slate-500 mt-1">{{ __('settings.subtitle') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-xs font-medium text-slate-700 shadow-sm transition">
                &larr; {{ __('common.actions.back') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-sm">
                <span>{{ session('status') }}</span>
                <span class="text-emerald-700 font-mono text-[10px]">CONFIG_UPDATED</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form Card -->
            <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ __('settings.card_title') }}</h3>
                        <p class="text-xs text-slate-500">{{ __('settings.card_subtitle') }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="delay_threshold_hours" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                            {{ __('settings.threshold_label') }}
                        </label>
                        <div class="relative max-w-xs">
                            <input type="number" id="delay_threshold_hours" name="delay_threshold_hours"
                                value="{{ old('delay_threshold_hours', $delayThreshold) }}"
                                min="1" max="168" required
                                class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 font-mono text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 focus:outline-none shadow-sm">
                            <span class="absolute right-4 top-2.5 text-xs text-slate-500 font-medium">{{ __('settings.hours_suffix') }}</span>
                        </div>
                        @error('delay_threshold_hours')
                            <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                        @enderror

                        <!-- Quick Preset Buttons -->
                        <div class="flex items-center space-x-2 mt-3">
                            <span class="text-[11px] text-slate-500 font-medium">{{ __('settings.group_presets') }}</span>
                            <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 12" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-mono font-medium transition cursor-pointer">
                                12h
                            </button>
                            <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 18" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-mono font-medium transition cursor-pointer">
                                18h
                            </button>
                            <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 24" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-xs font-mono font-medium transition cursor-pointer">
                                {{ __('settings.group_standard') }}
                            </button>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" name="re_evaluate_now" value="1" checked
                                class="mt-0.5 rounded border-slate-300 bg-white text-blue-600 focus:ring-blue-500/20">
                            <div>
                                <span class="text-xs font-semibold text-slate-800">{{ __('settings.re_evaluate_now') }}</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ __('settings.re_evaluate_desc') }}</p>
                            </div>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-sm transition cursor-pointer">
                            {{ __('settings.save_button') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reference Criteria Card -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('settings.scale_title') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ __('settings.scale_intro') }}
                </p>

                <div class="space-y-3 pt-2">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-yellow-50 text-yellow-800 border border-yellow-300">LOW</span>
                            <span class="text-xs font-mono text-slate-700 font-semibold">0 – 6 {{ __('settings.hours_suffix') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1">{{ __('settings.low_desc') }}</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-orange-50 text-orange-800 border border-orange-200">MEDIUM</span>
                            <span class="text-xs font-mono text-slate-700 font-semibold">&gt;6 – 24 {{ __('settings.hours_suffix') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1">{{ __('settings.medium_desc') }}</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200">HIGH</span>
                            <span class="text-xs font-mono text-slate-700 font-semibold">&gt;24 {{ __('settings.hours_suffix') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1">{{ __('settings.high_desc') }}</p>
                    </div>
                </div>

                <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <div class="text-[11px] text-blue-800 font-semibold uppercase tracking-wider mb-1">{{ __('settings.automation_title') }}</div>
                    <p class="text-[11px] text-slate-700">
                        {{ __('settings.automation_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
