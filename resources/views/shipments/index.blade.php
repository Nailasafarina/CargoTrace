@extends('layouts.app')

@section('title', 'Shipments Directory')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Global Shipments Directory</h1>
            <p class="text-sm text-slate-400 mt-1">
                @if(Auth::user()->isCustomer())
                    Showing your private assigned cargo bookings and tracking records.
                @else
                    Fleet-wide visibility across active maritime corridors, delays, and exceptions.
                @endif
            </p>
        </div>
        @can('create', App\Models\Shipment::class)
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Create Shipment
            </a>
        @endcan
    </div>

    <!-- Advanced Multi-Parameter Filter Card -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 text-xs">
            <span class="font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Server-Side Query &amp; Corridor Filters
            </span>
            <span class="text-slate-400 font-mono">Found: {{ $shipments->total() }} shipments</span>
        </div>

        <form method="GET" action="{{ route('shipments.index') }}" class="space-y-3">
            <!-- Row 1: Core Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Query -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Search Identifier</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Tracking # or Cargo type..."
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Lifecycle Status</label>
                    <select name="status" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Delay Status Filter -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Delay State</label>
                    <select name="delay_status" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">All Delay States</option>
                        @foreach($delayStatuses as $dst)
                            <option value="{{ $dst->value }}" {{ request('delay_status') === $dst->value ? 'selected' : '' }}>
                                {{ $dst->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Carrier Filter -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Operating Carrier</label>
                    <select name="carrier_id" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">All Carriers</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ (string) request('carrier_id') === (string) $car->id ? 'selected' : '' }}>
                                [{{ $car->code }}] {{ $car->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Location & Date Range Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Origin Port -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Origin Port</label>
                    <select name="origin_id" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">All Origins</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (string) request('origin_id') === (string) $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Destination Port -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Destination Port</label>
                    <select name="destination_id" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">All Destinations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (string) request('destination_id') === (string) $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departure From -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Departure After</label>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <!-- Departure To -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Departure Before</label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>
            </div>

            <!-- Row 3: Sorting & Action Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] text-slate-400 font-medium">Sort By:</span>
                    <select name="sort_by" class="px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-lg text-xs text-white focus:outline-none">
                        <option value="expected_departure_at" {{ ($sortBy ?? '') === 'expected_departure_at' ? 'selected' : '' }}>Departure Date</option>
                        <option value="expected_arrival_at" {{ ($sortBy ?? '') === 'expected_arrival_at' ? 'selected' : '' }}>Arrival ETA</option>
                        <option value="shipment_number" {{ ($sortBy ?? '') === 'shipment_number' ? 'selected' : '' }}>Shipment Number</option>
                        <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="weight_kg" {{ ($sortBy ?? '') === 'weight_kg' ? 'selected' : '' }}>Cargo Weight</option>
                    </select>
                    <select name="sort_dir" class="px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-lg text-xs text-white focus:outline-none">
                        <option value="desc" {{ ($sortDir ?? '') === 'desc' ? 'selected' : '' }}>Descending &darr;</option>
                        <option value="asc" {{ ($sortDir ?? '') === 'asc' ? 'selected' : '' }}>Ascending &uarr;</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-xs font-semibold text-white transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to', 'sort_by', 'sort_dir']))
                        <a href="{{ route('shipments.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 transition">
                            Reset All
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Badges -->
    @php
        $activeFilters = array_filter(request()->only(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to']));
    @endphp
    @if(!empty($activeFilters))
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-500 font-medium">Active Filters:</span>
            @foreach($activeFilters as $key => $val)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-400 border border-sky-500/30 font-mono text-[11px]">
                    {{ $key }}: <strong class="text-white">{{ $val }}</strong>
                </span>
            @endforeach
            <a href="{{ route('shipments.index') }}" class="text-xs text-slate-400 hover:text-rose-400 underline ml-1">
                Clear all
            </a>
        </div>
    @endif

    <!-- Shipments Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">
                        <a href="{{ route('shipments.index', array_merge(request()->query(), ['sort_by' => 'shipment_number', 'sort_dir' => ($sortBy === 'shipment_number' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-white flex items-center gap-1">
                            Shipment #
                            @if($sortBy === 'shipment_number')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-5 py-3.5">Route Corridor</th>
                    <th class="px-5 py-3.5">Carrier / Cargo</th>
                    <th class="px-5 py-3.5">Customer</th>
                    <th class="px-5 py-3.5 text-center">Lifecycle Status</th>
                    <th class="px-5 py-3.5 text-center">
                        <a href="{{ route('shipments.index', array_merge(request()->query(), ['sort_by' => 'expected_departure_at', 'sort_dir' => ($sortBy === 'expected_departure_at' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-white inline-flex items-center gap-1">
                            Schedule / Delay
                            @if($sortBy === 'expected_departure_at')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($shipments as $shipment)
                    <tr class="hover:bg-slate-800/40 transition">
                        <!-- Number & Exceptions Flag -->
                        <td class="px-5 py-3.5">
                            <a href="{{ route('shipments.show', $shipment) }}" class="font-mono font-bold text-sky-400 hover:underline">
                                {{ $shipment->shipment_number }}
                            </a>
                            @if($shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->isNotEmpty())
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                        <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        {{ $shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->first()->type }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- Route Corridor -->
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-white flex items-center gap-1.5">
                                <span>{{ $shipment->originLocation->city }}</span>
                                <span class="text-slate-500">&rarr;</span>
                                <span>{{ $shipment->destinationLocation->city }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                {{ $shipment->originLocation->name }}
                            </div>
                        </td>

                        <!-- Carrier & Cargo -->
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-200">
                                <span class="font-mono text-sky-400 font-semibold">{{ $shipment->carrier->code }}</span> &middot; {{ $shipment->carrier->name }}
                            </div>
                            <div class="text-[11px] text-slate-400 truncate max-w-[200px]" title="{{ $shipment->cargo_type }}">
                                {{ $shipment->cargo_type }} ({{ number_format($shipment->weight_kg, 1) }} kg)
                            </div>
                        </td>

                        <!-- Customer -->
                        <td class="px-5 py-3.5 text-slate-300">
                            <div class="font-medium text-white">{{ $shipment->customer->name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $shipment->customer->email }}</div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $shipment->current_status->badgeClass() }}">
                                {{ $shipment->current_status->label() }}
                            </span>
                        </td>

                        <!-- Delay Status -->
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $shipment->delay_status->badgeClass() }}" title="{{ $shipment->delay_reason ?? 'On schedule' }}">
                                {{ $shipment->delay_status->label() }}
                                @if($shipment->delay_hours > 0)
                                    ({{ $shipment->delay_hours }}h)
                                @endif
                            </span>
                            <div class="text-[10px] text-slate-500 mt-1 font-mono">
                                ETA: {{ $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y') : $shipment->expected_arrival_at->format('d M Y') }}
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('shipments.show', $shipment) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
                                View
                            </a>
                            @can('update', $shipment)
                                <a href="{{ route('shipments.edit', $shipment) }}" class="px-2.5 py-1 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 transition">
                                    Edit
                                </a>
                            @endcan
                            @can('delete', $shipment)
                                <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to permanently delete shipment {{ $shipment->shipment_number }}?');">
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
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            No shipments found matching the specified parameters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links with query string preservation -->
    <div>
        {{ $shipments->links() }}
    </div>
</div>
@endsection
