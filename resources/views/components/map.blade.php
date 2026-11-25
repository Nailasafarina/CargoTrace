@props([
    'id' => 'map-' . uniqid(),
    'mode' => 'single', // 'single', 'picker', 'multi'
    'latitude' => null,
    'longitude' => null,
    'zoom' => 12,
    'height' => '400px',
    'title' => null,
    'subtitle' => null,
    'marker' => null,
    'markers' => [],
    'latInput' => '#latitude',
    'lngInput' => '#longitude',
    'editUrl' => null,
    'osmUrl' => null,
])

@php
    $isValidCoordinate = function ($lat, $lng): bool {
        return $lat !== null && $lng !== null
            && is_numeric($lat) && is_numeric($lng)
            && $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180;
    };

    $hasValidCoords = false;
    if ($mode === 'single') {
        $hasValidCoords = $isValidCoordinate($latitude, $longitude);
    } elseif ($mode === 'picker') {
        $hasValidCoords = true; // Picker always renders canvas even if starting at world center
    } elseif ($mode === 'multi') {
        $hasValidCoords = collect($markers)->contains(function ($m) use ($isValidCoordinate) {
            return $isValidCoordinate($m['lat'] ?? $m['latitude'] ?? null, $m['lng'] ?? $m['longitude'] ?? null);
        });
    }

    $mapConfig = [
        'mode' => $mode,
        'latitude' => $latitude !== null ? (float) $latitude : null,
        'longitude' => $longitude !== null ? (float) $longitude : null,
        'zoom' => (int) $zoom,
        'title' => $title,
        'marker' => $marker,
        'markers' => $markers,
        'latInput' => $latInput,
        'lngInput' => $lngInput,
    ];

    // Compute OSM direct link if not explicitly provided
    if (!$osmUrl && $isValidCoordinate($latitude, $longitude)) {
        $osmUrl = "https://www.openstreetmap.org/?mlat={$latitude}&mlon={$longitude}#map={$zoom}/{$latitude}/{$longitude}";
    }
@endphp

@if($hasValidCoords)
    <div {{ $attributes->merge(['class' => 'nex-card p-6 space-y-4']) }}>
        @if($title || $subtitle || $osmUrl || $mode === 'multi')
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-3.5 border-b border-[#EFE6EA]">
                <div>
                    @if($title)
                        <h2 class="text-base font-bold text-[#1A1718] tracking-tight flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#BE2B5B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            {{ $title }}
                        </h2>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-[#6B6467] mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($mode === 'multi')
                        <!-- Compact Facility Type Legend in Top-Right Map Header -->
                        <div class="flex flex-wrap items-center gap-2 bg-[#FAF7F8] border border-[#EFE6EA] px-3 py-1.5 rounded-xl text-[11px] font-semibold text-[#1A1718]">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#e04879] ring-2 ring-[#FCECF1]"></span>
                                <span>{{ __('locations.types.port') }}</span>
                            </span>
                            <span class="text-[#E5DCE0]">|</span>
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#1f171b] ring-2 ring-zinc-200"></span>
                                <span>{{ __('locations.types.airport') }}</span>
                            </span>
                            <span class="text-[#E5DCE0]">|</span>
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#9e224b] ring-2 ring-[#FCECF1]"></span>
                                <span>{{ __('locations.types.warehouse') }}</span>
                            </span>
                            <span class="text-[#E5DCE0]">|</span>
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#10b981] ring-2 ring-emerald-100"></span>
                                <span>{{ __('locations.types.city') }}</span>
                            </span>
                        </div>
                    @endif

                    @if($osmUrl)
                        <a
                            href="{{ $osmUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#BE2B5B] hover:text-[#9E224B] hover:underline transition"
                        >
                            <span>{{ __('locations.view_osm') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Leaflet Map Canvas Container -->
        <div class="rounded-xl overflow-hidden border border-[#EFE6EA] relative">
            <div
                id="{{ $id }}"
                data-component="map"
                data-map-config="{{ json_encode($mapConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                style="height: {{ $height }}; min-height: 280px;"
                class="w-full z-0 bg-[#FAF7F8]"
            ></div>
        </div>
    </div>
@else
    <!-- Fallback Card for Missing or Invalid Coordinates -->
    <div {{ $attributes->merge(['class' => 'nex-card p-8 text-center flex flex-col items-center justify-center space-y-3']) }}>
        <div class="w-12 h-12 rounded-2xl bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center mb-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-[#4A2C35] tracking-tight">{{ __('locations.map.no_coordinates') }}</h4>
            <p class="text-xs text-[#8C737B] max-w-sm mt-1 leading-relaxed">{{ __('locations.map.no_coordinates_desc') }}</p>
        </div>
        @if($editUrl)
            <a
                href="{{ $editUrl }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-xs hover:shadow transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span>{{ __('locations.map.set_coordinates') }}</span>
            </a>
        @endif
    </div>
@endif
