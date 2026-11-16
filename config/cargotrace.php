<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Delay Threshold Hours
    |--------------------------------------------------------------------------
    |
    | The default delay threshold (in hours) configured for the project group.
    | When a shipment's estimated arrival exceeds expected arrival by this many
    | hours or more, it is officially classified as DELAYED.
    | Default parameter: 24 hours (configurable to 12, 18, 24).
    |
    */
    'delay_threshold_hours' => (int) env('CARGOTRACE_DELAY_THRESHOLD', 24),

    /*
    |--------------------------------------------------------------------------
    | Delay Severity Thresholds
    |--------------------------------------------------------------------------
    |
    | Classification boundaries:
    | - LOW: 0 - 6 hours
    | - MEDIUM: > 6 - 24 hours
    | - HIGH: > 24 hours
    |
    */
    'delay_severity' => [
        'low_max' => 6,
        'medium_max' => 24,
    ],
];
