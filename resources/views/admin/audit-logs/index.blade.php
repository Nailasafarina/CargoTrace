@extends('layouts.app')

@section('title', __('audit.title'))

@section('content')
<div class="space-y-4">
    <!-- Top Tactile Mode Strip (NexCargo Command Pattern) -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @php $currentAction = request('action'); @endphp
            <a href="{{ route('admin.audit-logs.index') }}" class="w-9 h-9 rounded-full flex items-center justify-center shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition hover:scale-105 {{ !$currentAction ? 'bg-[#C93565] text-white shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6]' }}" title="{{ __('audit.all_actions') }}" aria-label="{{ __('audit.all_actions') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'created']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $currentAction === 'created' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                CREATED
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'updated']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $currentAction === 'updated' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                UPDATED
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'status_updated']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $currentAction === 'status_updated' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                STATUS_UPDATED
            </a>
            <a href="{{ route('admin.audit-logs.index', ['action' => 'setting_updated']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $currentAction === 'setting_updated' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                SETTING_UPDATED
            </a>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#1F171B] text-white shadow-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-[#E04879]"></span>
            {{ __('common.badges.admin_guarded') }}
        </span>
    </div>

    <!-- NexCargo Asymmetric 12-Col Bento Stage -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        <!-- Left Sticky Control & Filter Rail -->
        <aside class="xl:col-span-3 xl:sticky xl:top-20 space-y-3">
            <!-- Identity Card -->
            <div class="nex-card p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-[#FAF7F9] border border-[#ECE4E8] font-mono text-[11px] font-semibold text-zinc-700">
                        {{ __('audit.total_logged', ['total' => $auditLogs->total()]) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-[#1F171B] leading-snug">{{ __('audit.title') }}</h1>
                    <p class="text-xs text-zinc-500 mt-1 leading-relaxed">{{ __('audit.subtitle') }}</p>
                </div>
            </div>

            <!-- Filter Command Card -->
            <div class="nex-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-[#F5F0F3]">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-[#FAF7F9] text-[#C93565] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </span>
                        <span class="text-xs font-bold text-zinc-900">{{ __('audit.filters_heading') }}</span>
                    </div>
                    @if(request()->hasAny(['user_id', 'action', 'entity_type', 'date_from', 'date_to']))
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-[11px] text-[#C93565] hover:underline font-semibold">
                            {{ __('common.actions.clear_filters') }}
                        </a>
                    @endif
                </div>

                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('audit.actor') }}</label>
                        <select name="user_id" class="nex-input">
                            <option value="">{{ __('audit.all_actors') }}</option>
                            @foreach($actors as $actor)
                                <option value="{{ $actor->id }}" {{ (string) request('user_id') === (string) $actor->id ? 'selected' : '' }}>
                                    {{ $actor->name }} ({{ \App\Enums\Role::tryFrom($actor->role)?->label() ?? $actor->role }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('audit.action') }}</label>
                        <select name="action" class="nex-input">
                            <option value="">{{ __('audit.all_actions') }}</option>
                            @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                    {{ strtoupper($act) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('audit.entity_type') }}</label>
                        <select name="entity_type" class="nex-input">
                            <option value="">{{ __('audit.all_entities') }}</option>
                            @foreach($entityTypes as $ent)
                                <option value="{{ $ent }}" {{ request('entity_type') === $ent ? 'selected' : '' }}>
                                    {{ class_basename($ent) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('audit.date_from') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="nex-input" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('audit.date_to') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="nex-input" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="submit" class="py-2 px-4 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                            {{ __('common.actions.filter') }}
                        </button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="py-2 px-4 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 text-xs font-semibold text-center transition">
                            {{ __('common.actions.clear_filters') }}
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Right Main Studio Stage -->
        <div class="xl:col-span-9 space-y-4">
            <!-- 3 Bento Telemetry Summary Cards -->
            @php
                $pageDiffCount = $auditLogs->getCollection()->filter(fn ($l) => ! empty($l->old_values) || ! empty($l->new_values))->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="nex-card p-5 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500">{{ __('audit.table.timestamp') }}</span>
                        <span class="w-8 h-8 rounded-full bg-[#FDF2F6] text-[#C93565] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between gap-2">
                        <span class="text-2xl font-bold text-[#1F171B] tabular-nums">{{ $auditLogs->total() }}</span>
                        <span class="text-[11px] font-mono text-[#9E224B] bg-[#FDF2F6] px-2.5 py-0.5 rounded-full border border-[#F8C9DC]">
                            {{ $pageDiffCount }} snapshots
                        </span>
                    </div>
                    <div class="h-2 rounded-full route-track-hatch overflow-hidden">
                        <div class="h-full bg-[#E04879] rounded-full" style="width: {{ min(100, max(18, $pageDiffCount * 10)) }}%"></div>
                    </div>
                </div>

                <div class="nex-card p-5 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500">{{ __('audit.table.actor') }}</span>
                        <span class="w-8 h-8 rounded-full bg-[#FAF7F9] text-[#1F171B] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-[#1F171B] tabular-nums">{{ $actors->count() }}</div>
                    <div class="text-[11px] text-zinc-400">{{ __('audit.all_actors') }}</div>
                </div>

                <div class="nex-card p-5 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500">{{ __('audit.table.target_entity') }}</span>
                        <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-[#1F171B] tabular-nums">{{ $entityTypes->count() }}</div>
                    <div class="text-[11px] text-zinc-400">{{ __('audit.all_entities') }}</div>
                </div>
            </div>

            <!-- Audit Log Studio Table Card -->
            <div class="nex-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-700">
                        <thead class="bg-[#FAF7F9] text-zinc-600 uppercase tracking-wider text-[11px] border-b border-[#ECE4E8] font-semibold">
                            <tr>
                                <th class="px-5 py-3.5">{{ __('audit.table.timestamp') }}</th>
                                <th class="px-5 py-3.5">{{ __('audit.table.actor') }}</th>
                                <th class="px-5 py-3.5 text-center">{{ __('audit.table.action') }}</th>
                                <th class="px-5 py-3.5">{{ __('audit.table.target_entity') }}</th>
                                <th class="px-5 py-3.5">{{ __('audit.table.ip_address') }}</th>
                                <th class="px-5 py-3.5 text-right">{{ __('audit.table.diff') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F0F3]">
                            @forelse($auditLogs as $log)
                                @php
                                    $actionColors = [
                                        'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'updated' => 'bg-[#FDF2F6] text-[#9E224B] border-[#F8C9DC]',
                                        'deleted' => 'bg-red-50 text-red-700 border-red-200',
                                        'status_updated' => 'bg-[#1F171B] text-white border-[#1F171B]',
                                        'exception_logged' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'exception_resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'setting_updated' => 'bg-[#FCE5EE] text-[#9E224B] border-[#F29EC0]',
                                    ];
                                    $badge = $actionColors[$log->action] ?? 'bg-[#FAF7F9] text-zinc-700 border-[#ECE4E8]';
                                @endphp
                                <tr class="hover:bg-[#FDF2F6]/45 transition">
                                    <td class="px-5 py-3.5 font-mono text-[11px] whitespace-nowrap">
                                        <span class="text-zinc-900 font-semibold tabular-nums">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                        <span class="text-zinc-500 block text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        @if($log->user)
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-[#1F171B] text-white text-[10px] font-bold flex items-center justify-center shrink-0 uppercase">
                                                    {{ substr($log->user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-zinc-900">{{ $log->user->name }}</div>
                                                    <div class="text-[11px] text-zinc-500 flex items-center gap-1.5 mt-0.5">
                                                        <span>{{ $log->user->email }}</span>
                                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-[#FAF7F9] text-zinc-600 border border-[#ECE4E8]">{{ \App\Enums\Role::tryFrom($log->user->role)?->label() ?? $log->user->role }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="italic text-zinc-500">{{ __('audit.system_actor') }}</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-3.5 font-mono text-xs">
                                        <span class="text-[#C93565] font-semibold">{{ class_basename($log->entity_type) }}</span>
                                        <span class="text-zinc-500">#{{ $log->entity_id }}</span>
                                    </td>

                                    <td class="px-5 py-3.5 font-mono text-[11px] text-zinc-600">
                                        <span class="px-2 py-0.5 rounded-full bg-[#FAF7F9] border border-[#ECE4E8]">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                    </td>

                                    <td class="px-5 py-3.5 text-right">
                                        @if(!empty($log->old_values) || !empty($log->new_values))
                                            <details class="group inline-block text-left">
                                                <summary class="px-3 py-1.5 rounded-full bg-[#FDF2F6] hover:bg-[#C93565] hover:text-white border border-[#F8C9DC] text-xs font-semibold text-[#9E224B] cursor-pointer select-none transition">
                                                    {!! __('audit.diff_modal.inspect_button') !!}
                                                </summary>
                                                <div class="fixed inset-0 bg-[#1F171B]/45 backdrop-blur-xs z-50 flex items-center justify-center p-4 group-open:flex hidden" onclick="if(event.target === this) this.parentElement.removeAttribute('open');">
                                                    <div class="bg-white border border-[#ECE4E8] rounded-[24px] max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto text-left" onclick="event.stopPropagation();">
                                                        <div class="flex items-center justify-between pb-3 border-b border-[#F5F0F3]">
                                                            <div>
                                                                <h3 class="text-sm font-bold text-[#1F171B] uppercase tracking-wider">
                                                                    {{ __('audit.diff_modal.snapshot_title', ['entity' => class_basename($log->entity_type), 'id' => $log->entity_id]) }}
                                                                </h3>
                                                                <p class="text-xs text-zinc-500 mt-0.5">{!! __('audit.diff_modal.snapshot_subtitle', ['action' => '<span class="font-mono text-[#C93565] font-bold uppercase">' . $log->action . '</span>', 'time' => $log->created_at->format('Y-m-d H:i:s')]) !!}</p>
                                                            </div>
                                                            <button type="button" class="w-8 h-8 rounded-full text-zinc-500 hover:text-[#1F171B] hover:bg-[#FAF7F9] flex items-center justify-center text-lg leading-none cursor-pointer transition" onclick="this.closest('details').removeAttribute('open');" aria-label="Close">
                                                                &times;
                                                            </button>
                                                        </div>

                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
                                                            <div class="space-y-1.5">
                                                                <span class="text-[11px] font-bold uppercase tracking-wider text-red-700">{{ __('audit.diff_modal.previous_state') }}</span>
                                                                <pre class="p-3.5 bg-[#FAF7F9] border border-[#ECE4E8] rounded-2xl text-zinc-800 overflow-x-auto text-[11px]">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                            </div>

                                                            <div class="space-y-1.5">
                                                                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">{{ __('audit.diff_modal.new_state') }}</span>
                                                                <pre class="p-3.5 bg-[#FDF2F6]/60 border border-[#F8C9DC] rounded-2xl text-zinc-800 overflow-x-auto text-[11px]">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </details>
                                        @else
                                            <span class="text-zinc-400 italic text-[11px]">{{ __('audit.diff_modal.no_state_change') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-zinc-500">
                                        {{ __('audit.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $auditLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
