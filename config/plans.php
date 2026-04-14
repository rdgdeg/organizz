<?php

return [
    'free' => [
        'max_active_events' => 2,
        'max_positions_per_event' => 3,
        'max_registrations_per_event' => 50,
        'max_reminder_rules_per_event' => 1,
        'csv_export' => false,
    ],
    'pro' => [
        'max_active_events' => PHP_INT_MAX,
        'max_positions_per_event' => PHP_INT_MAX,
        'max_registrations_per_event' => PHP_INT_MAX,
        'max_reminder_rules_per_event' => PHP_INT_MAX,
        'csv_export' => true,
    ],
];
