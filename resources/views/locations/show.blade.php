@extends('layouts.app')

@section('title', 'Location Details - ' . $location->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-white">{{ $location->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-500/10 text-sky-400 border border-sky-500/30">
                    {{ $location->type->value }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $location->city }}, {{ $location->country }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('locations.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 transition">&larr; Back</a>
            @can('update', $location)
                <a href="{{ route('locations.edit', $location) }}" class="px-3 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-medium transition">Edit</a>
            @endcan
        </div>
    </div>

    <!-- Metadata & Coordinates -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400">Departing Shipments</span>
            <div class="text-2xl font-bold text-sky-400 mt-1">{{ $location->origin_shipments_count }}</div>
        </div>
        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400">Arriving Shipments</span>
            <div class="text-2xl font-bold text-emerald-400 mt-1">{{ $location->destination_shipments_count }}</div>
        </div>
        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400">Milestone Events Logged</span>
            <div class="text-2xl font-bold text-indigo-400 mt-1">{{ $location->tracking_events_count }}</div>
        </div>
    </div>

    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
        <h2 class="text-base font-semibold text-white mb-2">Geographic Coordinates (WGS84 Format)</h2>
        <div class="p-4 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 flex items-center justify-between">
            <div>
                <span class="text-slate-500">Latitude:</span> <span class="text-sky-400">{{ number_format($location->latitude, 7) }}</span>
                <span class="text-slate-600 px-3">|</span>
                <span class="text-slate-500">Longitude:</span> <span class="text-emerald-400">{{ number_format($location->longitude, 7) }}</span>
            </div>
            <a
                href="https://www.openstreetmap.org/?mlat={{ $location->latitude }}&mlon={{ $location->longitude }}#map=12/{{ $location->latitude }}/{{ $location->longitude }}"
                target="_blank"
                rel="noreferrer"
                class="text-sky-400 hover:underline text-[11px]"
            >
                View on OpenStreetMap &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
