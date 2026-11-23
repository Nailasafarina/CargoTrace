<?php

return [
    'title' => 'Notification Center',
    'subtitle' => 'Real-time operational alerts, milestone tracking events, delay status updates, and exception advisories.',
    'unread_count' => ':count Unread',
    'all_caught_up' => 'All Caught Up',
    'mark_all_read' => 'Mark All as Read',
    'mark_read' => 'Mark as read',
    'read_status' => 'Read',
    'open_shipment' => 'Open Shipment',

    'empty' => [
        'title' => 'No Notifications',
        'description' => 'You have no operational notifications or alerts at this time.',
    ],

    'messages' => [
        'shipment_delay_alert' => 'Shipment :number delay alert (:classification): :reason',
        'shipment_delay_internal' => 'Operator Alert: :number flagged as :classification delay (:delay_hours h).',
        'shipment_exception_alert' => 'Shipment :number exception recorded [:type]: :description',
        'shipment_exception_internal' => 'Exception alert on :number [:type - Severity: :severity]. Operator action required.',
        'shipment_exception_resolved' => 'Shipment :number exception resolved [:type].',
        'shipment_milestone_updated' => 'Shipment :number status updated to :status: :description',
        'active_exception' => 'Shipment :number has an active exception: :type.',
        'legacy' => ':text',
    ],
];
