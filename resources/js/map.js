import L from 'leaflet';
import {
    SMOOTH_MAP_OPTIONS,
    escapeHtml,
    isValidCoordinate,
    getPinStyle,
    buildCurvedSegment,
} from './map-utils.js';

if (typeof window !== 'undefined') {
    window.buildCurvedSegment = buildCurvedSegment;
    window.SMOOTH_MAP_OPTIONS = SMOOTH_MAP_OPTIONS;
    window.escapeHtml = escapeHtml;
}

/**
 * Generate a modern, retina-ready Leaflet divIcon.
 */
function createLocationPin(type, isDraggable = false) {
    const style = getPinStyle(type);
    const pulseRing = isDraggable ? '<span class="absolute -inset-1.5 rounded-full bg-[#e04879]/30 animate-ping"></span>' : '';

    return L.divIcon({
        className: 'cargotrace-pin-wrapper',
        html: `
            <div class="relative flex items-center justify-center">
                ${pulseRing}
                <div style="background-color: ${style.color};" class="w-8 h-8 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white transition-transform duration-200 hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${style.svg}
                    </svg>
                </div>
            </div>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -18],
    });
}

/**
 * Safely format popup content with sanitized HTML strings.
 */
function buildPopupContent(marker) {
    const i18n = window.i18n?.leaflet || {};
    const title = escapeHtml(marker.title || marker.name || '');
    const typeLabel = escapeHtml(marker.typeLabel || marker.type || '');
    const locationStr = escapeHtml(
        marker.location ||
        (marker.city ? (marker.country ? `${marker.city}, ${marker.country}` : marker.city) : '')
    );

    const lat = Number(marker.lat ?? marker.latitude);
    const lng = Number(marker.lng ?? marker.longitude);
    const coordsValid = !isNaN(lat) && !isNaN(lng);
    const coordsStr = coordsValid ? `${lat.toFixed(4)}, ${lng.toFixed(4)}` : '';
    const url = marker.url ? encodeURI(marker.url) : null;

    return `
        <div class="text-xs space-y-1.5 p-1 min-w-[200px]">
            ${typeLabel ? `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#e04879]/20 text-[#ec6e9b] border border-[#e04879]/30">${typeLabel}</span>` : ''}
            <strong class="text-white text-sm font-bold block">${title}</strong>
            ${locationStr ? `<div class="text-zinc-300 text-xs flex items-center gap-1">${locationStr}</div>` : ''}
            ${coordsStr ? `<div class="text-[10px] font-mono text-zinc-400">${escapeHtml(i18n.coordinates || 'Coordinates')}: ${coordsStr}</div>` : ''}
            ${url ? `<div class="pt-1.5 border-t border-zinc-800"><a href="${url}" class="text-[#ec6e9b] hover:text-white hover:underline font-semibold text-xs inline-flex items-center gap-1">${escapeHtml(i18n.view_details || 'View Details')} &rarr;</a></div>` : ''}
        </div>
    `;
}

/**
 * Build a crisp hover tooltip showing hub name and city/country without clicking.
 */
function buildTooltipContent(marker) {
    const title = escapeHtml(marker.title || marker.name || '');
    const typeLabel = escapeHtml(marker.typeLabel || marker.type || '');
    const locationStr = escapeHtml(
        marker.location ||
        (marker.city ? (marker.country ? `${marker.city}, ${marker.country}` : marker.city) : '')
    );

    return `
        <div class="px-2 py-1 text-left leading-tight">
            <div class="text-[11px] font-extrabold text-[#1A1718]">${title}</div>
            ${locationStr ? `<div class="text-[10px] font-medium text-[#6B6467]">${locationStr}${typeLabel ? ` &bull; <span class="text-[#BE2B5B] font-bold uppercase">${typeLabel}</span>` : ''}</div>` : ''}
        </div>
    `;
}

/**
 * Attach Esri World Street Map (no-API-key required) tile layer as default with an optional layer switcher for OpenStreetMap Standard.
 */
function attachTileLayer(map, includeLayerSwitcher = true) {
    const esriStreetLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        keepBuffer: 6,
        updateWhenZooming: false,
        updateWhenIdle: false,
        attribution: 'Tiles &copy; Esri'
    });

    const osmStandardLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        keepBuffer: 6,
        updateWhenZooming: false,
        updateWhenIdle: false,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer" class="text-[#c93565] hover:underline font-medium">OpenStreetMap</a> contributors'
    });

    esriStreetLayer.on('tileerror', function (err) {
        console.warn('Map tile loading issue handled gracefully:', err);
    });

    esriStreetLayer.addTo(map);

    if (includeLayerSwitcher) {
        L.control.layers(
            {
                'Clean Map (Esri Street)': esriStreetLayer,
                'Detailed Map (OpenStreetMap)': osmStandardLayer,
            },
            null,
            { position: 'topright', collapsed: true }
        ).addTo(map);
    }

    return esriStreetLayer;
}

/**
 * Handle initial sizing and dynamic resize observer.
 */
function setupResizeObserver(map, container) {
    setTimeout(() => {
        try { map.invalidateSize(); } catch (e) {}
    }, 200);

    window.addEventListener('resize', () => {
        try { map.invalidateSize(); } catch (e) {}
    });

    if (window.ResizeObserver) {
        const ro = new ResizeObserver(() => {
            try { map.invalidateSize(); } catch (e) {}
        });
        ro.observe(container);
    }
}

/**
 * Initialize a single location map (detail view) with smooth fly-to on click.
 */
function initSingleMap(container, config) {
    const lat = parseFloat(config.latitude);
    const lng = parseFloat(config.longitude);

    if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        return null;
    }

    const zoom = parseInt(config.zoom, 10) || 12;
    const map = L.map(container, SMOOTH_MAP_OPTIONS).setView([lat, lng], zoom);

    attachTileLayer(map);

    const markerData = config.marker || {
        lat: lat,
        lng: lng,
        title: config.title || '',
        type: config.type || '',
        typeLabel: config.typeLabel || '',
        city: config.city || '',
        country: config.country || '',
    };

    const marker = L.marker([lat, lng], {
        icon: createLocationPin(markerData.type)
    }).addTo(map);

    marker.bindTooltip(buildTooltipContent(markerData), {
        direction: 'top',
        offset: [0, -16],
        opacity: 0.98,
        className: 'cargotrace-leaflet-tooltip',
    });
    marker.bindPopup(buildPopupContent(markerData));

    marker.on('click', () => {
        map.flyTo([lat, lng], Math.max(map.getZoom(), 13), {
            animate: true,
            duration: 0.75,
            easeLinearity: 0.25,
        });
    });

    setTimeout(() => {
        try { marker.openPopup(); } catch (e) {}
    }, 300);

    setupResizeObserver(map, container);
    return map;
}

/**
 * Initialize a multi-location summary map (index view) with smooth cluster bounds & fly-to.
 */
function initMultiMap(container, config) {
    const markers = Array.isArray(config.markers) ? config.markers : [];
    const validMarkers = markers.filter(m => {
        const lat = parseFloat(m.lat ?? m.latitude);
        const lng = parseFloat(m.lng ?? m.longitude);
        return !isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
    });

    if (validMarkers.length === 0) {
        return null;
    }

    const map = L.map(container, SMOOTH_MAP_OPTIONS);

    attachTileLayer(map);

    const featureGroup = L.featureGroup();

    validMarkers.forEach(m => {
        const lat = parseFloat(m.lat ?? m.latitude);
        const lng = parseFloat(m.lng ?? m.longitude);
        const pin = L.marker([lat, lng], {
            icon: createLocationPin(m.type)
        });
        pin.bindTooltip(buildTooltipContent(m), {
            direction: 'top',
            offset: [0, -16],
            opacity: 0.98,
            className: 'cargotrace-leaflet-tooltip',
        });
        pin.bindPopup(buildPopupContent(m));
        pin.on('click', () => {
            map.flyTo([lat, lng], Math.max(map.getZoom(), 6), {
                animate: true,
                duration: 0.75,
                easeLinearity: 0.25,
            });
        });
        pin.addTo(featureGroup);
    });

    featureGroup.addTo(map);

    if (validMarkers.length === 1) {
        const first = validMarkers[0];
        map.setView([parseFloat(first.lat ?? first.latitude), parseFloat(first.lng ?? first.longitude)], 12);
    } else {
        map.fitBounds(featureGroup.getBounds(), { padding: [50, 50], maxZoom: 6, animate: true });
    }

    setupResizeObserver(map, container);
    return map;
}

/**
 * Initialize an interactive coordinate picker map (create/edit forms).
 */
function initPickerMap(container, config) {
    const latInput = config.latInput ? document.querySelector(config.latInput) : null;
    const lngInput = config.lngInput ? document.querySelector(config.lngInput) : null;

    let initLat = parseFloat(config.latitude || (latInput ? latInput.value : null));
    let initLng = parseFloat(config.longitude || (lngInput ? lngInput.value : null));

    const hasValidCoords = !isNaN(initLat) && !isNaN(initLng) && initLat >= -90 && initLat <= 90 && initLng >= -180 && initLng <= 180;

    const center = hasValidCoords ? [initLat, initLng] : [20, 0];
    const zoom = hasValidCoords ? (parseInt(config.zoom, 10) || 12) : 2;

    const map = L.map(container, SMOOTH_MAP_OPTIONS).setView(center, zoom);

    attachTileLayer(map);

    let marker = null;

    function updateInputs(lat, lng) {
        if (latInput) {
            latInput.value = lat.toFixed(7);
            latInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (lngInput) {
            lngInput.value = lng.toFixed(7);
            lngInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function setMarkerPosition(lat, lng, pan = false) {
        if (!marker) {
            marker = L.marker([lat, lng], {
                draggable: true,
                icon: createLocationPin('port', true)
            }).addTo(map);

            marker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                updateInputs(pos.lat, pos.lng);
            });
        } else {
            marker.setLatLng([lat, lng]);
        }

        if (pan) {
            map.flyTo([lat, lng], Math.max(map.getZoom(), 10), {
                animate: true,
                duration: 0.65,
            });
        }
    }

    if (hasValidCoords) {
        setMarkerPosition(initLat, initLng, false);
    }

    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        setMarkerPosition(lat, lng, true);
        updateInputs(lat, lng);
    });

    function handleInputChange() {
        if (!latInput || !lngInput) return;
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            setMarkerPosition(lat, lng, true);
        }
    }

    if (latInput) latInput.addEventListener('input', handleInputChange);
    if (lngInput) lngInput.addEventListener('input', handleInputChange);

    setupResizeObserver(map, container);
    return map;
}

/**
 * Master initializer for all data-component="map" containers on the page.
 */
export function initCargoTraceMaps() {
    const mapElements = document.querySelectorAll('[data-component="map"]');
    mapElements.forEach(container => {
        if (container._cargoTraceMapInitialized) {
            return;
        }

        let config = {};
        try {
            config = JSON.parse(container.getAttribute('data-map-config') || '{}');
        } catch (e) {
            console.error('Invalid map config JSON:', e);
            return;
        }

        const mode = config.mode || 'single';
        let instance = null;

        if (mode === 'single') {
            instance = initSingleMap(container, config);
        } else if (mode === 'multi') {
            instance = initMultiMap(container, config);
        } else if (mode === 'picker') {
            instance = initPickerMap(container, config);
        }

        if (instance) {
            container._cargoTraceMapInitialized = true;
            container._leafletMapInstance = instance;
        }
    });
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCargoTraceMaps);
    } else {
        initCargoTraceMaps();
    }
}

if (typeof window !== 'undefined') {
    window.initCargoTraceMaps = initCargoTraceMaps;
}
