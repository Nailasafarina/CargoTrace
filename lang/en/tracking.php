<?php

return [
    'timeline_title' => 'Milestone Timeline',
    'timeline_subtitle' => 'Chronological log of port departures, transshipment checkpoints, and arrival telemetry',
    'record_event' => 'Record Milestone Event',

    'event_types' => [
        'order_created' => 'Booking Order Created',
        'picked_up' => 'Cargo Picked Up',
        'departed' => 'Vessel Departed Port',
        'in_transit' => 'In Transit / Ocean Passage',
        'transshipment_arrival' => 'Arrived at Transshipment Hub',
        'customs_hold' => 'Customs Hold Placed',
        'customs_cleared' => 'Customs Cleared',
        'arrived_destination' => 'Arrived at Destination Port',
        'out_for_delivery' => 'Out for Final Delivery',
        'delivered' => 'Delivered to Consignee',
        'exception_alert' => 'Shipment Exception Alert',
    ],

    'modal' => [
        'title' => 'Add Tracking Milestone',
        'event_type' => 'Event Type',
        'location' => 'Location Checkpoint',
        'occurred_at' => 'Event Timestamp (UTC)',
        'description' => 'Milestone Description',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'is_exception' => 'Mark as Exception Milestone',
    ],

    'empty_timeline' => 'No tracking events recorded yet. Record the first milestone above.',
];
