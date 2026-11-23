@extends('layouts.app')

@section('title', __('locations.details_title') . ' - ' . $location->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $location->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $location->type->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $location->city }}, {{ $location->country }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('locations.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">&larr; {{ __('common.actions.back') }}</a>
            @can('update', $location)
                <a href="{{ route('locations.edit', $location) }}" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition">{{ __('common.actions.edit') }}</a>
            @endcan
        </div>
    </div>

    <!-- Metadata & Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500">{{ __('locations.departing_shipments') }}</span>
            <div class="text-2xl font-bold text-blue-700 mt-1">{{ $location->origin_shipments_count }}</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500">{{ __('locations.arriving_shipments') }}</span>
            <div class="text-2xl font-bold text-emerald-700 mt-1">{{ $location->destination_shipments_count }}</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500">{{ __('locations.milestone_events') }}</span>
            <div class="text-2xl font-bold text-indigo-700 mt-1">{{ $location->tracking_events_count }}</div>
        </div>
    </div>

    <!-- Geographic Coordinates (WGS84 Format) -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-2">{{ __('locations.coords_title') }}</h2>
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs text-slate-800 flex items-center justify-between flex-wrap gap-2">
            <div>
                <span class="text-slate-500">{{ __('locations.fields.latitude') }}:</span>
                <span class="text-blue-700 font-semibold">{{ $location->latitude !== null ? number_format($location->latitude, 7) : '—' }}</span>
                <span class="text-slate-300 px-3">|</span>
                <span class="text-slate-500">{{ __('locations.fields.longitude') }}:</span>
                <span class="text-emerald-700 font-semibold">{{ $location->longitude !== null ? number_format($location->longitude, 7) : '—' }}</span>
            </div>
            @if($location->latitude !== null && $location->longitude !== null)
                <a
                    href="https://www.openstreetmap.org/?mlat={{ $location->latitude }}&mlon={{ $location->longitude }}#map=12/{{ $location->latitude }}/{{ $location->longitude }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-blue-600 hover:text-blue-800 hover:underline font-semibold text-[11px] inline-flex items-center gap-1"
                >
                    <span>{{ __('locations.view_osm') }}</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            @endif
        </div>
    </div>

    <!-- Interactive Location Map Card (Leaflet + OpenStreetMap) -->
    <x-map
        id="location-detail-map"
        mode="single"
        :latitude="$location->latitude"
        :longitude="$location->longitude"
        :zoom="12"
        height="400px"
        :title="__('locations.map.title')"
        :subtitle="__('locations.map.subtitle')"
        :marker="[
            'title' => $location->name,
            'type' => $location->type->value,
            'typeLabel' => $location->type->label(),
            'city' => $location->city,
            'country' => $location->country,
        ]"
        :edit-url="auth()->user()?->can('update', $location) ? route('locations.edit', $location) : null"
        :osm-url="$location->latitude !== null && $location->longitude !== null ? 'https://www.openstreetmap.org/?mlat=' . $location->latitude . '&mlon=' . $location->longitude . '#map=12/' . $location->latitude . '/' . $location->longitude : null"
    />
</div>
@endsection
