<?php

return [
    'title' => 'Maritime Carrier Directory',
    'subtitle' => 'Authorized container lines, fleet operators, and logistics service providers',
    'add_carrier' => 'Register New Carrier',
    'edit_carrier' => 'Edit Carrier',
    'edit_subtitle' => 'Update carrier operational details and contact credentials.',
    'details_title' => 'Carrier Profile',
    'profile_subtitle' => 'Carrier profile, contact details, and connected shipments.',
    'back_to_carriers' => 'Back to Carriers',
    'enable_carrier_help' => 'Enable this carrier for shipment assignment',
    'recent_shipments' => 'Recent Shipments Carried',
    'not_provided' => 'Not Provided',
    'no_shipments' => 'No shipments found for this carrier.',
    'search_placeholder' => 'Search name, code, email...',
    'delete_confirm' => 'Are you sure you want to delete carrier :name?',

    'fields' => [
        'name' => 'Carrier Name',
        'code' => 'SCAC / Carrier Code',
        'contact_email' => 'Operations Contact Email',
        'contact_phone' => 'Contact Telephone',
        'is_active' => 'Operational Status',
        'assigned_shipments' => 'Assigned Shipments',
    ],

    'table' => [
        'code' => 'Code',
        'carrier_name' => 'Carrier Name',
        'contact_info' => 'Contact Information',
        'active_shipments' => 'Active Shipments',
        'status' => 'Status',
        'actions' => 'Actions',
    ],

    'status' => [
        'active' => 'Active Service',
        'inactive' => 'Suspended / Inactive',
    ],

    'stats' => [
        'total_shipments' => 'Total Handled Shipments',
        'active_transits' => 'Active In-Transit',
        'delivered' => 'Delivered Shipments',
    ],

    'empty' => 'No carriers registered yet.',
];
