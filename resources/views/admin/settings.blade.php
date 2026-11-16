<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white tracking-tight">System Configuration & Parameters</h2>
                <p class="text-xs text-slate-400 mt-1">Manage global operational thresholds and dynamic ETA delay criteria</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300 transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <span class="text-emerald-500 font-mono text-[10px]">CONFIG_UPDATED</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form Card -->
            <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Delay Threshold Settings</h3>
                        <p class="text-xs text-slate-400">Configure tolerance hours before ETA delays trigger operational alerts</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="delay_threshold_hours" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                            Delay Alert Threshold (Hours) *
                        </label>
                        <div class="relative max-w-xs">
                            <input type="number" id="delay_threshold_hours" name="delay_threshold_hours"
                                value="{{ old('delay_threshold_hours', $delayThreshold) }}"
                                min="1" max="168" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <span class="absolute right-4 top-2.5 text-xs text-slate-500 font-medium">Hours</span>
                        </div>
                        @error('delay_threshold_hours')
                            <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                        @enderror

                        <!-- Quick Preset Buttons -->
                        <div class="flex items-center space-x-2 mt-3">
                            <span class="text-[11px] text-slate-500 font-medium">Group Presets:</span>
                            <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 12" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono font-medium transition">
                                12h
                            </button>
                            <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 18" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono font-medium transition">
                                18h
                            </button>
                            <button type="button" onclick="document.getElementById('delay_threshold_hours').value = 24" class="px-2.5 py-1 rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-400 text-xs font-mono font-medium transition">
                                24h (Group Standard)
                            </button>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" name="re_evaluate_now" value="1" checked
                                class="mt-0.5 rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500/20">
                            <div>
                                <span class="text-xs font-semibold text-slate-200">Re-evaluate all active shipments immediately</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">Applies the new threshold across all non-delivered corridor shipments right after saving.</p>
                            </div>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex items-center justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs shadow-lg shadow-blue-600/20 transition">
                            Save & Apply Configuration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reference Criteria Card -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">Delay Severity Scale</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Once a delay surpasses the active threshold, it is automatically categorized according to operational SLA standards:
                </p>

                <div class="space-y-3 pt-2">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30">LOW</span>
                            <span class="text-xs font-mono text-slate-300">0 – 6 Hours</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Minor schedule variation; port turn-around buffer.</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">MEDIUM</span>
                            <span class="text-xs font-mono text-slate-300">>6 – 24 Hours</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Requires transshipment monitoring and customer advisory.</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30 animate-pulse">HIGH</span>
                            <span class="text-xs font-mono text-slate-300">>24 Hours</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Major disruption; immediate operations escalation.</p>
                    </div>
                </div>

                <div class="mt-6 p-4 rounded-xl bg-blue-500/5 border border-blue-500/20">
                    <div class="text-[11px] text-blue-400 font-semibold uppercase tracking-wider mb-1">Scheduled Automation</div>
                    <p class="text-[11px] text-slate-400">
                        The Artisan command <code class="text-blue-300 font-mono">shipments:evaluate-delays</code> is registered to run on an hourly schedule to audit active vessel transits.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
