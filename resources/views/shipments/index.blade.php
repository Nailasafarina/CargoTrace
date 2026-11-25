@extends('layouts.app')

@section('title', __('shipments.title'))

@section('content')
@php
    $activeStatus = request('status');
    $activeDelay = request('delay_status');
    $pageItems = $shipments->getCollection();
    $pageTransit = $pageItems->filter(fn ($s) => $s->current_status === \App\Enums\ShipmentStatus::InTransit)->count();
    $pageDelayed = $pageItems->filter(fn ($s) => $s->delay_status->isDelayed())->count();
    $activeFilters = array_filter(request()->only(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to']));
@endphp

<div class="space-y-5">
    <!-- 1. Page Header Card (1 Balanced Row: Title + Badge + Description on Left, + Book New Shipment on Right) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#1A1718]">{{ __('shipments.title') }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FCECF1] text-[#BE2B5B] text-xs font-bold font-mono whitespace-nowrap">
                    {{ __('shipments.filters.found_count', ['count' => $shipments->total()]) }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-[#6B6467] mt-1.5 leading-relaxed">
                @if(Auth::user()->isCustomer())
                    {{ __('dashboard.customer.subtitle') }}
                @else
                    {{ __('shipments.subtitle') }}
                @endif
            </p>
        </div>

        @can('create', App\Models\Shipment::class)
            <div class="shrink-0">
                <a href="{{ route('shipments.create') }}" class="h-10 px-4 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-bold inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>{{ __('shipments.new_shipment') }}</span>
                </a>
            </div>
        @endcan
    </div>

    <!-- 2. Clean 4-Column Filter Card (Uniform h-10 rounded-xl Inputs & Unclipped Sort Controls) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] p-5 shadow-sm">
        <form method="GET" action="{{ route('shipments.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Row 1: Search Identifier | Lifecycle Status | Delay State | Operating Carrier -->
                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.search_identifier') }}</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('shipments.filters.search_placeholder_full') }}"
                        class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3.5 text-xs text-[#1A1718] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.lifecycle_status') }}</label>
                    <select name="status" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('shipments.filters.all_statuses') }}</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.delay_state') }}</label>
                    <select name="delay_status" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('shipments.filters.all_delays') }}</option>
                        @foreach($delayStatuses as $dst)
                            <option value="{{ $dst->value }}" {{ request('delay_status') === $dst->value ? 'selected' : '' }}>
                                {{ $dst->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.operating_carrier') }}</label>
                    <select name="carrier_id" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('shipments.filters.all_carriers') }}</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ (string) request('carrier_id') === (string) $car->id ? 'selected' : '' }}>
                                [{{ $car->code }}] {{ $car->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2: Origin Port | Destination Port | Departure After | Departure Before -->
                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.origin_port') }}</label>
                    <select name="origin_id" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('shipments.filters.all_origins') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (string) request('origin_id') === (string) $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.destination_port') }}</label>
                    <select name="destination_id" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                        <option value="">{{ __('shipments.filters.all_destinations') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (string) request('destination_id') === (string) $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.departure_after') }}</label>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.departure_before') }}</label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                    />
                </div>
            </div>

            <!-- Row 3: Sort By & Order on Left (min-w-[180px] each so text never clips), Reset & Apply Filters on Right -->
            <div class="pt-3 border-t border-[#EFE6EA] flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[190px] flex-1 sm:flex-initial">
                        <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">{{ __('shipments.filters.sort_by') }}</label>
                        <select name="sort_by" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                            <option value="expected_departure_at" {{ ($sortBy ?? '') === 'expected_departure_at' ? 'selected' : '' }}>{{ __('shipments.filters.sort_dep_date') }}</option>
                            <option value="expected_arrival_at" {{ ($sortBy ?? '') === 'expected_arrival_at' ? 'selected' : '' }}>{{ __('shipments.filters.sort_arr_eta') }}</option>
                            <option value="shipment_number" {{ ($sortBy ?? '') === 'shipment_number' ? 'selected' : '' }}>{{ __('shipments.filters.sort_shipment_num') }}</option>
                            <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>{{ __('shipments.filters.sort_created_at') }}</option>
                            <option value="weight_kg" {{ ($sortBy ?? '') === 'weight_kg' ? 'selected' : '' }}>{{ __('shipments.filters.sort_cargo_weight') }}</option>
                        </select>
                    </div>

                    <div class="min-w-[180px] flex-1 sm:flex-initial">
                        <label class="block text-xs font-semibold text-[#6B6467] mb-1.5">Order</label>
                        <select name="sort_dir" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                            <option value="desc" {{ ($sortDir ?? '') === 'desc' ? 'selected' : '' }}>{{ __('shipments.filters.descending') }}</option>
                            <option value="asc" {{ ($sortDir ?? '') === 'asc' ? 'selected' : '' }}>{{ __('shipments.filters.ascending') }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 shrink-0">
                    @if(request()->hasAny(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to', 'sort_by', 'sort_dir']))
                        <a href="{{ route('shipments.index') }}" class="h-10 px-4 rounded-xl bg-white hover:bg-[#FAF7F8] border border-[#E5DCE0] text-xs font-semibold text-[#1A1718] hover:text-[#BE2B5B] inline-flex items-center justify-center whitespace-nowrap transition">
                            {{ __('shipments.filters.reset_all') }}
                        </a>
                    @endif
                    <button type="submit" class="h-10 px-5 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-xs font-semibold text-white transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>{{ __('shipments.filters.apply_filters') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Badges -->
    @if(!empty($activeFilters))
        <div class="flex flex-wrap items-center gap-2 text-xs px-1">
            <span class="text-[#6B6467] font-semibold">{{ __('shipments.filters.active_filters') }}</span>
            @foreach($activeFilters as $key => $val)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#FCECF1] text-[#BE2B5B] border border-[#EFE6EA] font-mono text-[11px]">
                    {{ $key }}: <strong class="text-[#1A1718]">{{ $val }}</strong>
                </span>
            @endforeach
            <a href="{{ route('shipments.index') }}" class="text-xs text-[#6B6467] hover:text-[#BE2B5B] underline ml-1 font-semibold">
                {{ __('shipments.filters.clear_all') }}
            </a>
        </div>
    @endif

    <!-- 3. Full-Width Shipments Data Table Card (With Top Quick Status Tabs Bar) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] shadow-sm overflow-hidden">
        <!-- Quick Status Tabs Bar at Top of Table Card -->
        <div class="px-5 py-3.5 border-b border-[#EFE6EA] flex flex-wrap items-center gap-2 bg-white">
            <a href="{{ route('shipments.index') }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ !$activeStatus && !$activeDelay ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('shipments.filters.all_statuses') }}
            </a>
            <a href="{{ route('shipments.index', ['status' => 'BOOKED']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeStatus === 'BOOKED' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('shipments.status.booked') }}
            </a>
            <a href="{{ route('shipments.index', ['status' => 'IN_TRANSIT']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeStatus === 'IN_TRANSIT' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('shipments.status.in_transit') }}
            </a>
            <a href="{{ route('shipments.index', ['status' => 'DELIVERED']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeStatus === 'DELIVERED' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('shipments.status.delivered') }}
            </a>
            <a href="{{ route('shipments.index', ['delay_status' => 'HIGH']) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeDelay === 'HIGH' ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('shipments.delay_status.high') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1A1718]">
                <thead class="bg-[#FAF7F8] text-[#6B6467] uppercase tracking-wider text-xs font-semibold border-b border-[#EFE6EA]">
                    <tr>
                        <th class="px-5 py-3.5 whitespace-nowrap">
                            <a href="{{ route('shipments.index', array_merge(request()->query(), ['sort_by' => 'shipment_number', 'sort_dir' => ($sortBy === 'shipment_number' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-[#1A1718] flex items-center gap-1">
                                {{ __('shipments.table.shipment_number') }}
                                @if($sortBy === 'shipment_number')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3.5 whitespace-nowrap">{{ __('shipments.table.route_corridor') }}</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">{{ __('shipments.table.carrier_cargo') }}</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">{{ __('shipments.table.customer') }}</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">{{ __('shipments.table.lifecycle_status') }}</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">
                            <a href="{{ route('shipments.index', array_merge(request()->query(), ['sort_by' => 'expected_departure_at', 'sort_dir' => ($sortBy === 'expected_departure_at' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-[#1A1718] inline-flex items-center gap-1">
                                {{ __('shipments.table.schedule_delay') }}
                                @if($sortBy === 'expected_departure_at')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3.5 text-right whitespace-nowrap">{{ __('shipments.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE6EA]">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-[#FAF7F8]/60 transition">
                            <td class="px-5 py-3.5 align-middle whitespace-nowrap">
                                <a href="{{ route('shipments.show', $shipment) }}" class="font-mono text-xs font-bold text-[#BE2B5B] hover:underline">
                                    {{ $shipment->shipment_number }}
                                </a>
                                @if($shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->isNotEmpty())
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200 whitespace-nowrap">
                                            <svg class="w-2.5 h-2.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            {{ $shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->first()->type }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-bold text-[#1A1718] flex items-center gap-1.5 text-xs whitespace-nowrap">
                                    <span>{{ $shipment->originLocation->city }}</span>
                                    <span class="text-[#BE2B5B]">&rarr;</span>
                                    <span>{{ $shipment->destinationLocation->city }}</span>
                                </div>
                                <div class="text-[11px] text-[#6B6467] mt-0.5">
                                    {{ $shipment->originLocation->name }}
                                </div>
                            </td>

                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-medium text-[#1A1718] text-xs whitespace-nowrap">
                                    <span class="font-mono text-[#BE2B5B] font-bold">{{ $shipment->carrier->code }}</span> &middot; {{ $shipment->carrier->name }}
                                </div>
                                <div class="text-[11px] text-[#6B6467] mt-0.5">
                                    {{ $shipment->cargo_type }} ({{ number_format($shipment->weight_kg, 0) }} kg)
                                </div>
                            </td>

                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-semibold text-[#1A1718] text-xs">{{ $shipment->customer->name }}</div>
                                <div class="text-[11px] text-[#6B6467]">{{ $shipment->customer->email }}</div>
                            </td>

                            <td class="px-4 py-3.5 align-middle text-center whitespace-nowrap">
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $shipment->current_status->badgeClass() }}">
                                    {{ $shipment->current_status->label() }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5 align-middle text-center whitespace-nowrap">
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $shipment->delay_status->badgeClass() }}" title="{{ $shipment->delay_reason ?? __('shipments.show.on_schedule') }}">
                                    {{ $shipment->delay_status->label() }}
                                    @if($shipment->delay_hours > 0)
                                        ({{ $shipment->delay_hours }}h)
                                    @endif
                                </span>
                                <div class="text-[10px] text-[#6B6467] mt-1 font-mono tabular-nums">
                                    ETA: {{ $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y') : $shipment->expected_arrival_at->format('d M Y') }}
                                </div>
                            </td>

                            <td class="px-5 py-3.5 align-middle text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                    <a href="{{ route('shipments.show', $shipment) }}" class="px-3 py-1.5 rounded-lg bg-[#FAF7F8] hover:bg-[#EFE6EA] border border-[#EFE6EA] text-xs text-[#1A1718] font-semibold whitespace-nowrap transition">
                                        {{ __('common.actions.view') }}
                                    </a>
                                    @can('update', $shipment)
                                        <a href="{{ route('shipments.edit', $shipment) }}" class="px-3 py-1.5 rounded-lg bg-[#FCECF1] hover:bg-[#BE2B5B] text-xs text-[#BE2B5B] hover:text-white border border-[#EFE6EA] font-semibold whitespace-nowrap transition">
                                            {{ __('common.actions.edit') }}
                                        </a>
                                    @endcan
                                    @can('delete', $shipment)
                                        <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" class="inline-block" onsubmit="return confirm('{{ __('shipments.show.delete_confirm', ['number' => $shipment->shipment_number]) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-xs text-rose-700 border border-rose-200 font-semibold whitespace-nowrap transition cursor-pointer">
                                                {{ __('common.actions.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-[#6B6467]">
                                {{ __('shipments.empty.description') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
            <div class="px-5 py-4 border-t border-[#EFE6EA] bg-[#FAF7F8]/40">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
