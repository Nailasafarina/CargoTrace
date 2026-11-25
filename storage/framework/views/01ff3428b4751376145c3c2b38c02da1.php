<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<?php if($hasValidCoords): ?>
    <div <?php echo e($attributes->merge(['class' => 'p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4'])); ?>>
        <?php if($title || $subtitle || $osmUrl): ?>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <?php if($title): ?>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <?php echo e($title); ?>

                        </h2>
                    <?php endif; ?>
                    <?php if($subtitle): ?>
                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e($subtitle); ?></p>
                    <?php endif; ?>
                </div>

                <?php if($osmUrl): ?>
                    <div class="flex items-center gap-2 shrink-0">
                        <a
                            href="<?php echo e($osmUrl); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline transition"
                        >
                            <span><?php echo e(__('locations.view_osm')); ?></span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Leaflet Map Canvas Container -->
        <div class="rounded-xl overflow-hidden border border-slate-200 relative">
            <div
                id="<?php echo e($id); ?>"
                data-component="map"
                data-map-config="<?php echo e(json_encode($mapConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)); ?>"
                style="height: <?php echo e($height); ?>; min-height: 280px;"
                class="w-full z-0 bg-slate-50"
            ></div>
        </div>
    </div>
<?php else: ?>
    <!-- Fallback Card for Missing or Invalid Coordinates -->
    <div <?php echo e($attributes->merge(['class' => 'p-8 rounded-2xl bg-white border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center space-y-3'])); ?>>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center mb-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-slate-900 tracking-tight"><?php echo e(__('locations.map.no_coordinates')); ?></h4>
            <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed"><?php echo e(__('locations.map.no_coordinates_desc')); ?></p>
        </div>
        <?php if($editUrl): ?>
            <a
                href="<?php echo e($editUrl); ?>"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs hover:shadow transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span><?php echo e(__('locations.map.set_coordinates')); ?></span>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH /app/applet/resources/views/components/map.blade.php ENDPATH**/ ?>