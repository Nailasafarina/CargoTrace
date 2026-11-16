@extends('layouts.app')

@section('title', 'System Audit Trails')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">System Audit Trails &amp; Compliance</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                    Admin Guarded
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Immutable record of all administrative, dispatch, and operational actions performed across the CargoTrace platform.
            </p>
        </div>
        <div>
            <span class="text-xs font-mono text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-2 rounded-xl block">
                Total Logged Events: <strong class="text-white">{{ $auditLogs->total() }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 text-xs">
            <span class="font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Audit Log Filters
            </span>
            @if(request()->hasAny(['user_id', 'action', 'entity_type', 'date_from', 'date_to']))
                <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-slate-400 hover:text-white underline">
                    Clear all filters
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Actor Filter -->
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Actor (User)</label>
                <select name="user_id" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Actors</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" {{ (string) request('user_id') === (string) $actor->id ? 'selected' : '' }}>
                            {{ $actor->name }} ({{ $actor->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Action</label>
                <select name="action" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ strtoupper($act) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Entity Type Filter -->
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Entity Type</label>
                <select name="entity_type" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Entities</option>
                    @foreach($entityTypes as $ent)
                        <option value="{{ $ent }}" {{ request('entity_type') === $ent ? 'selected' : '' }}>
                            {{ class_basename($ent) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Date From</label>
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                />
            </div>

            <!-- Date To & Submit -->
            <div class="flex items-end gap-2">
                <div class="flex-grow">
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Date To</label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                    />
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-semibold transition cursor-pointer shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">Timestamp (UTC)</th>
                    <th class="px-5 py-3.5">Actor</th>
                    <th class="px-5 py-3.5 text-center">Action</th>
                    <th class="px-5 py-3.5">Target Entity</th>
                    <th class="px-5 py-3.5">IP Address</th>
                    <th class="px-5 py-3.5 text-right">State Changes / Diff</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($auditLogs as $log)
                    @php
                        $actionColors = [
                            'created' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                            'updated' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                            'deleted' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                            'status_updated' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                            'exception_logged' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                            'exception_resolved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                            'setting_updated' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                        ];
                        $badge = $actionColors[$log->action] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition">
                        <!-- Timestamp -->
                        <td class="px-5 py-3.5 font-mono text-[11px] whitespace-nowrap">
                            <span class="text-white">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                            <span class="text-slate-500 block text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                        </td>

                        <!-- Actor -->
                        <td class="px-5 py-3.5">
                            @if($log->user)
                                <div class="font-medium text-white">{{ $log->user->name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $log->user->email }}</span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-slate-800 text-slate-400">{{ $log->user->role }}</span>
                                </div>
                            @else
                                <span class="italic text-slate-500">System / Background Task</span>
                            @endif
                        </td>

                        <!-- Action -->
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }}">
                                {{ $log->action }}
                            </span>
                        </td>

                        <!-- Target Entity -->
                        <td class="px-5 py-3.5 font-mono text-xs">
                            <span class="text-sky-400 font-semibold">{{ class_basename($log->entity_type) }}</span>
                            <span class="text-slate-500">#{{ $log->entity_id }}</span>
                        </td>

                        <!-- IP Address -->
                        <td class="px-5 py-3.5 font-mono text-[11px] text-slate-400">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>

                        <!-- Changes Inspector -->
                        <td class="px-5 py-3.5 text-right">
                            @if(!empty($log->old_values) || !empty($log->new_values))
                                <details class="group inline-block text-left">
                                    <summary class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300 hover:text-white cursor-pointer select-none transition">
                                        Inspect Diff &darr;
                                    </summary>
                                    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 group-open:flex hidden" onclick="if(event.target === this) this.parentElement.removeAttribute('open');">
                                        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto" onclick="event.stopPropagation();">
                                            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                                <div>
                                                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                                                        Audit Snapshot: {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                                    </h3>
                                                    <p class="text-xs text-slate-400 mt-0.5">Action: <span class="font-mono text-purple-400 font-bold uppercase">{{ $log->action }}</span> at {{ $log->created_at->format('Y-m-d H:i:s') }} UTC</p>
                                                </div>
                                                <button type="button" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800" onclick="this.closest('details').removeAttribute('open');">
                                                    &times;
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
                                                <!-- Old Values -->
                                                <div class="space-y-1">
                                                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400">Previous State</span>
                                                    <pre class="p-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-300 overflow-x-auto text-[11px]">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </div>

                                                <!-- New Values -->
                                                <div class="space-y-1">
                                                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400">New State</span>
                                                    <pre class="p-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-300 overflow-x-auto text-[11px]">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            @else
                                <span class="text-slate-600 italic text-[11px]">No state change</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            No audit trails found matching the query criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $auditLogs->links() }}
    </div>
</div>
@endsection
