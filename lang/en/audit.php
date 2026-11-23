<?php

return [
    'title' => 'System Audit Trails & Compliance',
    'subtitle' => 'Immutable record of all administrative, dispatch, and operational actions performed across the CargoTrace platform.',
    'total_logged' => 'Total Logged Events: :total',
    'filters_heading' => 'Audit Log Filters',
    'actor' => 'Actor (User)',
    'all_actors' => 'All Actors',
    'action' => 'Action',
    'all_actions' => 'All Actions',
    'entity_type' => 'Entity Type',
    'all_entities' => 'All Entities',
    'date_from' => 'Date From',
    'date_to' => 'Date To',

    'table' => [
        'timestamp' => 'Timestamp (UTC)',
        'actor' => 'Actor',
        'action' => 'Action',
        'target_entity' => 'Target Entity',
        'ip_address' => 'IP Address',
        'diff' => 'State Changes / Diff',
    ],

    'diff_modal' => [
        'inspect_button' => 'Inspect Diff &darr;',
        'snapshot_title' => 'Audit Snapshot: :entity #:id',
        'snapshot_subtitle' => 'Action: :action at :time UTC',
        'previous_state' => 'Previous State',
        'new_state' => 'New State',
        'no_state_change' => 'No state change',
    ],

    'system_actor' => 'System / Background Task',
    'empty' => 'No audit trails found matching the query criteria.',
];
