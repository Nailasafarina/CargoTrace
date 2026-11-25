@extends('layouts.app')

@section('title', __('locations.details_title') . ' - ' . $location->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Left Hub Telemetry Rail -->
    <aside class="lg:col-span-5 space-y-4">
        <div class="nex-card p-6 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">
                        {{ $location->type->label() }}
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#4A2C35] mt-3">{{ $location->name }}</h1>
                    <p class="text-xs text-[#8C737B] mt-1">{{ $location->city }}, {{ $location->country }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('locations.index') }}" class="flex-1 py-2.5 px-4 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-[#6E545D] text-center transition">
                    &larr; {{ __('common.actions.back') }}
                </a>
                @can('update', $location)
                    <a href="{{ route('locations.edit', $location) }}" class="flex-1 py-2.5 px-4 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold text-center transition">
                        {{ __('common.actions.edit') }}
                    </a>
                @endcan
            </div>
        </div>

        <!-- Hub Throughput Bento Strip -->
        <div class="grid grid-cols-3 gap-3">
            <div class="nex-card p-4">
                <span class="text-[11px] text-[#8C737B] block">{{ __('locations.departing_shipments') }}</span>
                <div class="text-xl font-bold text-[#C93565] mt-1 tabular-nums">{{ $location->origin_shipments_count }}</div>
            </div>
            <div class="nex-card p-4">
                <span class="text-[11px] text-[#8C737B] block">{{ __('locations.arriving_shipments') }}</span>
                <div class="text-xl font-bold text-emerald-700 mt-1 tabular-nums">{{ $location->destination_shipments_count }}</div>
            </div>
            <div class="nex-card p-4">
                <span class="text-[11px] text-[#8C737B] block">{{ __('locations.milestone_events') }}</span>
                <div class="text-xl font-bold text-[#4A2C35] mt-1 tabular-nums">{{ $location->tracking_events_count }}</div>
            </div>
        </div>

        <!-- Geographic Coordinates (WGS84 Format) -->
        <div class="nex-card p-6 space-y-3">
            <h2 class="text-sm font-bold text-[#4A2C35]">{{ __('locations.coords_title') }}</h2>
            <div class="nex-inset p-4 font-mono text-xs text-[#4A2C35] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[#8C737B]">{{ __('locations.fields.latitude') }}:</span>
                    <span class="text-[#C93565] font-semibold tabular-nums">{{ $location->latitude !== null ? number_format($location->latitude, 7) : '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#8C737B]">{{ __('locations.fields.longitude') }}:</span>
                    <span class="text-emerald-700 font-semibold tabular-nums">{{ $location->longitude !== null ? number_format($location->longitude, 7) : '—' }}</span>
                </div>
                @if($location->latitude !== null && $location->longitude !== null)
                    <div class="pt-2 border-t border-[#ECE4E8]">
                        <a
                            href="https://www.openstreetmap.org/?mlat={{ $location->latitude }}&mlon={{ $location->longitude }}#map=12/{{ $location->latitude }}/{{ $location->longitude }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[#C93565] hover:underline font-semibold text-[11px] inline-flex items-center gap-1"
                        >
                            <span>{{ __('locations.view_osm') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </aside>

    <!-- Right Interactive Location Map Stage -->
    <div class="lg:col-span-7">
        <x-map
            id="location-detail-map"
            mode="single"
            :latitude="$location->latitude"
            :longitude="$location->longitude"
            :zoom="12"
            height="480px"
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
</div>
@endsection
