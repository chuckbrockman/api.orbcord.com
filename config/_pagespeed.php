<?php
/**
 *
 *
 * @author Chuck Brockman <chuck@orbcord.com>
 */
return [
    // Backblaze B2 bucket
    'bucket' => 'lighthouse',

    // Google Pagespeed Insights
    'google' => [
        'key' => env('GOOGLE_PAGESPEED_INSIGHTS_API_KEY'),
    ],

    // GTMetrix API config
    'gtmetrix' => [
        'username' => env('GTMETRIX_API_USERNAME'),
        'key' => env('GTMETRIX_API_KEY'),
    ],

    'load_time_bounce_rate' => [
        [
            'time' => 1,
            'bounce_rate' => 7,
        ],
        [
            'time' => 2,
            'bounce_rate' => 6,
        ],
        [
            'time' => 3,
            'bounce_rate' => 11,
        ],
        [
            'time' => 4,
            'bounce_rate' => 24,
        ],
        [
            'time' => 5,
            'bounce_rate' => 38,
        ],
        [
            'time' => 6,
            'bounce_rate' => 46,
        ],
        [
            'time' => 7,
            'bounce_rate' => 53,
        ],
        [
            'time' => 8,
            'bounce_rate' => 59,
        ],
        [
            'time' => 9,
            'bounce_rate' => 61,
        ],
        [
            'time' => 10,
            'bounce_rate' => 65,
        ],
        [
            'time' => 11,
            'bounce_rate' => 67,
        ],
        [
            'time' => 12,
            'bounce_rate' => 67,
        ],
        [
            'time' => 13,
            'bounce_rate' => 69,
        ],
        [
            'time' => 14,
            'bounce_rate' => 69,
        ],
        [
            'time' => 15,
            'bounce_rate' => 69,
        ],
        [
            'time' => 16,
            'bounce_rate' => 73,
        ],
    ],
    'load_time_conversion_rate' => [

    ]
];
