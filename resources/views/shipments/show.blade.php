@extends('layouts.app')

@section('title', 'Shipment ' . $shipment->shipment_number)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Custom Leaflet styling for dark mode */
    .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f8fafc !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }
    .leaflet-popup-tip {
        background: #0f172a !important;
        border: 1px solid #334155 !important;
    }
    .leaflet-container {
        font-family: inherit !important;
        background: #020617 !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header with Status Badges -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight text-white">
                    {{ $shipment->shipment_number }}
                </h1>

                <!-- Current Status Badge -->
                @php
                    $statusColors = [
                        'CREATED' => 'bg-slate-800 text-slate-300 border-slate-700',
                        'BOOKED' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                        'PICKED_UP' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                        'IN_TRANSIT' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                        'DELIVERED' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                        'CANCELLED' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                    ];
                    $badge = $statusColors[$shipment->current_status->value] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $badge }}">
                    {{ $shipment->current_status->label() }}
                </span>

                <!-- Delay Status Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $shipment->delay_status->badgeClasses() }}" title="{{ $shipment->delay_reason ?? 'On schedule' }}">
                    @if($shipment->delay_status === \App\Enums\DelayStatus::OnTime)
                        On Schedule
                    @elseif($shipment->delay_status === \App\Enums\DelayStatus::Low)
                        Delay: LOW ({{ $shipment->delay_hours }}h)
                    @elseif($shipment->delay_status === \App\Enums\DelayStatus::Medium)
                        Delay: MEDIUM ({{ $shipment->delay_hours }}h)
                    @elseif($shipment->delay_status === \App\Enums\DelayStatus::High)
                        Delay: HIGH ({{ $shipment->delay_hours }}h)
                    @else
                        {{ $shipment->delay_status->label() }} ({{ $shipment->delay_hours }}h)
                    @endif
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1.5">
                Contracted Carrier: <span class="text-slate-300 font-medium">{{ $shipment->carrier->name }}</span> &bull; Booked by {{ $shipment->creator->name }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 transition">&larr; Shipments</a>
            @can('update', $shipment)
                <a href="{{ route('shipments.edit', $shipment) }}" class="px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-semibold transition">
                    Edit Booking
                </a>
            @endcan
            @can('delete', $shipment)
                <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" onsubmit="return confirm('Permanently delete shipment {{ $shipment->shipment_number }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-semibold transition cursor-pointer">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if($shipment->delay_reason)
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 text-xs text-slate-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold uppercase bg-blue-500/10 text-blue-400 border border-blue-500/30 shrink-0">
                    ETA Evaluation SLA
                </span>
                <span class="text-slate-300 font-mono text-xs">{{ $shipment->delay_reason }}</span>
            </div>
            <span class="text-[11px] text-slate-500 font-mono shrink-0">Active SLA Rule</span>
        </div>
    @endif

    <!-- Active Exception Alert (Primary Parameter: CUSTOMS_HOLD Highlight) -->
    @php
        $activeException = $shipment->exceptions->firstWhere('status.value', 'OPEN')
            ?? $shipment->exceptions->firstWhere('status.value', 'INVESTIGATING');
    @endphp

    @if($activeException)
        <div class="p-5 rounded-2xl bg-amber-950/30 border border-amber-500/40 text-amber-200 shadow-lg">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl bg-amber-500/20 text-amber-400 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Active Operational Exception:</span>
                        <span class="font-bold text-white text-sm">{{ $activeException->type->label() }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Severity: {{ $activeException->severity->value }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-900 text-slate-300">
                            {{ $activeException->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-amber-200/90 mt-1.5 leading-relaxed">
                        {{ $activeException->description }}
                    </p>
                    <div class="text-[11px] text-amber-300/70 mt-2">
                        Detected: {{ $activeException->detected_at->format('d M Y, H:i') }} UTC &bull; Logged in Tracking Telemetry
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Current Location & Live Position Telemetry Card -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-sky-950/40 via-slate-900/90 to-slate-900/90 border border-sky-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 shrink-0">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-400">Current Position / Verified Hub</span>
                <div class="text-lg font-bold text-white mt-0.5">
                    {{ $currentLocation->name ?? 'Port of Departure' }}
                </div>
                <div class="text-xs text-slate-400">
                    {{ $currentLocation->city ?? '' }}, {{ $currentLocation->country ?? '' }}
                    @if($latestEvent && $latestEvent->latitude && $latestEvent->longitude)
                        &bull; <span class="font-mono text-slate-300">WGS84: {{ number_format($latestEvent->latitude, 6) }}, {{ number_format($latestEvent->longitude, 6) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-right sm:border-l sm:border-slate-800 sm:pl-6">
            <span class="text-[10px] uppercase tracking-wider text-slate-500 block">Last Milestone Update</span>
            <span class="text-xs font-mono font-medium text-slate-300 mt-0.5 block">
                {{ $latestEvent ? $latestEvent->occurred_at->format('d M Y, H:i') . ' UTC' : 'Pending Departure' }}
            </span>
            <span class="text-[11px] text-sky-400 font-semibold block mt-0.5">
                {{ $latestEvent ? $latestEvent->event_type->label() : 'Booking Initiated' }}
            </span>
        </div>
    </div>

    <!-- Route Overview Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Route -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Corridor Routing & Port Waypoints</h2>

            <div class="flex items-center justify-between relative">
                <!-- Origin -->
                <div class="text-left z-10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-400">Origin Port</span>
                    <div class="text-lg font-bold text-white mt-0.5">{{ $shipment->originLocation->city }}</div>
                    <div class="text-xs text-slate-400">{{ $shipment->originLocation->name }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 font-mono">
                        ETD: {{ $shipment->expected_departure_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <!-- Mid-route connector -->
                <div class="flex-grow mx-6 flex flex-col items-center">
                    <div class="w-full border-t border-dashed border-slate-700 relative flex items-center justify-center">
                        <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sky-400 -mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 font-medium">Ocean Transit Corridor</span>
                </div>

                <!-- Destination -->
                <div class="text-right z-10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Destination Port</span>
                    <div class="text-lg font-bold text-white mt-0.5">{{ $shipment->destinationLocation->city }}</div>
                    <div class="text-xs text-slate-400">{{ $shipment->destinationLocation->name }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 font-mono">
                        ETA: {{ $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y, H:i') : $shipment->expected_arrival_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <!-- Specs Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-slate-800/80 text-xs">
                <div class="p-3 rounded-xl bg-slate-950">
                    <span class="text-slate-500 block text-[11px]">Commodity</span>
                    <span class="font-semibold text-white mt-0.5 block truncate" title="{{ $shipment->cargo_type }}">{{ $shipment->cargo_type }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-950">
                    <span class="text-slate-500 block text-[11px]">Total Weight</span>
                    <span class="font-semibold text-white mt-0.5 block font-mono">{{ number_format($shipment->weight_kg, 2) }} kg</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-950">
                    <span class="text-slate-500 block text-[11px]">Volume</span>
                    <span class="font-semibold text-white mt-0.5 block font-mono">{{ number_format($shipment->volume_cbm, 2) }} CBM</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-950">
                    <span class="text-slate-500 block text-[11px]">Carrier</span>
                    <span class="font-semibold text-sky-400 mt-0.5 block font-mono">[{{ $shipment->carrier->code }}]</span>
                </div>
            </div>
        </div>

        <!-- Consignee Profile Card -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Consignee Ownership</h2>
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Consignee Account</span>
                <div class="text-sm font-bold text-white mt-1">{{ $shipment->customer->name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $shipment->customer->email }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-400">Operating Carrier</span>
                <div class="text-sm font-bold text-white mt-1">{{ $shipment->carrier->name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $shipment->carrier->contact_email ?? 'Standard Dispatch' }}</div>
            </div>
        </div>
    </div>

    <!-- Maritime Corridor Map & Live Meteorological Telemetry (Tahap 7) -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Interactive Global Corridor & Telemetry Map
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Real-time geospatial tracking connecting port of origin, ocean transit milestones, and port of destination</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-semibold bg-slate-900 border border-slate-800 text-slate-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> OpenStreetMap Tiles
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Map Container (3 Cols) -->
            <div class="lg:col-span-3 rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl relative min-h-[420px] flex flex-col">
                @if($mapData['has_coordinates'])
                    <div id="shipment-corridor-map" class="w-full flex-grow min-h-[420px] z-0"></div>
                @else
                    <div class="w-full flex-grow flex flex-col items-center justify-center p-8 text-center bg-slate-950/60 min-h-[320px]">
                        <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-white">Geographic Coordinates Unavailable</h4>
                        <p class="text-xs text-slate-400 max-w-sm mt-1 leading-relaxed">
                            No WGS84 GPS coordinates are configured for this shipment's origin, destination, or recorded milestones. Coordinates are required to render the interactive corridor map.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Weather Card (1 Col) -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex flex-col justify-between shadow-xl space-y-4">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                            Current Weather
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $weather['available'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400' }}">
                            {{ $weather['available'] ? 'Live' : 'Offline' }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 block">Verified Hub Location</span>
                        <div class="text-sm font-bold text-white mt-0.5">{{ $currentLocation->name ?? 'Port of Departure' }}</div>
                        <div class="text-xs text-slate-400">{{ $currentLocation->city ?? '' }}, {{ $currentLocation->country ?? '' }}</div>
                    </div>

                    @if($weather['available'])
                        <div class="mt-4 p-4 rounded-xl bg-slate-950 border border-slate-800/80 space-y-3">
                            <div class="flex items-baseline justify-between">
                                <span class="text-2xl font-bold font-mono text-white">{{ $weather['temperature'] }}°C</span>
                                <span class="text-xs font-medium text-sky-400">{{ $weather['condition'] }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block">Wind Velocity</span>
                                    <span class="font-mono text-slate-200 font-semibold">{{ $weather['wind_speed'] }} km/h</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Humidity</span>
                                    <span class="font-mono text-slate-200 font-semibold">{{ $weather['humidity'] }}%</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 p-4 rounded-xl bg-slate-950/60 border border-slate-800/60 text-center">
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $weather['message'] ?? 'Live weather telemetry currently unavailable.' }}</p>
                        </div>
                    @endif
                </div>

                <!-- Mandatory Non-Causal Contextual Disclaimer (Requirement 4) -->
                <div class="p-3 rounded-xl bg-amber-500/5 border border-amber-500/20 text-[10px] text-amber-300/80 leading-relaxed">
                    <strong class="font-bold uppercase tracking-wider block text-amber-400 text-[9px] mb-0.5">Maritime Context Disclaimer</strong>
                    {{ $weather['disclaimer'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Chronological Timeline Section (Requirement 3) -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h2 class="text-base font-bold text-white">Tracking Milestone Timeline</h2>
                <p class="text-xs text-slate-400 mt-0.5">Chronologically ordered telemetry and waypoint clearance history.</p>
            </div>
            <span class="text-xs text-slate-500 font-mono">{{ $shipment->trackingEvents->count() }} milestones logged</span>
        </div>

        <div class="relative border-l-2 border-slate-800 ml-4 space-y-6 pt-2">
            @forelse($shipment->trackingEvents as $event)
                <div class="relative pl-7">
                    <!-- Milestone Marker Node -->
                    @if($event->is_exception)
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-slate-950 shadow-md shadow-rose-500/40"></div>
                    @else
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-sky-500 border-2 border-slate-950 shadow-md shadow-sky-500/30"></div>
                    @endif

                    <!-- Event Card Container -->
                    <div class="p-4 rounded-xl border {{ $event->is_exception ? 'bg-rose-950/20 border-rose-500/30 text-rose-200' : 'bg-slate-950/70 border-slate-800/80 text-slate-200' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $event->is_exception ? 'text-rose-400' : 'text-sky-400' }}">
                                    {{ $event->event_type->label() }}
                                </span>
                                @if($event->location)
                                    <span class="text-xs font-medium text-white">&bull; {{ $event->location->name }} ({{ $event->location->city }}, {{ $event->location->country }})</span>
                                @endif
                            </div>
                            <span class="text-[11px] font-mono text-slate-400">
                                {{ $event->occurred_at->format('d M Y, H:i') }} UTC
                            </span>
                        </div>

                        <p class="text-xs mt-2 leading-relaxed {{ $event->is_exception ? 'text-rose-200' : 'text-slate-300' }}">
                            {{ $event->description }}
                        </p>

                        @if($event->latitude && $event->longitude)
                            <div class="mt-2 text-[11px] font-mono text-slate-500 flex items-center gap-3">
                                <span>GPS: {{ number_format($event->latitude, 6) }}, {{ number_format($event->longitude, 6) }}</span>
                                <a
                                    href="https://www.openstreetmap.org/?mlat={{ $event->latitude }}&mlon={{ $event->longitude }}#map=12/{{ $event->latitude }}/{{ $event->longitude }}"
                                    target="_blank"
                                    rel="noreferrer"
                                    class="text-sky-400 hover:underline"
                                >
                                    Open in OSM &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="pl-6 text-xs text-slate-500">
                    No tracking milestones recorded yet for this booking.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Form: Add Tracking Event (Requirement 4 - Admin & Operator Only) -->
    @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
        <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-white">Log Tracking Milestone Event</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Append a verified tracking milestone and transition the shipment lifecycle status.
                    </p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    Operator Console
                </span>
            </div>

            @if($shipment->current_status === \App\Enums\ShipmentStatus::Delivered)
                <div class="p-4 rounded-xl bg-emerald-950/20 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>This shipment has been <strong>DELIVERED</strong>. The delivery milestone is terminal and no further status transitions are allowed.</span>
                </div>
            @else
                <form method="POST" action="{{ route('shipments.events.store', $shipment) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Target Status -->
                        <div>
                            <label for="status" class="block text-xs font-medium text-slate-300 mb-1">Target Lifecycle Status *</label>
                            <select id="status" name="status" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" {{ old('status', $shipment->current_status->value) === $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block text-xs font-medium text-slate-300 mb-1">Milestone Event Type *</label>
                            <select id="event_type" name="event_type" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                @foreach($eventTypes as $et)
                                    <option value="{{ $et->value }}" {{ old('event_type') === $et->value ? 'selected' : '' }}>
                                        {{ $et->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_type') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location_id" class="block text-xs font-medium text-slate-300 mb-1">Waypoint Hub / Port (Optional)</label>
                            <select id="location_id" name="location_id" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <option value="">Select Waypoint...</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }} ({{ $loc->city }})
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Occurred At -->
                        <div>
                            <label for="occurred_at" class="block text-xs font-medium text-slate-300 mb-1">Event Timestamp (UTC) *</label>
                            <input
                                type="datetime-local"
                                id="occurred_at"
                                name="occurred_at"
                                value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                required
                                class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                            @error('occurred_at') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-xs font-medium text-slate-300 mb-1">Latitude (Optional Decimal)</label>
                            <input
                                type="number"
                                step="0.0000001"
                                id="latitude"
                                name="latitude"
                                value="{{ old('latitude') }}"
                                placeholder="e.g. 1.2644120"
                                class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-xs font-medium text-slate-300 mb-1">Longitude (Optional Decimal)</label>
                            <input
                                type="number"
                                step="0.0000001"
                                id="longitude"
                                name="longitude"
                                value="{{ old('longitude') }}"
                                placeholder="e.g. 103.8222180"
                                class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-medium text-slate-300 mb-1">Milestone Description *</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="2"
                            required
                            placeholder="Detail milestone actions, e.g. Vessel berthed at terminal berth 4, customs examination cleared..."
                            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                        >{{ old('description') }}</textarea>
                        @error('description') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Is Exception Flag -->
                    <div>
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_exception"
                                value="1"
                                {{ old('is_exception') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-rose-500"
                            />
                            Mark as Exception Event (Applies prominent visual indicator in customer timeline)
                        </label>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition cursor-pointer"
                        >
                            Record Milestone Event &rarr;
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    <!-- Operational Exceptions & Incident Management -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Operational Exceptions & Incidents ({{ $shipment->exceptions->count() }})
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Customs holds, weather disruptions, port congestion, and cargo alerts</p>
            </div>
            @if($shipment->exceptions->whereIn('status.value', ['OPEN', 'INVESTIGATING'])->isNotEmpty())
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/30 animate-pulse">
                    Active Disruptions
                </span>
            @endif
        </div>

        <!-- Exceptions List -->
        <div class="space-y-4">
            @forelse($shipment->exceptions as $exception)
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/90 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-slate-900 text-white border border-slate-700">
                                {{ $exception->type->label() }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                Severity: {{ $exception->severity->value }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $exception->status->badgeClasses() }}">
                                Status: {{ $exception->status->label() }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono">
                            Detected: {{ $exception->detected_at->format('d M Y, H:i') }}
                            @if($exception->resolved_at)
                                &bull; <span class="text-emerald-400 font-semibold">Resolved: {{ $exception->resolved_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-slate-300 whitespace-pre-line leading-relaxed">
                        {{ $exception->description }}
                    </div>

                    <!-- Operational State Transition Controls (Operator / Admin Only) -->
                    @if((auth()->user()->isAdmin() || auth()->user()->isOperator()) && $exception->status !== \App\Enums\ExceptionStatus::Resolved)
                        <div class="pt-3 border-t border-slate-800/60 flex flex-wrap items-center justify-between gap-3">
                            <span class="text-[11px] text-slate-400 font-medium">Update Incident Lifecycle:</span>
                            <div class="flex items-center gap-2">
                                @if($exception->status === \App\Enums\ExceptionStatus::Open)
                                    <form method="POST" action="{{ route('exceptions.status.update', $exception) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Enums\ExceptionStatus::Investigating->value }}">
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold transition cursor-pointer">
                                            Mark as Investigating
                                        </button>
                                    </form>
                                @endif

                                <!-- Resolve Form -->
                                <form method="POST" action="{{ route('exceptions.status.update', $exception) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Enums\ExceptionStatus::Resolved->value }}">
                                    <input type="text" name="resolution_notes" placeholder="Resolution notes (e.g. customs cleared)..." required
                                        class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 w-64">
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-semibold transition cursor-pointer">
                                        Resolve & Clear
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-5 rounded-xl bg-slate-950/50 border border-slate-800/60 text-center text-xs text-slate-500">
                    No exceptions or operational disruptions recorded for this shipment.
                </div>
            @endforelse
        </div>

        <!-- Log New Exception Form (Admin / Operator Only) -->
        @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
            <div class="pt-4 border-t border-slate-800">
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer text-xs font-bold uppercase tracking-wider text-amber-400 hover:text-amber-300">
                        <span>+ Flag New Operational Exception Incident</span>
                        <span class="text-slate-500 group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <form action="{{ route('shipments.exceptions.store', $shipment) }}" method="POST" class="mt-4 p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Exception Type -->
                            <div>
                                <label for="exc_type" class="block text-xs font-medium text-slate-300 mb-1">Exception Category *</label>
                                <select id="exc_type" name="type" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                    @foreach(\App\Enums\ExceptionType::cases() as $typeCase)
                                        <option value="{{ $typeCase->value }}">{{ $typeCase->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Severity -->
                            <div>
                                <label for="exc_severity" class="block text-xs font-medium text-slate-300 mb-1">Severity Level *</label>
                                <select id="exc_severity" name="severity" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                    @foreach(\App\Enums\ExceptionSeverity::cases() as $severityCase)
                                        <option value="{{ $severityCase->value }}" {{ $severityCase === \App\Enums\ExceptionSeverity::High ? 'selected' : '' }}>
                                            {{ $severityCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Detected At -->
                            <div>
                                <label for="exc_detected_at" class="block text-xs font-medium text-slate-300 mb-1">Detected At (Defaults to Now)</label>
                                <input type="datetime-local" id="exc_detected_at" name="detected_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="exc_description" class="block text-xs font-medium text-slate-300 mb-1">Incident Description & Context *</label>
                            <textarea id="exc_description" name="description" rows="2" required placeholder="Describe the operational issue, e.g. Customs physical inspection requested at Port of Rotterdam..."
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs transition cursor-pointer">
                                Log Exception & Broadcast Alert &rarr;
                            </button>
                        </div>
                    </form>
                </details>
            </div>
        @endif
    </div>

    <!-- Cargo Manifest Items Table -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Cargo Line Items Manifest ({{ $shipment->items->count() }})</h2>
            <span class="text-xs text-slate-400">Total Net Weight: <strong class="text-white font-mono">{{ number_format($shipment->weight_kg, 2) }} kg</strong></span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-2.5">#</th>
                        <th class="px-4 py-2.5">Packaged Commodity Description</th>
                        <th class="px-4 py-2.5 text-center">Package Quantity</th>
                        <th class="px-4 py-2.5 text-right">Net Weight (KG)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($shipment->items as $idx => $item)
                        <tr class="hover:bg-slate-800/40">
                            <td class="px-4 py-3 font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-center font-mono font-semibold">{{ number_format($item->quantity) }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-200">{{ number_format($item->weight_kg, 2) }} kg</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-slate-500">No individual line items registered.</td>
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
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" class="text-sky-400 hover:underline">OpenStreetMap</a> contributors'
    }).addTo(map);

    const latlngs = [];
    const bounds = [];

    // Custom Map Pin HTML Generator
    function createMapPin(bgColor, labelText, ringColor = 'border-slate-950') {
        return L.divIcon({
            className: 'custom-leaflet-marker',
            html: `<div style="background-color: ${bgColor};" class="w-6 h-6 rounded-full border-2 ${ringColor} shadow-lg flex items-center justify-center text-[10px] font-bold text-white font-mono">${labelText}</div>`,
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
            icon: createMapPin('#0284c7', 'O', 'border-white')
        }).addTo(map);

        originMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-400 block">Port of Origin</span>
                <strong class="text-white text-sm block">${escapeHtml(mapData.origin.name)}</strong>
                <span class="text-slate-400 block">${escapeHtml(mapData.origin.city)}, ${escapeHtml(mapData.origin.country)}</span>
                <span class="font-mono text-[10px] text-slate-400 block">GPS: ${mapData.origin.lat.toFixed(4)}, ${mapData.origin.lng.toFixed(4)}</span>
            </div>
        `);
    }

    // 2. Waypoint Milestones Markers
    if (mapData.waypoints && mapData.waypoints.length > 0) {
        mapData.waypoints.forEach((wp, index) => {
            const wpPoint = [wp.lat, wp.lng];
            latlngs.push(wpPoint);
            bounds.push(wpPoint);

            const color = wp.is_exception ? '#f43f5e' : '#6366f1';
            const marker = L.marker(wpPoint, {
                icon: createMapPin(color, (index + 1).toString())
            }).addTo(map);

            marker.bindPopup(`
                <div class="text-xs space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider ${wp.is_exception ? 'text-rose-400' : 'text-indigo-400'} block">
                        Milestone #${index + 1}: ${escapeHtml(wp.status)}
                    </span>
                    <strong class="text-white block">${escapeHtml(wp.location_name)}</strong>
                    <p class="text-slate-300 text-[11px] leading-relaxed">${escapeHtml(wp.description)}</p>
                    <span class="font-mono text-[10px] text-slate-400 block mt-1">Recorded: ${escapeHtml(wp.occurred_at)}</span>
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
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block">Port of Destination</span>
                <strong class="text-white text-sm block">${escapeHtml(mapData.destination.name)}</strong>
                <span class="text-slate-400 block">${escapeHtml(mapData.destination.city)}, ${escapeHtml(mapData.destination.country)}</span>
                <span class="font-mono text-[10px] text-slate-400 block">GPS: ${mapData.destination.lat.toFixed(4)}, ${mapData.destination.lng.toFixed(4)}</span>
            </div>
        `);
    }

    // 4. Polyline Transit Corridor (following chronological milestones)
    if (latlngs.length >= 2) {
        L.polyline(latlngs, {
            color: '#38bdf8',
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
