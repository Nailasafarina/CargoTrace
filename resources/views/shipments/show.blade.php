@extends('layouts.app')

@section('title', __('shipments.details_title') . ' ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">
    <!-- Top Studio Command Header -->
    <div class="nex-card p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight text-[#1F171B]">
                    {{ $shipment->shipment_number }}
                </h1>

                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $shipment->current_status->badgeClasses() }}">
                    {{ $shipment->current_status->label() }}
                </span>

                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $shipment->delay_status->badgeClasses() }}" title="{{ $shipment->delay_reason ?? __('shipments.show.on_schedule') }}">
                    @if($shipment->delay_status === \App\Enums\DelayStatus::OnTime)
                        {{ __('shipments.show.on_schedule') }}
                    @else
                        {{ $shipment->delay_status->label() }} ({{ $shipment->delay_hours }}h)
                    @endif
                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-1.5">
                {{ __('shipments.show.contracted_carrier') }} <span class="text-[#1F171B] font-semibold">{{ $shipment->carrier->name }}</span> &bull; {{ __('shipments.show.booked_by') }} {{ $shipment->creator->name }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-zinc-700 transition">
                {{ __('shipments.show.back_shipments') }}
            </a>
            @can('update', $shipment)
                <a href="{{ route('shipments.edit', $shipment) }}" class="px-4 py-2 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold transition">
                    {{ __('shipments.show.edit_booking') }}
                </a>
            @endcan
            @can('delete', $shipment)
                <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" onsubmit="return confirm('{{ __('shipments.show.delete_confirm', ['number' => $shipment->shipment_number]) }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold transition cursor-pointer">
                        {{ __('common.actions.delete') }}
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if($shipment->delay_reason)
        <div class="nex-card p-4 text-xs text-zinc-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] shrink-0">
                    {{ __('shipments.show.eta_evaluation_sla') }}
                </span>
                <span class="text-zinc-800 font-mono text-xs font-medium">{{ $shipment->delay_reason }}</span>
            </div>
            <span class="text-[11px] text-zinc-500 font-mono shrink-0">{{ __('shipments.show.active_sla_rule') }}</span>
        </div>
    @endif

    @php
        $activeException = $shipment->exceptions->firstWhere('status.value', 'OPEN')
            ?? $shipment->exceptions->firstWhere('status.value', 'INVESTIGATING');
    @endphp

    @if($activeException)
        <div class="p-5 rounded-[24px] bg-amber-50 border border-amber-300 text-amber-900 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl bg-amber-100 text-amber-700 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">{{ __('shipments.show.active_operational_exception') }}</span>
                        <span class="font-bold text-zinc-900 text-sm">{{ $activeException->type->label() }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                            {{ __('shipments.show.severity') }} {{ $activeException->severity->value }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white text-zinc-700 border border-zinc-200">
                            {{ $activeException->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-amber-900 mt-1.5 leading-relaxed">
                        {{ $activeException->description }}
                    </p>
                    <div class="text-[11px] text-amber-800 mt-2 font-medium">
                        {{ __('shipments.show.detected') }} {{ $activeException->detected_at->format('d M Y, H:i') }} UTC &bull; {{ __('shipments.show.logged_in_telemetry') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 4-Card NexCargo Bento Overview Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Bento Card 1: Corridor & Cargo Specs -->
        <div class="nex-card p-5 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#FAF7F9] text-[#1F171B] flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-[#1F171B]">{{ __('shipments.show.corridor_routing_waypoints') }}</h2>
                </div>

                <!-- Route Hatched Progress -->
                <div>
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">
                        <span class="text-[#C93565]">{{ __('shipments.show.origin_port') }}</span>
                        <span class="text-emerald-700">{{ __('shipments.show.destination_port') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-zinc-900 truncate max-w-[90px]" title="{{ $shipment->originLocation->name }}">{{ $shipment->originLocation->city }}</span>
                        <div class="flex-1 h-2.5 rounded-full route-track-hatch relative flex items-center justify-center">
                            <span class="w-5 h-5 rounded-full bg-[#1F171B] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-2.5 h-2.5 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                            </span>
                        </div>
                        <span class="text-xs font-bold text-zinc-900 truncate max-w-[90px] text-right" title="{{ $shipment->destinationLocation->name }}">{{ $shipment->destinationLocation->city }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-zinc-500 font-mono mt-1.5">
                        <span>{{ $shipment->expected_departure_at->format('d M, H:i') }}</span>
                        <span>{{ ($shipment->estimated_arrival_at ?? $shipment->expected_arrival_at)->format('d M, H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="nex-inset p-2.5">
                    <span class="text-zinc-500 block text-[10px]">{{ __('shipments.show.commodity') }}</span>
                    <span class="font-semibold text-zinc-900 block truncate" title="{{ $shipment->cargo_type }}">{{ $shipment->cargo_type }}</span>
                </div>
                <div class="nex-inset p-2.5">
                    <span class="text-zinc-500 block text-[10px]">{{ __('shipments.show.total_weight') }}</span>
                    <span class="font-semibold text-zinc-900 block font-mono tabular-nums">{{ number_format($shipment->weight_kg, 0) }} kg</span>
                </div>
                <div class="nex-inset p-2.5">
                    <span class="text-zinc-500 block text-[10px]">{{ __('shipments.show.volume') }}</span>
                    <span class="font-semibold text-zinc-900 block font-mono tabular-nums">{{ number_format($shipment->volume_cbm, 1) }} CBM</span>
                </div>
                <div class="nex-inset p-2.5">
                    <span class="text-zinc-500 block text-[10px]">{{ __('shipments.show.carrier') }}</span>
                    <span class="font-bold text-[#C93565] block font-mono">[{{ $shipment->carrier->code }}]</span>
                </div>
            </div>
        </div>

        <!-- Bento Card 2: Live Position & Connected Step Summary -->
        <div class="nex-card p-5 flex flex-col justify-between space-y-3">
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-[#FDF2F6] text-[#C93565] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h2 class="text-sm font-bold text-[#1F171B]">{{ __('shipments.show.current_position_hub') }}</h2>
                    </div>
                </div>

                <div class="relative pl-0.5 space-y-3 pt-1">
                    <div class="absolute left-[11px] top-3 bottom-3 w-px border-l border-dashed border-[#E6DCE1]"></div>

                    <div class="relative flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-[#E04879] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs shadow-[#E04879]/30 z-10">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[10px] text-zinc-400">{{ $latestEvent ? $latestEvent->event_type->label() : __('shipments.show.booking_initiated') }}</div>
                            <div class="text-xs font-bold text-zinc-900 truncate">{{ $currentLocation->name ?? __('shipments.show.port_of_departure') }}</div>
                            <div class="text-[11px] text-zinc-500 truncate">{{ $currentLocation->city ?? '' }}, {{ $currentLocation->country ?? '' }}</div>
                        </div>
                    </div>

                    <div class="relative flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-[#FAF7F9] text-zinc-600 flex items-center justify-center shrink-0 mt-0.5 z-10">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[10px] text-zinc-400">{{ __('shipments.show.origin_port') }}</div>
                            <div class="text-xs font-semibold text-zinc-800 truncate">{{ $shipment->originLocation->name }}</div>
                        </div>
                    </div>

                    <div class="relative flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-[#FAF7F9] text-zinc-600 flex items-center justify-center shrink-0 mt-0.5 z-10">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[10px] text-zinc-400">{{ __('shipments.show.destination_port') }}</div>
                            <div class="text-xs font-semibold text-zinc-800 truncate">{{ $shipment->destinationLocation->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nex-inset px-3 py-2 flex items-center justify-between text-[11px] font-mono text-zinc-600">
                <span>{{ __('shipments.show.last_milestone_update') }}</span>
                <span class="font-semibold text-zinc-900">{{ $latestEvent ? $latestEvent->occurred_at->format('d M, H:i') : __('shipments.show.pending_departure') }}</span>
            </div>
        </div>

        <!-- Bento Card 3: Consignee & Carrier Ownership -->
        <div class="nex-card p-5 flex flex-col justify-between space-y-3">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-[#FAF7F9] text-[#1F171B] flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-[#1F171B]">{{ __('shipments.show.consignee_ownership') }}</h2>
            </div>

            <div class="space-y-2.5">
                <div class="nex-inset p-3.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">{{ __('shipments.show.consignee_account') }}</span>
                    <div class="text-sm font-bold text-zinc-900 mt-0.5 truncate">{{ $shipment->customer->name }}</div>
                    <div class="text-[11px] text-zinc-500 truncate">{{ $shipment->customer->email }}</div>
                </div>
                <div class="nex-inset p-3.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#C93565]">{{ __('shipments.show.operating_carrier') }}</span>
                    <div class="text-sm font-bold text-zinc-900 mt-0.5 truncate">{{ $shipment->carrier->name }}</div>
                    <div class="text-[11px] text-zinc-500 truncate">{{ $shipment->carrier->contact_email ?? __('shipments.show.standard_dispatch') }}</div>
                </div>
            </div>
        </div>

        <!-- Bento Card 4: Live Meteorological Telemetry & Non-Causal Disclaimer -->
        <div class="nex-card p-5 flex flex-col justify-between space-y-3">
            <div>
                <div class="flex items-center justify-between pb-2.5 border-b border-[#F5F0F3]">
                    <span class="text-xs font-bold text-[#1F171B] uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#E04879]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        {{ __('shipments.show.current_weather') }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $weather['available'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#FAF7F9] text-zinc-600 border border-[#ECE4E8]' }}">
                        {{ $weather['available'] ? __('shipments.show.live') : __('shipments.show.offline') }}
                    </span>
                </div>

                @if($weather['available'])
                    <div class="mt-3 nex-inset p-3 space-y-2">
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-bold font-mono text-[#1F171B] tabular-nums">{{ $weather['temperature'] }}°C</span>
                            <span class="text-xs font-semibold text-[#C93565]">{{ $weather['condition'] }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#ECE4E8] text-[11px]">
                            <div>
                                <span class="text-zinc-500 block">{{ __('shipments.show.wind_velocity') }}</span>
                                <span class="font-mono text-zinc-800 font-semibold">{{ $weather['wind_speed'] }} km/h</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 block">{{ __('shipments.show.humidity') }}</span>
                                <span class="font-mono text-zinc-800 font-semibold">{{ $weather['humidity'] }}%</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-3 nex-inset p-3.5 text-center">
                        <p class="text-xs text-zinc-500 leading-relaxed">{{ $weather['message'] ?? __('shipments.show.weather_unavailable') }}</p>
                    </div>
                @endif
            </div>

            <!-- Mandatory Non-Causal Contextual Disclaimer -->
            <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-[10px] text-amber-900 leading-relaxed">
                <strong class="font-bold uppercase tracking-wider block text-amber-800 text-[9px] mb-0.5">{{ __('shipments.show.maritime_context_disclaimer') }}</strong>
                {{ $weather['disclaimer'] }}
            </div>
        </div>
    </div>

    <!-- Interactive OpenStreetMap Corridor Command Stage -->
    <div class="nex-card p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#F5F0F3]">
            <div>
                <h2 class="text-sm font-bold text-[#1F171B] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#E04879]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    {{ __('shipments.show.interactive_map_title') }}
                </h2>
                <p class="text-xs text-zinc-500 mt-0.5">{{ __('shipments.show.interactive_map_desc') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-1.5">
                @if($mapData['has_coordinates'])
                    <button type="button" id="corridor-focus-origin-btn" class="px-3 py-1 rounded-full text-[11px] font-semibold bg-white hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 transition cursor-pointer">
                        Origin
                    </button>
                    <button type="button" id="corridor-focus-current-btn" class="px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FDF2F6] hover:bg-[#FCE5EE] border border-[#F8C9DC] text-[#9E224B] transition cursor-pointer">
                        Current Position
                    </button>
                    <button type="button" id="corridor-focus-dest-btn" class="px-3 py-1 rounded-full text-[11px] font-semibold bg-white hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 transition cursor-pointer">
                        Destination
                    </button>
                    <button type="button" id="corridor-reset-zoom-btn" class="px-3 py-1 rounded-full text-[11px] font-semibold bg-[#1F171B] hover:bg-[#C93565] text-white transition cursor-pointer">
                        Fit Route
                    </button>
                @endif
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-semibold bg-[#FAF7F9] border border-[#ECE4E8] text-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#E04879]"></span> {{ __('shipments.show.osm_tiles') }}
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-[#ECE4E8] overflow-hidden relative min-h-[430px] flex flex-col">
            @if($mapData['has_coordinates'])
                <div id="shipment-corridor-map" class="w-full flex-grow min-h-[430px] z-0"></div>
            @else
                <div class="w-full flex-grow flex flex-col items-center justify-center p-8 text-center bg-[#FAF7F9] min-h-[320px]">
                    <div class="w-12 h-12 rounded-2xl bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-zinc-900">{{ __('shipments.show.coords_unavailable_title') }}</h4>
                    <p class="text-xs text-zinc-500 max-w-sm mt-1 leading-relaxed">
                        {{ __('shipments.show.coords_unavailable_desc') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Asymmetric 12-Col Operations Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left 7 Cols: Chronological Timeline & Cargo Manifest -->
        <div class="lg:col-span-7 space-y-6">
            <div id="milestone-timeline" class="nex-card p-6 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F5F0F3]">
                    <div>
                        <h2 class="text-base font-bold text-[#1F171B]">{{ __('shipments.show.timeline_title') }}</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">{{ __('shipments.show.timeline_desc') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-[#FAF7F9] border border-[#ECE4E8] text-xs text-zinc-600 font-mono">{{ __('shipments.show.milestones_logged', ['count' => $shipment->trackingEvents->count()]) }}</span>
                </div>

                <div class="relative border-l-2 border-[#ECE4E8] ml-4 space-y-4 pt-1">
                    @forelse($shipment->trackingEvents as $event)
                        <div class="relative pl-7">
                            @if($event->is_exception)
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-red-600 border-2 border-white shadow-sm"></div>
                            @else
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-[#E04879] border-2 border-white shadow-sm"></div>
                            @endif

                            <div
                                @if($event->latitude && $event->longitude)
                                    onclick="window.focusCorridorPoint({{ (float) $event->latitude }}, {{ (float) $event->longitude }})"
                                    role="button"
                                    tabindex="0"
                                    title="Focus map on this milestone"
                                @endif
                                class="p-4 rounded-2xl border transition {{ $event->latitude && $event->longitude ? 'cursor-pointer hover:border-[#F29EC0]' : '' }} {{ $event->is_exception ? 'bg-red-50/50 border-red-200 text-red-900' : 'bg-[#FAF7F9] border-[#ECE4E8] text-zinc-800' }}"
                            >
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold uppercase tracking-wider {{ $event->is_exception ? 'text-red-700' : 'text-[#C93565]' }}">
                                            {{ $event->event_type->label() }}
                                        </span>
                                        @if($event->location)
                                            <span class="text-xs font-semibold text-zinc-800">&bull; {{ $event->location->name }} ({{ $event->location->city }}, {{ $event->location->country }})</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] font-mono text-zinc-500">
                                        {{ $event->occurred_at->format('d M Y, H:i') }} UTC
                                    </span>
                                </div>

                                <p class="text-xs mt-2 leading-relaxed {{ $event->is_exception ? 'text-red-800' : 'text-zinc-600' }}">
                                    {{ $event->description }}
                                </p>

                                @if($event->latitude && $event->longitude)
                                    <div class="mt-2 text-[11px] font-mono text-zinc-500 flex items-center gap-3">
                                        <span>GPS: {{ number_format($event->latitude, 6) }}, {{ number_format($event->longitude, 6) }}</span>
                                        <a
                                            href="https://www.openstreetmap.org/?mlat={{ $event->latitude }}&mlon={{ $event->longitude }}#map=12/{{ $event->latitude }}/{{ $event->longitude }}"
                                            target="_blank"
                                            rel="noreferrer"
                                            onclick="event.stopPropagation()"
                                            class="text-[#C93565] hover:underline font-semibold"
                                        >
                                            {{ __('shipments.show.open_in_osm') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="pl-6 text-xs text-zinc-500">
                            {{ __('shipments.show.no_milestones') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cargo Manifest Items Table -->
            <div class="nex-card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#1F171B] uppercase tracking-wider">{{ __('shipments.show.manifest_title', ['count' => $shipment->items->count()]) }}</h2>
                    <span class="text-xs text-zinc-500">{{ __('shipments.show.total_weight') }}: <strong class="text-zinc-900 font-mono">{{ number_format($shipment->weight_kg, 2) }} kg</strong></span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-[#ECE4E8]">
                    <table class="w-full text-left text-xs text-zinc-700">
                        <thead class="bg-[#FAF7F9] text-zinc-600 uppercase text-[10px] font-semibold border-b border-[#ECE4E8]">
                            <tr>
                                <th class="px-4 py-2.5">{{ __('shipments.show.col_number') }}</th>
                                <th class="px-4 py-2.5">{{ __('shipments.show.col_description') }}</th>
                                <th class="px-4 py-2.5 text-center">{{ __('shipments.show.col_quantity') }}</th>
                                <th class="px-4 py-2.5 text-right">{{ __('shipments.show.col_net_weight') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F0F3]">
                            @forelse($shipment->items as $idx => $item)
                                <tr class="hover:bg-[#FDF2F6]/40 transition">
                                    <td class="px-4 py-3 font-mono text-zinc-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold text-zinc-900">{{ $item->description }}</td>
                                    <td class="px-4 py-3 text-center font-mono font-semibold text-zinc-700 tabular-nums">{{ number_format($item->quantity) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-zinc-800 tabular-nums">{{ number_format($item->weight_kg, 2) }} kg</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-zinc-500">{{ __('shipments.show.no_line_items') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 5 Cols: Operator Milestone Form & Exceptions Console -->
        <div class="lg:col-span-5 space-y-6">
            @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
                <div class="nex-card p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#F5F0F3]">
                        <div>
                            <h2 class="text-base font-bold text-[#1F171B]">{{ __('shipments.show.log_milestone_title') }}</h2>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ __('shipments.show.log_milestone_desc') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] whitespace-nowrap shrink-0">
                            {{ __('shipments.show.operator_console') }}
                        </span>
                    </div>

                    @if($shipment->current_status === \App\Enums\ShipmentStatus::Delivered)
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ __('shipments.show.delivered_terminal_notice') }}</span>
                        </div>
                    @else
                        <form method="POST" action="{{ route('shipments.events.store', $shipment) }}" class="space-y-3.5">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="status" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.target_lifecycle_status') }}</label>
                                    <select id="status" name="status" required class="nex-input">
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->value }}" {{ old('status', $shipment->current_status->value) === $st->value ? 'selected' : '' }}>
                                                {{ $st->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="event_type" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.milestone_event_type') }}</label>
                                    <select id="event_type" name="event_type" required class="nex-input">
                                        @foreach($eventTypes as $et)
                                            <option value="{{ $et->value }}" {{ old('event_type') === $et->value ? 'selected' : '' }}>
                                                {{ $et->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('event_type') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="location_id" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.waypoint_hub_port') }}</label>
                                    <select id="location_id" name="location_id" class="nex-input">
                                        <option value="">{{ __('shipments.show.select_waypoint') }}</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                                {{ $loc->name }} ({{ $loc->city }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="occurred_at" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.event_timestamp') }}</label>
                                    <input type="datetime-local" id="occurred_at" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required class="nex-input" />
                                    @error('occurred_at') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="latitude" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.latitude_opt') }}</label>
                                    <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="e.g. 1.2644120" class="nex-input font-mono" />
                                </div>

                                <div>
                                    <label for="longitude" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.longitude_opt') }}</label>
                                    <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="e.g. 103.8222180" class="nex-input font-mono" />
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.milestone_desc') }}</label>
                                <textarea id="description" name="description" rows="2" required placeholder="{{ __('shipments.show.milestone_desc_placeholder') }}" class="nex-input">{{ old('description') }}</textarea>
                                @error('description') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-1">
                                <label class="flex items-center gap-2 text-xs text-zinc-700 cursor-pointer">
                                    <input type="checkbox" name="is_exception" value="1" {{ old('is_exception') ? 'checked' : '' }} class="w-4 h-4 rounded border-[#E6DCE1] text-red-600 focus:ring-red-500/20" />
                                    {{ __('shipments.show.mark_exception_event') }}
                                </label>
                                <button type="submit" class="px-5 py-2.5 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-xs font-semibold shadow-sm transition cursor-pointer shrink-0">
                                    {{ __('shipments.show.record_milestone_button') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif

            <!-- Operational Exceptions & Incident Management -->
            <div class="nex-card p-6 space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-[#1F171B] uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            {{ __('shipments.show.exceptions_title', ['count' => $shipment->exceptions->count()]) }}
                        </h2>
                        <p class="text-xs text-zinc-500 mt-0.5">{{ __('shipments.show.exceptions_subtitle') }}</p>
                    </div>
                    @if($shipment->exceptions->whereIn('status.value', ['OPEN', 'INVESTIGATING'])->isNotEmpty())
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                            {{ __('shipments.show.active_disruptions') }}
                        </span>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($shipment->exceptions as $exception)
                        <div class="nex-inset p-4 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white text-zinc-800 border border-[#ECE4E8]">
                                        {{ $exception->type->label() }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $exception->severity->badgeClasses() }}">
                                        {{ __('shipments.show.severity') }} {{ $exception->severity->value }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $exception->status->badgeClasses() }}">
                                        {{ __('common.labels.status') }}: {{ $exception->status->label() }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-zinc-500 font-mono">
                                    {{ __('shipments.show.detected') }} {{ $exception->detected_at->format('d M Y, H:i') }}
                                    @if($exception->resolved_at)
                                        &bull; <span class="text-emerald-700 font-semibold">{{ __('shipments.show.resolved_at', ['time' => $exception->resolved_at->format('d M Y, H:i')]) }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-xs text-zinc-700 whitespace-pre-line leading-relaxed">
                                {{ $exception->description }}
                            </div>

                            @if((auth()->user()->isAdmin() || auth()->user()->isOperator()) && $exception->status !== \App\Enums\ExceptionStatus::Resolved)
                                <div class="pt-3 border-t border-[#ECE4E8] space-y-2">
                                    <span class="text-[11px] text-zinc-600 font-medium block">{{ __('shipments.show.update_incident_lifecycle') }}</span>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($exception->status === \App\Enums\ExceptionStatus::Open)
                                            <form method="POST" action="{{ route('exceptions.status.update', $exception) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ \App\Enums\ExceptionStatus::Investigating->value }}">
                                                <button type="submit" class="px-3 py-1.5 rounded-full bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold transition cursor-pointer">
                                                    {{ __('shipments.show.mark_investigating') }}
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('exceptions.status.update', $exception) }}" class="flex flex-1 items-center gap-2 min-w-[220px]">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ \App\Enums\ExceptionStatus::Resolved->value }}">
                                            <input type="text" name="resolution_notes" placeholder="{{ __('shipments.show.resolution_notes_placeholder') }}" required
                                                class="flex-1 px-3 py-1.5 rounded-full bg-white border border-[#ECE4E8] text-xs text-zinc-800 placeholder-zinc-400 focus:outline-none focus:border-emerald-500">
                                            <button type="submit" class="px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition cursor-pointer shrink-0">
                                                {{ __('shipments.show.resolve_and_clear') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="nex-inset p-5 text-center text-xs text-zinc-500">
                            {{ __('shipments.show.no_exceptions') }}
                        </div>
                    @endforelse
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
                    <div class="pt-3 border-t border-[#F5F0F3]">
                        <details class="group">
                            <summary class="flex items-center justify-between cursor-pointer text-xs font-bold uppercase tracking-wider text-[#9E224B] hover:text-[#C93565]">
                                <span>{{ __('shipments.show.flag_new_exception') }}</span>
                                <span class="text-zinc-400 group-open:rotate-180 transition">&darr;</span>
                            </summary>
                            <form action="{{ route('shipments.exceptions.store', $shipment) }}" method="POST" class="mt-4 nex-inset p-4 space-y-3">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label for="exc_type" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.exception_category') }}</label>
                                        <select id="exc_type" name="type" required class="nex-input bg-white">
                                            @foreach(\App\Enums\ExceptionType::cases() as $typeCase)
                                                <option value="{{ $typeCase->value }}">{{ $typeCase->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="exc_severity" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.severity_level') }}</label>
                                            <select id="exc_severity" name="severity" required class="nex-input bg-white">
                                                @foreach(\App\Enums\ExceptionSeverity::cases() as $severityCase)
                                                    <option value="{{ $severityCase->value }}" {{ $severityCase === \App\Enums\ExceptionSeverity::High ? 'selected' : '' }}>
                                                        {{ $severityCase->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="exc_detected_at" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.detected_at_label') }}</label>
                                            <input type="datetime-local" id="exc_detected_at" name="detected_at" value="{{ now()->format('Y-m-d\TH:i') }}" class="nex-input bg-white">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="exc_description" class="block text-xs font-semibold text-zinc-700 mb-1">{{ __('shipments.show.incident_desc_label') }}</label>
                                    <textarea id="exc_description" name="description" rows="2" required placeholder="{{ __('shipments.show.incident_desc_placeholder') }}" class="nex-input bg-white"></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-5 py-2 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white font-semibold text-xs shadow-sm transition cursor-pointer">
                                        {{ __('shipments.show.log_exception_button') }}
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapData = @json($mapData);

    if (!mapData.has_coordinates || !document.getElementById('shipment-corridor-map')) {
        return;
    }

    const LRef = window.L;
    if (!LRef) return;

    const map = LRef.map('shipment-corridor-map', window.SMOOTH_MAP_OPTIONS || {
        zoomControl: true,
        scrollWheelZoom: true,
        zoomSnap: 0.1,
        zoomDelta: 0.5,
        wheelPxPerZoomLevel: 85,
        wheelDebounceTime: 20,
        zoomAnimation: true,
        fadeAnimation: true,
        markerZoomAnimation: true,
        inertia: true,
        inertiaDeceleration: 2800
    });

    LRef.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        keepBuffer: 6,
        updateWhenZooming: false,
        updateWhenIdle: false,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer" class="text-[#C93565] hover:underline font-medium">OpenStreetMap</a> contributors'
    }).addTo(map);

    const latlngs = [];
    const bounds = [];
    const markerRegistry = [];

    function createMapPin(bgColor, labelText, isPulse = false) {
        const pulseHtml = isPulse
            ? `<span style="background-color: ${bgColor};" class="absolute -inset-1.5 rounded-full opacity-35 animate-ping"></span>`
            : '';
        return LRef.divIcon({
            className: 'custom-leaflet-marker',
            html: `<div class="relative flex items-center justify-center">
                ${pulseHtml}
                <div style="background-color: ${bgColor};" class="w-7 h-7 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-[10px] font-bold text-white font-mono transition-transform hover:scale-110">${labelText}</div>
            </div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14]
        });
    }

    const escapeHtml = typeof window.escapeHtml === 'function'
        ? window.escapeHtml
        : function (str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        };

    const curveFn = typeof window.buildCurvedSegment === 'function'
        ? window.buildCurvedSegment
        : (a, b) => [a, b];

    let originPoint = null;
    let currentPoint = null;
    let destPoint = null;

    if (mapData.origin) {
        originPoint = [mapData.origin.lat, mapData.origin.lng];
        latlngs.push(originPoint);
        bounds.push(originPoint);

        const originMarker = LRef.marker(originPoint, {
            icon: createMapPin('#1F171B', 'O', false)
        }).addTo(map);

        originMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#EC6E9B] block">${escapeHtml(window.i18n?.leaflet?.origin || 'Port of Origin')}</span>
                <strong class="text-white text-sm block">${escapeHtml(mapData.origin.name)}</strong>
                <span class="text-zinc-300 block">${escapeHtml(mapData.origin.city)}, ${escapeHtml(mapData.origin.country)}</span>
                <span class="font-mono text-[10px] text-zinc-400 block">${escapeHtml(window.i18n?.leaflet?.coordinates || 'GPS')}: ${mapData.origin.lat.toFixed(4)}, ${mapData.origin.lng.toFixed(4)}</span>
            </div>
        `);

        originMarker.on('click', () => {
            map.flyTo(originPoint, Math.max(map.getZoom(), 7), { animate: true, duration: 0.85, easeLinearity: 0.25 });
        });
        markerRegistry.push({ point: originPoint, marker: originMarker });
    }

    if (mapData.waypoints && mapData.waypoints.length > 0) {
        mapData.waypoints.forEach((wp, index) => {
            const wpPoint = [wp.lat, wp.lng];
            latlngs.push(wpPoint);
            bounds.push(wpPoint);
            currentPoint = wpPoint;

            const isLatest = index === mapData.waypoints.length - 1;
            const color = wp.is_exception ? '#DC2626' : '#E04879';
            const marker = LRef.marker(wpPoint, {
                icon: createMapPin(color, (index + 1).toString(), isLatest)
            }).addTo(map);

            marker.bindPopup(`
                <div class="text-xs space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider ${wp.is_exception ? 'text-red-400' : 'text-[#EC6E9B]'} block">
                        Milestone #${index + 1}: ${escapeHtml(wp.status)}
                    </span>
                    <strong class="text-white block">${escapeHtml(wp.location_name)}</strong>
                    <p class="text-zinc-300 text-[11px] leading-relaxed">${escapeHtml(wp.description)}</p>
                    <span class="font-mono text-[10px] text-zinc-400 block mt-1">Recorded: ${escapeHtml(wp.occurred_at)}</span>
                </div>
            `);

            marker.on('click', () => {
                map.flyTo(wpPoint, Math.max(map.getZoom(), 7), { animate: true, duration: 0.85, easeLinearity: 0.25 });
            });
            markerRegistry.push({ point: wpPoint, marker });
        });
    }

    if (mapData.destination) {
        destPoint = [mapData.destination.lat, mapData.destination.lng];
        bounds.push(destPoint);

        const destMarker = LRef.marker(destPoint, {
            icon: createMapPin('#10B981', 'D', false)
        }).addTo(map);

        destMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block">${escapeHtml(window.i18n?.leaflet?.destination || 'Port of Destination')}</span>
                <strong class="text-white text-sm block">${escapeHtml(mapData.destination.name)}</strong>
                <span class="text-zinc-300 block">${escapeHtml(mapData.destination.city)}, ${escapeHtml(mapData.destination.country)}</span>
                <span class="font-mono text-[10px] text-zinc-400 block">${escapeHtml(window.i18n?.leaflet?.coordinates || 'GPS')}: ${mapData.destination.lat.toFixed(4)}, ${mapData.destination.lng.toFixed(4)}</span>
            </div>
        `);

        destMarker.on('click', () => {
            map.flyTo(destPoint, Math.max(map.getZoom(), 7), { animate: true, duration: 0.85, easeLinearity: 0.25 });
        });
        markerRegistry.push({ point: destPoint, marker: destMarker });
    }

    // Curved Polyline Transit Corridor
    for (let i = 0; i < latlngs.length - 1; i++) {
        const arc = curveFn(latlngs[i], latlngs[i + 1], 0.12, 24);
        LRef.polyline(arc.length > 1 ? arc : [latlngs[i], latlngs[i + 1]], {
            color: '#1F171B',
            weight: 3,
            opacity: 0.9,
            lineJoin: 'round'
        }).addTo(map);
    }

    if (destPoint && latlngs.length >= 1) {
        const lastActive = latlngs[latlngs.length - 1];
        const remArc = curveFn(lastActive, destPoint, -0.12, 24);
        LRef.polyline(remArc.length > 1 ? remArc : [lastActive, destPoint], {
            color: '#E04879',
            weight: 2.5,
            opacity: 0.85,
            dashArray: '6, 8',
            lineJoin: 'round'
        }).addTo(map);
    }

    function fitCorridor(animate = false) {
        if (bounds.length > 0) {
            if (animate) {
                map.flyToBounds(bounds, { padding: [50, 50], maxZoom: 7, duration: 0.9 });
            } else {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 7 });
            }
        } else {
            map.setView([15, 60], 3);
        }
    }

    fitCorridor(false);

    window.focusCorridorPoint = function (lat, lng) {
        map.flyTo([lat, lng], 7, { animate: true, duration: 0.85, easeLinearity: 0.25 });
        const found = markerRegistry.find(item => Math.abs(item.point[0] - lat) < 0.001 && Math.abs(item.point[1] - lng) < 0.001);
        if (found) {
            setTimeout(() => found.marker.openPopup(), 450);
        }
        document.getElementById('shipment-corridor-map')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    document.getElementById('corridor-reset-zoom-btn')?.addEventListener('click', () => fitCorridor(true));
    document.getElementById('corridor-focus-origin-btn')?.addEventListener('click', () => {
        if (originPoint) window.focusCorridorPoint(originPoint[0], originPoint[1]);
    });
    document.getElementById('corridor-focus-current-btn')?.addEventListener('click', () => {
        const pt = currentPoint || originPoint;
        if (pt) window.focusCorridorPoint(pt[0], pt[1]);
    });
    document.getElementById('corridor-focus-dest-btn')?.addEventListener('click', () => {
        if (destPoint) window.focusCorridorPoint(destPoint[0], destPoint[1]);
    });
});
</script>
@endpush
