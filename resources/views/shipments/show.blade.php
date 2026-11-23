@extends('layouts.app')

@section('title', __('shipments.details_title') . ' ' . $shipment->shipment_number)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Custom Leaflet styling for light theme */
    .leaflet-popup-content-wrapper {
        background: #ffffff !important;
        color: #1e293b !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
    }
    .leaflet-popup-tip {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }
    .leaflet-container {
        font-family: inherit !important;
        background: #f8fafc !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header with Status Badges -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight text-slate-900">
                    {{ $shipment->shipment_number }}
                </h1>

                <!-- Current Status Badge -->
                @php
                    $statusColors = [
                        'CREATED' => 'bg-slate-100 text-slate-700 border-slate-300',
                        'BOOKED' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'PICKED_UP' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'IN_TRANSIT' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'DELIVERED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $badge = $statusColors[$shipment->current_status->value] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $badge }}">
                    {{ $shipment->current_status->label() }}
                </span>

                <!-- Delay Status Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $shipment->delay_status->badgeClasses() }}" title="{{ $shipment->delay_reason ?? __('shipments.show.on_schedule') }}">
                    @if($shipment->delay_status === \App\Enums\DelayStatus::OnTime)
                        {{ __('shipments.show.on_schedule') }}
                    @else
                        {{ $shipment->delay_status->label() }} ({{ $shipment->delay_hours }}h)
                    @endif
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1.5">
                {{ __('shipments.show.contracted_carrier') }} <span class="text-slate-800 font-semibold">{{ $shipment->carrier->name }}</span> &bull; {{ __('shipments.show.booked_by') }} {{ $shipment->creator->name }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">{{ __('shipments.show.back_shipments') }}</a>
            @can('update', $shipment)
                <a href="{{ route('shipments.edit', $shipment) }}" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition">
                    {{ __('shipments.show.edit_booking') }}
                </a>
            @endcan
            @can('delete', $shipment)
                <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" onsubmit="return confirm('{{ __('shipments.show.delete_confirm', ['number' => $shipment->shipment_number]) }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition cursor-pointer">
                        {{ __('common.actions.delete') }}
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if($shipment->delay_reason)
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200 shrink-0">
                    {{ __('shipments.show.eta_evaluation_sla') }}
                </span>
                <span class="text-slate-800 font-mono text-xs font-medium">{{ $shipment->delay_reason }}</span>
            </div>
            <span class="text-[11px] text-slate-500 font-mono shrink-0">{{ __('shipments.show.active_sla_rule') }}</span>
        </div>
    @endif

    <!-- Active Exception Alert -->
    @php
        $activeException = $shipment->exceptions->firstWhere('status.value', 'OPEN')
            ?? $shipment->exceptions->firstWhere('status.value', 'INVESTIGATING');
    @endphp

    @if($activeException)
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl bg-amber-100 text-amber-700 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">{{ __('shipments.show.active_operational_exception') }}</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $activeException->type->label() }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                            {{ __('shipments.show.severity') }} {{ $activeException->severity->value }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white text-slate-700 border border-slate-200">
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

    <!-- Current Location & Live Position Telemetry Card -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">{{ __('shipments.show.current_position_hub') }}</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">
                    {{ $currentLocation->name ?? __('shipments.show.port_of_departure') }}
                </div>
                <div class="text-xs text-slate-500">
                    {{ $currentLocation->city ?? '' }}, {{ $currentLocation->country ?? '' }}
                    @if($latestEvent && $latestEvent->latitude && $latestEvent->longitude)
                        &bull; <span class="font-mono text-slate-700">WGS84: {{ number_format($latestEvent->latitude, 6) }}, {{ number_format($latestEvent->longitude, 6) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-right sm:border-l sm:border-slate-200 sm:pl-6">
            <span class="text-[10px] uppercase tracking-wider text-slate-500 block">{{ __('shipments.show.last_milestone_update') }}</span>
            <span class="text-xs font-mono font-semibold text-slate-800 mt-0.5 block">
                {{ $latestEvent ? $latestEvent->occurred_at->format('d M Y, H:i') . ' UTC' : __('shipments.show.pending_departure') }}
            </span>
            <span class="text-[11px] text-blue-600 font-semibold block mt-0.5">
                {{ $latestEvent ? $latestEvent->event_type->label() : __('shipments.show.booking_initiated') }}
            </span>
        </div>
    </div>

    <!-- Route Overview Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Route -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">{{ __('shipments.show.corridor_routing_waypoints') }}</h2>

            <div class="flex items-center justify-between relative">
                <!-- Origin -->
                <div class="text-left z-10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">{{ __('shipments.show.origin_port') }}</span>
                    <div class="text-lg font-bold text-slate-900 mt-0.5">{{ $shipment->originLocation->city }}</div>
                    <div class="text-xs text-slate-500">{{ $shipment->originLocation->name }}</div>
                    <div class="text-[11px] text-slate-600 mt-1 font-mono">
                        {{ __('shipments.show.etd') }} {{ $shipment->expected_departure_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <!-- Mid-route connector -->
                <div class="flex-grow mx-6 flex flex-col items-center">
                    <div class="w-full border-t border-dashed border-slate-300 relative flex items-center justify-center">
                        <div class="w-7 h-7 rounded-full bg-slate-50 border border-slate-300 flex items-center justify-center text-blue-600 -mt-0.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 font-medium">{{ __('shipments.show.ocean_transit_corridor') }}</span>
                </div>

                <!-- Destination -->
                <div class="text-right z-10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">{{ __('shipments.show.destination_port') }}</span>
                    <div class="text-lg font-bold text-slate-900 mt-0.5">{{ $shipment->destinationLocation->city }}</div>
                    <div class="text-xs text-slate-500">{{ $shipment->destinationLocation->name }}</div>
                    <div class="text-[11px] text-slate-600 mt-1 font-mono">
                        {{ __('shipments.show.eta') }} {{ $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y, H:i') : $shipment->expected_arrival_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <!-- Specs Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]">{{ __('shipments.show.commodity') }}</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block truncate" title="{{ $shipment->cargo_type }}">{{ $shipment->cargo_type }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]">{{ __('shipments.show.total_weight') }}</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block font-mono">{{ number_format($shipment->weight_kg, 2) }} kg</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]">{{ __('shipments.show.volume') }}</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block font-mono">{{ number_format($shipment->volume_cbm, 2) }} CBM</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]">{{ __('shipments.show.carrier') }}</span>
                    <span class="font-semibold text-blue-600 mt-0.5 block font-mono">[{{ $shipment->carrier->code }}]</span>
                </div>
            </div>
        </div>

        <!-- Consignee Profile Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">{{ __('shipments.show.consignee_ownership') }}</h2>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">{{ __('shipments.show.consignee_account') }}</span>
                <div class="text-sm font-bold text-slate-900 mt-1">{{ $shipment->customer->name }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $shipment->customer->email }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">{{ __('shipments.show.operating_carrier') }}</span>
                <div class="text-sm font-bold text-slate-900 mt-1">{{ $shipment->carrier->name }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $shipment->carrier->contact_email ?? __('shipments.show.standard_dispatch') }}</div>
            </div>
        </div>
    </div>

    <!-- Maritime Corridor Map & Live Meteorological Telemetry -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    {{ __('shipments.show.interactive_map_title') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('shipments.show.interactive_map_desc') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-semibold bg-slate-100 border border-slate-200 text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ __('shipments.show.osm_tiles') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Map Container (3 Cols) -->
            <div class="lg:col-span-3 rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-sm relative min-h-[420px] flex flex-col">
                @if($mapData['has_coordinates'])
                    <div id="shipment-corridor-map" class="w-full flex-grow min-h-[420px] z-0"></div>
                @else
                    <div class="w-full flex-grow flex flex-col items-center justify-center p-8 text-center bg-slate-50 min-h-[320px]">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">{{ __('shipments.show.coords_unavailable_title') }}</h4>
                        <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed">
                            {{ __('shipments.show.coords_unavailable_desc') }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Weather Card (1 Col) -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200 flex flex-col justify-between shadow-sm space-y-4">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                            {{ __('shipments.show.current_weather') }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $weather['available'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                            {{ $weather['available'] ? __('shipments.show.live') : __('shipments.show.offline') }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 block font-semibold">{{ __('shipments.show.verified_hub_loc') }}</span>
                        <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $currentLocation->name ?? __('shipments.show.port_of_departure') }}</div>
                        <div class="text-xs text-slate-500">{{ $currentLocation->city ?? '' }}, {{ $currentLocation->country ?? '' }}</div>
                    </div>

                    @if($weather['available'])
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-baseline justify-between">
                                <span class="text-2xl font-bold font-mono text-slate-900">{{ $weather['temperature'] }}°C</span>
                                <span class="text-xs font-semibold text-blue-600">{{ $weather['condition'] }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block">{{ __('shipments.show.wind_velocity') }}</span>
                                    <span class="font-mono text-slate-800 font-semibold">{{ $weather['wind_speed'] }} km/h</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">{{ __('shipments.show.humidity') }}</span>
                                    <span class="font-mono text-slate-800 font-semibold">{{ $weather['humidity'] }}%</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $weather['message'] ?? __('shipments.show.weather_unavailable') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Mandatory Non-Causal Contextual Disclaimer -->
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[10px] text-amber-900 leading-relaxed">
                    <strong class="font-bold uppercase tracking-wider block text-amber-800 text-[9px] mb-0.5">{{ __('shipments.show.maritime_context_disclaimer') }}</strong>
                    {{ $weather['disclaimer'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Chronological Timeline Section -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-base font-bold text-slate-900">{{ __('shipments.show.timeline_title') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('shipments.show.timeline_desc') }}</p>
            </div>
            <span class="text-xs text-slate-500 font-mono">{{ __('shipments.show.milestones_logged', ['count' => $shipment->trackingEvents->count()]) }}</span>
        </div>

        <div class="relative border-l-2 border-slate-200 ml-4 space-y-6 pt-2">
            @forelse($shipment->trackingEvents as $event)
                <div class="relative pl-7">
                    <!-- Milestone Marker Node -->
                    @if($event->is_exception)
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-sm"></div>
                    @else
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-600 border-2 border-white shadow-sm"></div>
                    @endif

                    <!-- Event Card Container -->
                    <div class="p-4 rounded-xl border {{ $event->is_exception ? 'bg-rose-50/50 border-rose-200 text-rose-900' : 'bg-slate-50 border-slate-200 text-slate-800' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $event->is_exception ? 'text-rose-700' : 'text-blue-600' }}">
                                    {{ $event->event_type->label() }}
                                </span>
                                @if($event->location)
                                    <span class="text-xs font-semibold text-slate-800">&bull; {{ $event->location->name }} ({{ $event->location->city }}, {{ $event->location->country }})</span>
                                @endif
                            </div>
                            <span class="text-[11px] font-mono text-slate-500">
                                {{ $event->occurred_at->format('d M Y, H:i') }} UTC
                            </span>
                        </div>

                        <p class="text-xs mt-2 leading-relaxed {{ $event->is_exception ? 'text-rose-800' : 'text-slate-600' }}">
                            {{ $event->description }}
                        </p>

                        @if($event->latitude && $event->longitude)
                            <div class="mt-2 text-[11px] font-mono text-slate-500 flex items-center gap-3">
                                <span>GPS: {{ number_format($event->latitude, 6) }}, {{ number_format($event->longitude, 6) }}</span>
                                <a
                                    href="https://www.openstreetmap.org/?mlat={{ $event->latitude }}&mlon={{ $event->longitude }}#map=12/{{ $event->latitude }}/{{ $event->longitude }}"
                                    target="_blank"
                                    rel="noreferrer"
                                    class="text-blue-600 hover:text-blue-800 hover:underline font-semibold"
                                >
                                    {{ __('shipments.show.open_in_osm') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="pl-6 text-xs text-slate-500">
                    {{ __('shipments.show.no_milestones') }}
                </div>
            @endforelse
        </div>
    </div>

    <!-- Form: Add Tracking Event (Admin & Operator Only) -->
    @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ __('shipments.show.log_milestone_title') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ __('shipments.show.log_milestone_desc') }}
                    </p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                    {{ __('shipments.show.operator_console') }}
                </span>
            </div>

            @if($shipment->current_status === \App\Enums\ShipmentStatus::Delivered)
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ __('shipments.show.delivered_terminal_notice') }}</span>
                </div>
            @else
                <form method="POST" action="{{ route('shipments.events.store', $shipment) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Target Status -->
                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.target_lifecycle_status') }}</label>
                            <select id="status" name="status" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" {{ old('status', $shipment->current_status->value) === $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.milestone_event_type') }}</label>
                            <select id="event_type" name="event_type" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                                @foreach($eventTypes as $et)
                                    <option value="{{ $et->value }}" {{ old('event_type') === $et->value ? 'selected' : '' }}>
                                        {{ $et->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_type') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location_id" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.waypoint_hub_port') }}</label>
                            <select id="location_id" name="location_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                                <option value="">{{ __('shipments.show.select_waypoint') }}</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }} ({{ $loc->city }})
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Occurred At -->
                        <div>
                            <label for="occurred_at" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.event_timestamp') }}</label>
                            <input
                                type="datetime-local"
                                id="occurred_at"
                                name="occurred_at"
                                value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                            @error('occurred_at') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.latitude_opt') }}</label>
                            <input
                                type="number"
                                step="0.0000001"
                                id="latitude"
                                name="latitude"
                                value="{{ old('latitude') }}"
                                placeholder="e.g. 1.2644120"
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.longitude_opt') }}</label>
                            <input
                                type="number"
                                step="0.0000001"
                                id="longitude"
                                name="longitude"
                                value="{{ old('longitude') }}"
                                placeholder="e.g. 103.8222180"
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.milestone_desc') }}</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="2"
                            required
                            placeholder="{{ __('shipments.show.milestone_desc_placeholder') }}"
                            class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                        >{{ old('description') }}</textarea>
                        @error('description') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Is Exception Flag -->
                    <div>
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_exception"
                                value="1"
                                {{ old('is_exception') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500/20"
                            />
                            {{ __('shipments.show.mark_exception_event') }}
                        </label>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer"
                        >
                            {{ __('shipments.show.record_milestone_button') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    <!-- Operational Exceptions & Incident Management -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ __('shipments.show.exceptions_title', ['count' => $shipment->exceptions->count()]) }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('shipments.show.exceptions_subtitle') }}</p>
            </div>
            @if($shipment->exceptions->whereIn('status.value', ['OPEN', 'INVESTIGATING'])->isNotEmpty())
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                    {{ __('shipments.show.active_disruptions') }}
                </span>
            @endif
        </div>

        <!-- Exceptions List -->
        <div class="space-y-4">
            @forelse($shipment->exceptions as $exception)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-white text-slate-800 border border-slate-300">
                                {{ $exception->type->label() }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $exception->severity->badgeClasses() }}">
                                {{ __('shipments.show.severity') }} {{ $exception->severity->value }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $exception->status->badgeClasses() }}">
                                {{ __('common.labels.status') }}: {{ $exception->status->label() }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono">
                            {{ __('shipments.show.detected') }} {{ $exception->detected_at->format('d M Y, H:i') }}
                            @if($exception->resolved_at)
                                &bull; <span class="text-emerald-700 font-semibold">{{ __('shipments.show.resolved_at', ['time' => $exception->resolved_at->format('d M Y, H:i')]) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                        {{ $exception->description }}
                    </div>

                    <!-- Operational State Transition Controls (Operator / Admin Only) -->
                    @if((auth()->user()->isAdmin() || auth()->user()->isOperator()) && $exception->status !== \App\Enums\ExceptionStatus::Resolved)
                        <div class="pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            <span class="text-[11px] text-slate-600 font-medium">{{ __('shipments.show.update_incident_lifecycle') }}</span>
                            <div class="flex items-center gap-2">
                                @if($exception->status === \App\Enums\ExceptionStatus::Open)
                                    <form method="POST" action="{{ route('exceptions.status.update', $exception) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Enums\ExceptionStatus::Investigating->value }}">
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold transition cursor-pointer">
                                            {{ __('shipments.show.mark_investigating') }}
                                        </button>
                                    </form>
                                @endif

                                <!-- Resolve Form -->
                                <form method="POST" action="{{ route('exceptions.status.update', $exception) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\ExceptionStatus::Resolved->value }}">
                                    <input type="text" name="resolution_notes" placeholder="{{ __('shipments.show.resolution_notes_placeholder') }}" required
                                        class="px-3 py-1 rounded-lg bg-white border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 w-64 shadow-2xs">
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition cursor-pointer shadow-2xs">
                                        {{ __('shipments.show.resolve_and_clear') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                    {{ __('shipments.show.no_exceptions') }}
                </div>
            @endforelse
        </div>

        <!-- Log New Exception Form (Admin / Operator Only) -->
        @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
            <div class="pt-4 border-t border-slate-200">
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer text-xs font-bold uppercase tracking-wider text-amber-700 hover:text-amber-800">
                        <span>{{ __('shipments.show.flag_new_exception') }}</span>
                        <span class="text-slate-400 group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <form action="{{ route('shipments.exceptions.store', $shipment) }}" method="POST" class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Exception Type -->
                            <div>
                                <label for="exc_type" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.exception_category') }}</label>
                                <select id="exc_type" name="type" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs">
                                    @foreach(\App\Enums\ExceptionType::cases() as $typeCase)
                                        <option value="{{ $typeCase->value }}">{{ $typeCase->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Severity -->
                            <div>
                                <label for="exc_severity" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.severity_level') }}</label>
                                <select id="exc_severity" name="severity" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs">
                                    @foreach(\App\Enums\ExceptionSeverity::cases() as $severityCase)
                                        <option value="{{ $severityCase->value }}" {{ $severityCase === \App\Enums\ExceptionSeverity::High ? 'selected' : '' }}>
                                            {{ $severityCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Detected At -->
                            <div>
                                <label for="exc_detected_at" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.detected_at_label') }}</label>
                                <input type="datetime-local" id="exc_detected_at" name="detected_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="exc_description" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.show.incident_desc_label') }}</label>
                            <textarea id="exc_description" name="description" rows="2" required placeholder="{{ __('shipments.show.incident_desc_placeholder') }}"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-sm transition cursor-pointer">
                                {{ __('shipments.show.log_exception_button') }}
                            </button>
                        </div>
                    </form>
                </details>
            </div>
        @endif
    </div>

    <!-- Cargo Manifest Items Table -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('shipments.show.manifest_title', ['count' => $shipment->items->count()]) }}</h2>
            <span class="text-xs text-slate-500">{{ __('shipments.show.total_weight') }}: <strong class="text-slate-900 font-mono">{{ number_format($shipment->weight_kg, 2) }} kg</strong></span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5">{{ __('shipments.show.col_number') }}</th>
                        <th class="px-4 py-2.5">{{ __('shipments.show.col_description') }}</th>
                        <th class="px-4 py-2.5 text-center">{{ __('shipments.show.col_quantity') }}</th>
                        <th class="px-4 py-2.5 text-right">{{ __('shipments.show.col_net_weight') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shipment->items as $idx => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-center font-mono font-semibold text-slate-700">{{ number_format($item->quantity) }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-800">{{ number_format($item->weight_kg, 2) }} kg</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-slate-500">{{ __('shipments.show.no_line_items') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapData = @json($mapData);

    if (!mapData.has_coordinates || !document.getElementById('shipment-corridor-map')) {
        return;
    }

    // Initialize Leaflet Map
    const map = L.map('shipment-corridor-map');

    // Add OpenStreetMap Tile Layer with standard required attribution
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" class="text-blue-600 hover:underline">OpenStreetMap</a> contributors'
    }).addTo(map);

    const latlngs = [];
    const bounds = [];

    // Custom Map Pin HTML Generator (Light theme with crisp white borders & clean shadow)
    function createMapPin(bgColor, labelText, ringColor = 'border-white') {
        return L.divIcon({
            className: 'custom-leaflet-marker',
            html: `<div style="background-color: ${bgColor};" class="w-6 h-6 rounded-full border-2 ${ringColor} shadow-md flex items-center justify-center text-[10px] font-bold text-white font-mono">${labelText}</div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12],
            popupAnchor: [0, -14]
        });
    }

    // HTML sanitization helper for Leaflet popups (prevents DOM XSS injection)
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // 1. Origin Marker
    if (mapData.origin) {
        const originPoint = [mapData.origin.lat, mapData.origin.lng];
        latlngs.push(originPoint);
        bounds.push(originPoint);

        const originMarker = L.marker(originPoint, {
            icon: createMapPin('#2563eb', 'O', 'border-white')
        }).addTo(map);

        originMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">${escapeHtml(window.i18n?.leaflet?.origin || 'Port of Origin')}</span>
                <strong class="text-slate-900 text-sm block">${escapeHtml(mapData.origin.name)}</strong>
                <span class="text-slate-600 block">${escapeHtml(mapData.origin.city)}, ${escapeHtml(mapData.origin.country)}</span>
                <span class="font-mono text-[10px] text-slate-500 block">${escapeHtml(window.i18n?.leaflet?.coordinates || 'GPS')}: ${mapData.origin.lat.toFixed(4)}, ${mapData.origin.lng.toFixed(4)}</span>
            </div>
        `);
    }

    // 2. Waypoint Milestones Markers
    if (mapData.waypoints && mapData.waypoints.length > 0) {
        mapData.waypoints.forEach((wp, index) => {
            const wpPoint = [wp.lat, wp.lng];
            latlngs.push(wpPoint);
            bounds.push(wpPoint);

            const color = wp.is_exception ? '#ef4444' : '#6366f1';
            const marker = L.marker(wpPoint, {
                icon: createMapPin(color, (index + 1).toString())
            }).addTo(map);

            marker.bindPopup(`
                <div class="text-xs space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider ${wp.is_exception ? 'text-rose-600' : 'text-indigo-600'} block">
                        Milestone #${index + 1}: ${escapeHtml(wp.status)}
                    </span>
                    <strong class="text-slate-900 block">${escapeHtml(wp.location_name)}</strong>
                    <p class="text-slate-700 text-[11px] leading-relaxed">${escapeHtml(wp.description)}</p>
                    <span class="font-mono text-[10px] text-slate-500 block mt-1">Recorded: ${escapeHtml(wp.occurred_at)}</span>
                </div>
            `);
        });
    }

    // 3. Destination Marker
    if (mapData.destination) {
        const destPoint = [mapData.destination.lat, mapData.destination.lng];
        bounds.push(destPoint);

        const destMarker = L.marker(destPoint, {
            icon: createMapPin('#10b981', 'D', 'border-white')
        }).addTo(map);

        destMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">${escapeHtml(window.i18n?.leaflet?.destination || 'Port of Destination')}</span>
                <strong class="text-slate-900 text-sm block">${escapeHtml(mapData.destination.name)}</strong>
                <span class="text-slate-600 block">${escapeHtml(mapData.destination.city)}, ${escapeHtml(mapData.destination.country)}</span>
                <span class="font-mono text-[10px] text-slate-500 block">${escapeHtml(window.i18n?.leaflet?.coordinates || 'GPS')}: ${mapData.destination.lat.toFixed(4)}, ${mapData.destination.lng.toFixed(4)}</span>
            </div>
        `);
    }

    // 4. Polyline Transit Corridor (following chronological milestones)
    if (latlngs.length >= 2) {
        L.polyline(latlngs, {
            color: '#2563eb',
            weight: 3.5,
            opacity: 0.85,
            dashArray: '6, 8',
            lineJoin: 'round'
        }).addTo(map);
    }

    // 5. Fit Bounds
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 8 });
    } else {
        map.setView([15, 60], 3);
    }
});
</script>
@endpush
