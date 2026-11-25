import test from 'node:test';
import assert from 'node:assert/strict';
import {
    SMOOTH_MAP_OPTIONS,
    escapeHtml,
    isValidCoordinate,
    getPinStyle,
    buildCurvedSegment,
} from './map-utils.js';

test('escapeHtml sanitizes HTML special characters against XSS', () => {
    assert.equal(escapeHtml('<script>alert("xss")</script>'), '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;');
    assert.equal(escapeHtml("Port's & Terminal"), 'Port&#039;s &amp; Terminal');
    assert.equal(escapeHtml(null), '');
});

test('isValidCoordinate validates WGS84 latitude and longitude bounds', () => {
    assert.equal(isValidCoordinate(-6.1045, 106.8827), true);
    assert.equal(isValidCoordinate(51.95, 4.1333), true);
    assert.equal(isValidCoordinate(95, 10), false);
    assert.equal(isValidCoordinate(10, 190), false);
    assert.equal(isValidCoordinate(null, 106.8), false);
    assert.equal(isValidCoordinate('', ''), false);
});

test('getPinStyle returns NexCargo palette colors for facility types', () => {
    assert.equal(getPinStyle('port').color, '#ff6b1a');
    assert.equal(getPinStyle('AIRPORT').color, '#18181b');
    assert.equal(getPinStyle('warehouse').color, '#ea580c');
    assert.equal(getPinStyle('city').color, '#10b981');
});

test('SMOOTH_MAP_OPTIONS configures fractional zoomSnap for smooth OpenStreetMap zooming', () => {
    assert.equal(SMOOTH_MAP_OPTIONS.zoomSnap, 0.25);
    assert.equal(SMOOTH_MAP_OPTIONS.zoomDelta, 0.5);
    assert.equal(SMOOTH_MAP_OPTIONS.scrollWheelZoom, true);
    assert.equal(SMOOTH_MAP_OPTIONS.inertia, true);
});

test('buildCurvedSegment generates smooth intermediate arc coordinates between origin and destination', () => {
    const arc = buildCurvedSegment([-6.1045, 106.8827], [1.2644, 103.8222], 0.15, 10);
    assert.equal(arc.length, 11);
    assert.deepEqual(arc[0], [-6.1045, 106.8827]);
    assert.deepEqual(arc[10], [1.2644, 103.8222]);
});
