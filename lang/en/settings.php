<?php

return [
    'title' => 'System Configuration & Parameters',
    'subtitle' => 'Manage global operational thresholds and dynamic ETA delay criteria',
    'updated_status' => 'Delay threshold settings updated successfully.',
    'card_title' => 'Delay Threshold Settings',
    'card_subtitle' => 'Configure tolerance hours before ETA delays trigger operational alerts',
    'threshold_label' => 'Delay Alert Threshold (Hours) *',
    'hours_suffix' => 'Hours',
    'group_presets' => 'Group Presets:',
    'group_standard' => '24h (Group Standard)',
    're_evaluate_now' => 'Re-evaluate all active shipments immediately',
    're_evaluate_desc' => 'Applies the new threshold across all non-delivered corridor shipments right after saving.',
    'save_button' => 'Save & Apply Configuration',
    'scale_title' => 'Delay Severity Scale',
    'scale_intro' => 'Once a delay surpasses the active threshold, it is automatically categorized according to operational SLA standards:',
    'low_desc' => 'Minor schedule variation; port turn-around buffer.',
    'medium_desc' => 'Requires transshipment monitoring and customer advisory.',
    'high_desc' => 'Major disruption; immediate operations escalation.',
    'automation_title' => 'Scheduled Automation',
    'automation_desc' => 'The Artisan command shipments:evaluate-delays is registered to run on an hourly schedule to audit active vessel transits.',
];
