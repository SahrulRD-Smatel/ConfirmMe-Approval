<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Work Schedule
    |--------------------------------------------------------------------------
    */
    'work_hours' => [
        'start' => '08:00',
        'end' => '17:00',
        'late_tolerance_minutes' => 15, // 08:15 still on_time
        'early_leave_threshold_minutes' => 30, // leaving 30+ min early = early_leave
    ],

    'break_hours' => [
        'start' => '12:00',
        'end' => '13:00',
    ],

    /*
    |--------------------------------------------------------------------------
    | Geofencing
    |--------------------------------------------------------------------------
    */
    'geofencing' => [
        'enabled' => false, // Set to true when GPS validation is required
        'default_radius_meters' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Selfie / Photo
    |--------------------------------------------------------------------------
    */
    'photo' => [
        'required' => true,
        'max_size_kb' => 2048, // 2MB
        'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
        'storage_disk' => 'public',
        'storage_path' => 'attendances',
    ],

    /*
    |--------------------------------------------------------------------------
    | WFH (Work From Home)
    |--------------------------------------------------------------------------
    */
    'wfh' => [
        'enabled' => true,
        'requires_photo' => true,
        'requires_location' => false, // No strict geofencing for WFH
        'requires_notes' => true, // WFH must provide reason/notes
    ],

    /*
    |--------------------------------------------------------------------------
    | Working Days (ISO day numbers: 1=Monday ... 7=Sunday)
    |--------------------------------------------------------------------------
    */
    'working_days' => [1, 2, 3, 4, 5], // Mon-Fri

    /*
    |--------------------------------------------------------------------------
    | Features Toggle
    |--------------------------------------------------------------------------
    */
    'features' => [
        'break_tracking' => true,
        'overtime_tracking' => false,
        'auto_checkout' => false,  // Auto check-out at end of day
        'auto_checkout_time' => '22:00',
    ],
];
