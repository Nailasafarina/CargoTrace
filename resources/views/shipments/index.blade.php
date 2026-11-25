@extends('layouts.app')

@section('title', __('shipments.title'))

@section('content')
<div class="space-y-4">
    <!-- Top Tactile Mode Strip (NexCargo Command Pattern) -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $activeStatus = request('status');
                $activeDelay = request('delay_status');
            @endphp
            <a href="{{ route('shipments.index') }}" class="w-9 h-9 rounded-full flex items-center justify-center shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition hover:scale-105 {{ !$activeStatus && !$activeDelay ? 'bg-[#C93565] text-white shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6]' }}" title="{{ __('shipments.filters.all_statuses') }}" aria-label="{{ __('shipments.filters.all_statuses') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </a>
            <a href="{{ route('shipments.index', ['status' => 'BOOKED']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $activeStatus === 'BOOKED' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                {{ __('shipments.status.booked') }}
            </a>
            <a href="{{ route('shipments.index', ['status' => 'IN_TRANSIT']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $activeStatus === 'IN_TRANSIT' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                {{ __('shipments.status.in_transit') }}
            </a>
            <a href="{{ route('shipments.index', ['status' => 'DELIVERED']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $activeStatus === 'DELIVERED' ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                {{ __('shipments.status.delivered') }}
            </a>
            <a href="{{ route('shipments.index', ['delay_status' => 'HIGH']) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $activeDelay === 'HIGH' ? 'bg-[#1F171B] text-white shadow-sm' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                {{ __('shipments.delay_status.high') }}
            </a>
        </div>

        @can('create', App\Models\Shipment::class)
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-sm shadow-[#C93565]/20 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ __('shipments.new_shipment') }}
            </a>
        @endcan
    </div>

    <!-- NexCargo Asymmetric 12-Col Bento Stage -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        <!-- Left Sticky Control & Filter Rail -->
        <aside class="xl:col-span-3 xl:sticky xl:top-20 space-y-3">
            <!-- Fleet Consignment Identity Card -->
            <div class="nex-card p-4 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <h1 class="text-lg font-bold tracking-tight text-[#1F171B]">{{ __('shipments.title') }}</h1>
                        <p class="text-[11px] text-zinc-500 mt-0.5 leading-relaxed">
                            @if(Auth::user()->isCustomer())
                                {{ __('dashboard.customer.subtitle') }}
                            @else
                                {{ __('shipments.subtitle') }}
                            @endif
                        </p>
                    </div>
                    <div class="w-20 h-10 shrink-0 flex items-center justify-end select-none">
                        <img src="{{ asset('images/fleet-truck-mini.png') }}" alt="Fleet transport unit" class="w-full h-full object-contain" />
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-[#F5F0F3] text-xs">
                    <span class="font-mono text-zinc-600">{{ __('shipments.filters.found_count', ['count' => $shipments->total()]) }}</span>
                    @if(request()->hasAny(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to', 'sort_by', 'sort_dir']))
                        <a href="{{ route('shipments.index') }}" class="text-[#C93565] hover:underline font-semibold text-[11px]">
                            {{ __('shipments.filters.reset_all') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Multi-Parameter Filter Card -->
            <div class="nex-card p-5 space-y-3.5">
                <div class="flex items-center gap-2 pb-2.5 border-b border-[#F5F0F3]">
                    <span class="w-7 h-7 rounded-full bg-[#FAF7F9] text-[#C93565] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </span>
                    <span class="text-xs font-bold text-zinc-900">{{ __('shipments.filters.server_side_title') }}</span>
                </div>

                <form method="GET" action="{{ route('shipments.index') }}" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.search_identifier') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('shipments.filters.search_placeholder_full') }}" class="nex-input" />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.lifecycle_status') }}</label>
                            <select name="status" class="nex-input">
                                <option value="">{{ __('shipments.filters.all_statuses') }}</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.delay_state') }}</label>
                            <select name="delay_status" class="nex-input">
                                <option value="">{{ __('shipments.filters.all_delays') }}</option>
                                @foreach($delayStatuses as $dst)
                                    <option value="{{ $dst->value }}" {{ request('delay_status') === $dst->value ? 'selected' : '' }}>
                                        {{ $dst->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.operating_carrier') }}</label>
                        <select name="carrier_id" class="nex-input">
                            <option value="">{{ __('shipments.filters.all_carriers') }}</option>
                            @foreach($carriers as $car)
                                <option value="{{ $car->id }}" {{ (string) request('carrier_id') === (string) $car->id ? 'selected' : '' }}>
                                    [{{ $car->code }}] {{ $car->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.origin_port') }}</label>
                            <select name="origin_id" class="nex-input">
                                <option value="">{{ __('shipments.filters.all_origins') }}</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ (string) request('origin_id') === (string) $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }} ({{ $loc->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.destination_port') }}</label>
                            <select name="destination_id" class="nex-input">
                                <option value="">{{ __('shipments.filters.all_destinations') }}</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ (string) request('destination_id') === (string) $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }} ({{ $loc->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.departure_after') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="nex-input" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.departure_before') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="nex-input" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">{{ __('shipments.filters.sort_by') }}</label>
                            <select name="sort_by" class="nex-input">
                                <option value="expected_departure_at" {{ ($sortBy ?? '') === 'expected_departure_at' ? 'selected' : '' }}>{{ __('shipments.filters.sort_dep_date') }}</option>
                                <option value="expected_arrival_at" {{ ($sortBy ?? '') === 'expected_arrival_at' ? 'selected' : '' }}>{{ __('shipments.filters.sort_arr_eta') }}</option>
                                <option value="shipment_number" {{ ($sortBy ?? '') === 'shipment_number' ? 'selected' : '' }}>{{ __('shipments.filters.sort_shipment_num') }}</option>
                                <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>{{ __('shipments.filters.sort_created_at') }}</option>
                                <option value="weight_kg" {{ ($sortBy ?? '') === 'weight_kg' ? 'selected' : '' }}>{{ __('shipments.filters.sort_cargo_weight') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-600 mb-1">Order</label>
                            <select name="sort_dir" class="nex-input">
                                <option value="desc" {{ ($sortDir ?? '') === 'desc' ? 'selected' : '' }}>{{ __('shipments.filters.descending') }}</option>
                                <option value="asc" {{ ($sortDir ?? '') === 'asc' ? 'selected' : '' }}>{{ __('shipments.filters.ascending') }}</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-xs font-semibold text-white shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        {{ __('shipments.filters.apply_filters') }}
                    </button>
                </form>
            </div>
        </aside>

        <!-- Right Main Studio Stage -->
        <div class="xl:col-span-9 space-y-4">
            <!-- 3 Bento Fleet Telemetry Cards -->
            @php
                $pageItems = $shipments->getCollection();
                $pageTransit = $pageItems->filter(fn ($s) => $s->current_status === \App\Enums\ShipmentStatus::InTransit)->count();
                $pageDelayed = $pageItems->filter(fn ($s) => $s->delay_status->isDelayed())->count();
                $pageOnTimePct = $pageItems->count() > 0 ? (int) round((($pageItems->count() - $pageDelayed) / $pageItems->count()) * 100) : 100;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="nex-card p-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500">{{ __('shipments.title') }}</span>
                        <span class="w-8 h-8 rounded-full bg-[#FAF7F9] text-[#1F171B] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-[#1F171B] tabular-nums">{{ $shipments->total() }}</div>
                    <div class="text-[11px] text-zinc-400">{{ __('shipments.filters.found_count', ['count' => $shipments->total()]) }}</div>
                </div>

                <div class="nex-card p-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500">{{ __('shipments.status.in_transit') }}</span>
                        <span class="w-8 h-8 rounded-full bg-[#FDF2F6] text-[#C93565] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-[#C93565] tabular-nums">{{ $pageTransit }}</div>
                    <div class="text-[11px] text-zinc-400">Page {{ $shipments->currentPage() }} ({{ $pageItems->count() }} rows)</div>
                </div>

                <div class="nex-card p-5 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500">{{ __('shipments.table.schedule_delay') }}</span>
                        <span class="text-xs font-bold text-[#9E224B] tabular-nums">{{ $pageOnTimePct }}%</span>
                    </div>
                    <div class="h-2.5 rounded-full route-track-hatch overflow-hidden">
                        <div class="h-full bg-[#E04879] rounded-full" style="width: {{ $pageOnTimePct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-zinc-500">
                        <span>Page {{ $shipments->currentPage() }} {{ __('shipments.show.on_schedule') }}</span>
                        <span class="font-semibold text-zinc-800">{{ $pageDelayed }} {{ __('shipments.delay_status.delayed') }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Filter Badges -->
            @php
                $activeFilters = array_filter(request()->only(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to']));
            @endphp
            @if(!empty($activeFilters))
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-zinc-500 font-semibold">{{ __('shipments.filters.active_filters') }}</span>
                    @foreach($activeFilters as $key => $val)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] font-mono text-[11px]">
                            {{ $key }}: <strong class="text-[#1F171B]">{{ $val }}</strong>
                        </span>
                    @endforeach
                    <a href="{{ route('shipments.index') }}" class="text-xs text-zinc-500 hover:text-red-600 underline ml-1 font-semibold">
                        {{ __('shipments.filters.clear_all') }}
                    </a>
                </div>
            @endif

            <!-- Shipments Studio Table -->
            <div class="nex-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-700">
                        <thead class="bg-[#FAF7F9] text-zinc-600 uppercase tracking-wider text-[10px] font-semibold border-b border-[#ECE4E8]">
                            <tr>
                                <th class="pl-4 pr-2 py-3 whitespace-nowrap">
                                    <a href="{{ route('shipments.index', array_merge(request()->query(), ['sort_by' => 'shipment_number', 'sort_dir' => ($sortBy === 'shipment_number' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-[#1F171B] flex items-center gap-1">
                                        {{ __('shipments.table.shipment_number') }}
                                        @if($sortBy === 'shipment_number')
                                            <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-2 py-3">{{ __('shipments.table.route_corridor') }}</th>
                                <th class="px-2 py-3">{{ __('shipments.table.carrier_cargo') }}</th>
                                <th class="px-2 py-3">{{ __('shipments.table.customer') }}</th>
                                <th class="px-2 py-3 text-center">{{ __('shipments.table.lifecycle_status') }}</th>
                                <th class="px-2 py-3 text-center">
                                    <a href="{{ route('shipments.index', array_merge(request()->query(), ['sort_by' => 'expected_departure_at', 'sort_dir' => ($sortBy === 'expected_departure_at' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-[#1F171B] inline-flex items-center gap-1">
                                        {{ __('shipments.table.schedule_delay') }}
                                        @if($sortBy === 'expected_departure_at')
                                            <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="pl-2 pr-4 py-3 text-right whitespace-nowrap">{{ __('shipments.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F0F3]">
                            @forelse($shipments as $shipment)
                                <tr class="hover:bg-[#FDF2F6]/45 transition">
                                    <td class="pl-4 pr-2 py-3 whitespace-nowrap">
                                        <a href="{{ route('shipments.show', $shipment) }}" class="font-mono text-[10.5px] font-bold text-[#C93565] hover:underline">
                                            {{ $shipment->shipment_number }}
                                        </a>
                                        @if($shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->isNotEmpty())
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                                                    <svg class="w-2.5 h-2.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    {{ $shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->first()->type }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-2 py-3 max-w-[132px]">
                                        <div class="font-semibold text-zinc-900 flex items-center gap-1 truncate text-[11px]">
                                            <span>{{ $shipment->originLocation->city }}</span>
                                            <span class="text-[#E04879]">&rarr;</span>
                                            <span>{{ $shipment->destinationLocation->city }}</span>
                                        </div>
                                        <div class="text-[10px] text-zinc-500 mt-0.5 truncate" title="{{ $shipment->originLocation->name }}">
                                            {{ $shipment->originLocation->name }}
                                        </div>
                                    </td>

                                    <td class="px-2 py-3 max-w-[130px]">
                                        <div class="font-medium text-zinc-800 truncate text-[11px]" title="[{{ $shipment->carrier->code }}] {{ $shipment->carrier->name }}">
                                            <span class="font-mono text-[#C93565] font-bold">{{ $shipment->carrier->code }}</span> &middot; {{ $shipment->carrier->name }}
                                        </div>
                                        <div class="text-[10px] text-zinc-500 truncate" title="{{ $shipment->cargo_type }}">
                                            {{ $shipment->cargo_type }} ({{ number_format($shipment->weight_kg, 0) }} kg)
                                        </div>
                                    </td>

                                    <td class="px-2 py-3 text-zinc-700 max-w-[115px]">
                                        <div class="font-semibold text-zinc-900 truncate text-[11px]" title="{{ $shipment->customer->name }}">{{ $shipment->customer->name }}</div>
                                        <div class="text-[10px] text-zinc-500 truncate" title="{{ $shipment->customer->email }}">{{ $shipment->customer->email }}</div>
                                    </td>

                                    <td class="px-2 py-3 text-center whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider {{ $shipment->current_status->badgeClass() }}">
                                            {{ $shipment->current_status->label() }}
                                        </span>
                                    </td>

                                    <td class="px-2 py-3 text-center whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider {{ $shipment->delay_status->badgeClass() }}" title="{{ $shipment->delay_reason ?? __('shipments.show.on_schedule') }}">
                                            {{ $shipment->delay_status->label() }}
                                            @if($shipment->delay_hours > 0)
                                                ({{ $shipment->delay_hours }}h)
                                            @endif
                                        </span>
                                        <div class="text-[9.5px] text-zinc-500 mt-1 font-mono tabular-nums">
                                            ETA: {{ $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y') : $shipment->expected_arrival_at->format('d M Y') }}
                                        </div>
                                    </td>

                                    <td class="pl-2 pr-4 py-3 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('shipments.show', $shipment) }}" class="px-2 py-1 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-[10.5px] text-zinc-700 font-semibold transition">
                                                {{ __('common.actions.view') }}
                                            </a>
                                            @can('update', $shipment)
                                                <a href="{{ route('shipments.edit', $shipment) }}" class="px-2 py-1 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[10.5px] text-[#9E224B] border border-[#F8C9DC] font-semibold transition">
                                                    {{ __('common.actions.edit') }}
                                                </a>
                                            @endcan
                                            @can('delete', $shipment)
                                                <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" class="inline-block" onsubmit="return confirm('{{ __('shipments.show.delete_confirm', ['number' => $shipment->shipment_number]) }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 rounded-full bg-red-50 hover:bg-red-100 text-[10.5px] text-red-700 border border-red-200 font-semibold transition cursor-pointer">
                                                        {{ __('common.actions.delete') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-zinc-500">
                                        {{ __('shipments.empty.description') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination links with query string preservation -->
            <div>
                {{ $shipments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
