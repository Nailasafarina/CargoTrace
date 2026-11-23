<?php

return [
    'title' => 'Global Logistics Hub Directory',
    'subtitle' => 'Seaports, inland container depots, airports, and transshipment terminals',
    'add_location' => 'Register New Location',
    'edit_location' => 'Edit Location',
    'details_title' => 'Location Profile',
    'departing_shipments' => 'Departing Shipments',
    'arriving_shipments' => 'Arriving Shipments',
    'milestone_events' => 'Milestone Events Logged',
    'coords_title' => 'Geographic Coordinates (WGS84 Format)',
    'view_osm' => 'View on OpenStreetMap',
    'search_placeholder' => 'Search name, city, country...',
    'all_types' => 'All Facility Types',
    'delete_confirm' => 'Are you sure you want to delete :name?',

    'map' => [
        'title' => 'Location Map',
        'subtitle' => 'Interactive OpenStreetMap geospatial positioning',
        'no_coordinates' => 'Coordinates Not Available',
        'no_coordinates_desc' => 'This location does not have valid GPS coordinates configured yet.',
        'set_coordinates' => 'Set Coordinates',
        'summary_title' => 'Global Logistics Network Map',
        'summary_subtitle' => 'Interactive spatial overview of hubs, ports, and transit terminals',
        'toggle_summary_map' => 'Toggle Network Map',
        'picker_title' => 'Interactive Map Pin Picker',
        'picker_hint' => 'Click on the map or drag the marker to pinpoint latitude & longitude coordinates automatically.',
    ],

    'fields' => [
        'name' => 'Location Name',
        'code' => 'UN/LOCODE / Port Code',
        'type' => 'Facility Type',
        'city' => 'City Hub',
        'country' => 'Country',
        'country_code' => 'Country Code (ISO 2)',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'address' => 'Address / Physical Location',
    ],

    'table' => [
        'hub_name' => 'Hub / Facility Name',
        'type' => 'Type',
        'city_country' => 'Country & City',
        'coordinates' => 'GPS Coordinates (WGS84)',
        'shipments_ratio' => 'Shipments (Out / In)',
        'actions' => 'Actions',
    ],

    'types' => [
        'port' => 'Port',
        'airport' => 'Airport',
        'warehouse' => 'Warehouse',
        'city' => 'City Hub',
    ],

    'geocoding' => [
        'toolbar_title' => 'OpenStreetMap Geocoding Toolbar',
        'toolbar_desc' => 'Automatically lookup and populate geographic coordinates from address',
        'lookup_button' => 'Geocode Address',
        'idle' => 'Ready to geocode',
    ],

    'empty' => 'No locations registered yet.',
];
