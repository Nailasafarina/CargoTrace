/**
 * Pure OpenStreetMap & CargoTrace geospatial utility functions.
 * Decoupled for deterministic unit testing and reuse across Leaflet map views.
 */

export const SMOOTH_MAP_OPTIONS = {
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
    inertiaDeceleration: 2800,
};

/**
 * Escape HTML entities to prevent DOM-based XSS inside Leaflet popups.
 */
export function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Validate whether latitude and longitude form a valid WGS84 coordinate pair.
 */
export function isValidCoordinate(lat, lng) {
    if (lat === null || lat === undefined || lng === null || lng === undefined || lat === '' || lng === '') {
        return false;
    }
    const numLat = Number(lat);
    const numLng = Number(lng);
    return !Number.isNaN(numLat) && !Number.isNaN(numLng) && numLat >= -90 && numLat <= 90 && numLng >= -180 && numLng <= 180;
}

/**
 * Resolve facility pin styling by location type (Camellia Pink & Obsidian palette).
 */
export function getPinStyle(type) {
    const normalized = String(type || '').toLowerCase();
    switch (normalized) {
        case 'port':
            return {
                color: '#e04879',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a3 3 0 00-3 3v2H6a2 2 0 00-2 2v2a2 2 0 002 2h1v4a5 5 0 0010 0v-4h1a2 2 0 002-2V9a2 2 0 00-2-2h-3V5a3 3 0 00-3-3z"/>',
            };
        case 'airport':
            return {
                color: '#1f171b',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
            };
        case 'warehouse':
            return {
                color: '#9e224b',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
            };
        case 'city':
        default:
            return {
                color: '#10b981',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
            };
    }
}

/**
 * Compute smooth curved intermediate points (quadratic Bezier arc) between two coordinates
 * so route lines on the OpenStreetMap widget have a realistic S-curve/arc like the reference screenshot.
 */
export function buildCurvedSegment(startPt, endPt, curvature = 0.16, steps = 24) {
    const lat1 = Number(startPt[0]);
    const lng1 = Number(startPt[1]);
    const lat2 = Number(endPt[0]);
    const lng2 = Number(endPt[1]);

    if (!isValidCoordinate(lat1, lng1) || !isValidCoordinate(lat2, lng2)) {
        return [];
    }

    const dLat = lat2 - lat1;
    const dLng = lng2 - lng1;
    const midLat = (lat1 + lat2) / 2 + (-dLng * curvature);
    const midLng = (lng1 + lng2) / 2 + (dLat * curvature);

    const points = [];
    for (let i = 0; i <= steps; i++) {
        const t = i / steps;
        const oneMinusT = 1 - t;
        const lat = oneMinusT * oneMinusT * lat1 + 2 * oneMinusT * t * midLat + t * t * lat2;
        const lng = oneMinusT * oneMinusT * lng1 + 2 * oneMinusT * t * midLng + t * t * lng2;
        points.push([Number(lat.toFixed(5)), Number(lng.toFixed(5))]);
    }
    return points;
}
