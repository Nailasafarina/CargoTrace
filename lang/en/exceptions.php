<?php

return [
    'title' => 'Exceptions & Disruption Advisories',
    'subtitle' => 'Active operational impediments, customs holds, and resolution workflows',
    'log_exception' => 'Log Exception',

    'types' => [
        'customs_hold' => 'Customs Regulatory Hold',
        'weather_delay' => 'Adverse Weather Delay',
        'port_congestion' => 'Port Berth Congestion',
        'damaged' => 'Cargo Packaging Damage',
        'misrouted' => 'Carrier Transshipment Misroute',
        'documentation_issue' => 'Missing Documentation Issue',
    ],

    'status' => [
        'open' => 'Open',
        'investigating' => 'Under Investigation',
        'resolved' => 'Resolved & Cleared',
    ],

    'severity' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ],

    'form' => [
        'type' => 'Exception Type',
        'severity' => 'Severity Level',
        'detected_at' => 'Detected At (UTC)',
        'description' => 'Description & Root Cause',
        'resolution_notes' => 'Resolution Notes',
        'update_status' => 'Update Status',
        'resolve_button' => 'Resolve Exception',
    ],

    'no_exceptions' => 'No active or historical exceptions recorded for this shipment.',
];
