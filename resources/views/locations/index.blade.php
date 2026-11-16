@extends('layouts.app')

@section('title', 'Waypoint Hubs & Ports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Locations & Waypoint Hubs</h1>
            <p class="text-sm text-slate-400 mt-1">Maritime ports, airports, and transshipment terminals along global trade corridors.</p>
        </div>
        @can('create', App\Models\Location::class)
            <a href="{{ route('locations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add New Hub / Port
            </a>
        @endcan
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('locations.index') }}" class="w-full sm:w-auto flex flex-wrap gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name, city, country..."
                class="px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-60"
            />
            <select name="type" class="px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                <option value="">All Facility Types</option>
                @foreach($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">
                Filter
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('locations.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-400">Clear</a>
            @endif
        </form>
        <div class="text-xs text-slate-400">
            Showing <span class="font-semibold text-white">{{ $locations->total() }}</span> hubs
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">Hub / Facility Name</th>
                    <th class="px-5 py-3.5">Type</th>
                    <th class="px-5 py-3.5">Country & City</th>
                    <th class="px-5 py-3.5">GPS Coordinates (WGS84)</th>
                    <th class="px-5 py-3.5 text-center">Shipments (Out / In)</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($locations as $loc)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3.5 font-semibold text-white">
                            <a href="{{ route('locations.show', $loc) }}" class="hover:text-sky-400 transition">
                                {{ $loc->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $typeStyles = [
                                    'port' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                    'airport' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                                    'warehouse' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                    'city' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                ];
                                $style = $typeStyles[$loc->type->value] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $style }}">
                                {{ $loc->type->value }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-white font-medium">{{ $loc->city }}</span>,
                            <span class="text-slate-400">{{ $loc->country }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-[11px] text-slate-400">
                            {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="text-sky-400 font-semibold">{{ $loc->origin_shipments_count }}</span>
                            <span class="text-slate-600">/</span>
                            <span class="text-emerald-400 font-semibold">{{ $loc->destination_shipments_count }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('locations.show', $loc) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
                                View
                            </a>
                            @can('update', $loc)
                                <a href="{{ route('locations.edit', $loc) }}" class="px-2.5 py-1 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 transition">
                                    Edit
                                </a>
                            @endcan
                            @can('delete', $loc)
                                <form method="POST" action="{{ route('locations.destroy', $loc) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete {{ $loc->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 transition cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                            No locations found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $locations->links() }}
    </div>
</div>
@endsection
