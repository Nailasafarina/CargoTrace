<?php

return [
    'leaflet' => [
        'origin' => 'Origin Port',
        'destination' => 'Destination Port',
        'current_position' => 'Current Milestone Location',
        'status' => 'Status',
        'coordinates' => 'Coordinates',
        'carrier' => 'Carrier',
        'eta' => 'ETA',
        'waypoint' => 'Corridor Transit Waypoint',
        'na' => 'N/A',
        'view_details' => 'View Details',
        'facility_type' => 'Facility Type',
        'location' => 'Location',
        'drag_marker_hint' => 'Drag pin to adjust GPS coordinates',
        'tile_error' => 'Map tiles could not be loaded.',
    ],

    'charts' => [
        'created' => 'Created',
        'booked' => 'Booked',
        'picked_up' => 'Picked Up',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'on_time' => 'On Time',
        'low' => 'Low Delay',
        'medium' => 'Medium Delay',
        'high' => 'High Delay',
        'shipments_count' => 'Shipments Count',
    ],

    'geocoding' => [
        'loading' => 'Geocoding address via OpenStreetMap...',
        'success' => 'Coordinates found and applied!',
        'failed' => 'Failed to geocode address. Please enter coordinates manually.',
        'min_chars' => 'Please enter at least 3 characters to geocode.',
        'network_error' => 'Network error while geocoding address.',
    ],

    'confirm' => [
        'delete_shipment' => 'Are you sure you want to delete this shipment?',
        'delete_record' => 'Are you sure you want to delete this record?',
        'remove_item' => 'Remove this cargo item?',
    ],
];
