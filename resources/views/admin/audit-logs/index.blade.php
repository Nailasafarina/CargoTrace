@extends('layouts.app')

@section('title', __('audit.title'))

@section('content')
@php
    $currentAction = request('action');
@endphp

<div class="space-y-5">
    <!-- 1. Page Header Card (Separated cleanly from Filter & Quick Tabs) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] p-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-2.5">
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#1A1718]">
                {{ __('audit.title') }}
            </h1>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FCECF1] text-[#BE2B5B] text-xs font-bold font-mono whitespace-nowrap">
                {{ __('audit.total_logged', ['total' => $auditLogs->total()]) }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FCECF1] text-[#BE2B5B] border border-[#EFE6EA] whitespace-nowrap">
                <span class="w-1.5 h-1.5 rounded-full bg-[#BE2B5B]"></span>
                {{ __('common.badges.admin_guarded') }}
            </span>
        </div>
        <p class="text-xs sm:text-sm text-[#6B6467] mt-1.5 leading-relaxed">
            {{ __('audit.subtitle') }}
        </p>
    </div>

    <!-- 2. Filter Form Card (5-Column Responsive Grid + Separate Bottom-Right Action Row) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1A1718] mb-1.5">{{ __('audit.actor') }}</label>
                    <select name="user_id" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('audit.all_actors') }}</option>
                        @foreach($actors as $actor)
                            <option value="{{ $actor->id }}" {{ (string) request('user_id') === (string) $actor->id ? 'selected' : '' }}>
                                {{ $actor->name }} ({{ \App\Enums\Role::tryFrom($actor->role)?->label() ?? $actor->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#1A1718] mb-1.5">{{ __('audit.action') }}</label>
                    <select name="action" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('audit.all_actions') }}</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ strtoupper($act) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#1A1718] mb-1.5">{{ __('audit.entity_type') }}</label>
                    <select name="entity_type" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('audit.all_entities') }}</option>
                        @foreach($entityTypes as $ent)
                            <option value="{{ $ent }}" {{ request('entity_type') === $ent ? 'selected' : '' }}>
                                {{ class_basename($ent) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#1A1718] mb-1.5">{{ __('audit.date_from') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#1A1718] mb-1.5">{{ __('audit.date_to') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition" />
                </div>
            </div>

            <!-- Separate Bottom-Right Action Row (Never Wraps Vertically) -->
            <div class="flex flex-wrap items-center justify-end gap-2.5 pt-3 border-t border-[#EFE6EA]">
                <a href="{{ route('admin.audit-logs.index') }}" class="h-10 px-4 rounded-xl bg-white hover:bg-[#FAF7F8] border border-[#E5DCE0] text-[#1A1718] text-xs font-semibold inline-flex items-center justify-center whitespace-nowrap transition">
                    {{ __('common.actions.clear_filters') }}
                </a>
                <button type="submit" class="h-10 px-5 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>{{ __('common.actions.filter') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Data Table Card (With Top Quick Action Tabs Bar Inside Card) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] shadow-sm overflow-hidden">
        <!-- Top Quick Action Filter Tabs (Clean Horizontal Flex Row without Outer Pill Bubble) -->
        <div class="px-5 py-3.5 border-b border-[#EFE6EA] flex flex-wrap items-center gap-2 bg-white">
            <a href="{{ route('admin.audit-logs.index') }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ !$currentAction ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('audit.all_actions') }}
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'created']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentAction === 'created' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                CREATED
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'updated']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentAction === 'updated' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                UPDATED
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'status_updated']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentAction === 'status_updated' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                STATUS_UPDATED
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'setting_updated']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentAction === 'setting_updated' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                SETTING_UPDATED
            </a>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1A1718]">
                <thead class="bg-[#FAF7F8] text-[#6B6467] uppercase tracking-wider text-xs font-semibold border-b border-[#EFE6EA]">
                    <tr>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('audit.table.timestamp') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('audit.table.actor') }}</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">{{ __('audit.table.action') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('audit.table.target_entity') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('audit.table.ip_address') }}</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">{{ __('audit.table.diff') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE6EA]">
                    @forelse($auditLogs as $log)
                        @php
                            $actionColors = [
                                'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'updated' => 'bg-[#FCECF1] text-[#BE2B5B] border-[#EFE6EA]',
                                'deleted' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'status_updated' => 'bg-[#BE2B5B] text-white border-[#BE2B5B]',
                                'exception_logged' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'exception_resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'setting_updated' => 'bg-[#FCECF1] text-[#BE2B5B] border-[#EFE6EA]',
                            ];
                            $badge = $actionColors[$log->action] ?? 'bg-[#FAF7F8] text-[#6B6467] border-[#EFE6EA]';
                        @endphp
                        <tr class="hover:bg-[#FAF7F8]/60 transition">
                            <td class="py-3.5 px-4 align-middle font-mono text-[11px] whitespace-nowrap">
                                <span class="text-[#1A1718] font-bold tabular-nums block">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                <span class="text-[#6B6467] block text-[10px] mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#FCECF1] text-[#BE2B5B] border border-[#EFE6EA] text-[11px] font-extrabold flex items-center justify-center shrink-0 uppercase">
                                            {{ substr($log->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-[#1A1718]">{{ $log->user->name }}</span>
                                                <span class="whitespace-nowrap px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-[#FAF7F8] text-[#6B6467] border border-[#EFE6EA]">
                                                    {{ \App\Enums\Role::tryFrom($log->user->role)?->label() ?? $log->user->role }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-[#6B6467] mt-0.5">
                                                {{ $log->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="italic text-[#6B6467]">{{ __('audit.system_actor') }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 align-middle text-center whitespace-nowrap">
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 align-middle font-mono text-xs whitespace-nowrap">
                                <span class="text-[#BE2B5B] font-bold">{{ class_basename($log->entity_type) }}</span>
                                <span class="text-[#6B6467]">#{{ $log->entity_id }}</span>
                            </td>

                            <td class="py-3.5 px-4 align-middle font-mono text-[11px] text-[#6B6467] whitespace-nowrap">
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 rounded-lg bg-[#FAF7F8] border border-[#EFE6EA]">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 align-middle text-right whitespace-nowrap">
                                @if(!empty($log->old_values) || !empty($log->new_values))
                                    <details class="group inline-block text-left">
                                        <summary class="list-none [&::-webkit-details-marker]:hidden inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-medium bg-[#FCECF1] text-[#BE2B5B] hover:bg-[#BE2B5B] hover:text-white transition cursor-pointer select-none">
                                            {!! __('audit.diff_modal.inspect_button') !!}
                                        </summary>
                                        <div class="fixed inset-0 bg-[#1A1718]/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 group-open:flex hidden" onclick="if(event.target === this) this.parentElement.removeAttribute('open');">
                                            <div class="bg-white border border-[#EFE6EA] rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto text-left whitespace-normal" onclick="event.stopPropagation();">
                                                <div class="flex items-center justify-between pb-3 border-b border-[#EFE6EA]">
                                                    <div>
                                                        <h3 class="text-sm font-bold text-[#1A1718] uppercase tracking-wider">
                                                            {{ __('audit.diff_modal.snapshot_title', ['entity' => class_basename($log->entity_type), 'id' => $log->entity_id]) }}
                                                        </h3>
                                                        <p class="text-xs text-[#6B6467] mt-0.5">{!! __('audit.diff_modal.snapshot_subtitle', ['action' => '<span class="font-mono text-[#BE2B5B] font-bold uppercase">' . $log->action . '</span>', 'time' => $log->created_at->format('Y-m-d H:i:s')]) !!}</p>
                                                    </div>
                                                    <button type="button" class="w-8 h-8 rounded-full text-[#6B6467] hover:text-[#1A1718] hover:bg-[#FAF7F8] flex items-center justify-center text-lg leading-none cursor-pointer transition" onclick="this.closest('details').removeAttribute('open');" aria-label="Close">
                                                        &times;
                                                    </button>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
                                                    <div class="space-y-1.5">
                                                        <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">{{ __('audit.diff_modal.previous_state') }}</span>
                                                        <pre class="p-3.5 bg-[#FAF7F8] border border-[#EFE6EA] rounded-xl text-[#1A1718] overflow-x-auto text-[11px]">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                    </div>

                                                    <div class="space-y-1.5">
                                                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">{{ __('audit.diff_modal.new_state') }}</span>
                                                        <pre class="p-3.5 bg-[#FCECF1]/50 border border-[#EFE6EA] rounded-xl text-[#1A1718] overflow-x-auto text-[11px]">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-[#6B6467] italic text-[11px]">{{ __('audit.diff_modal.no_state_change') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-[#6B6467]">
                                {{ __('audit.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditLogs->hasPages())
            <div class="px-5 py-4 border-t border-[#EFE6EA] bg-[#FAF7F8]/40">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
