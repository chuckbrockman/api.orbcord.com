<?php
/**
 * GTMetrix API config
 *
 * @author Chuck Brockman <chuck@orbcord.com>
 */
return [
    // Backblaze B2 bucket
    'bucket' => 'gtmetrix',

    'api' => [
        'username' => env('GTMETRIX_API_USERNAME'),
        'key' => env('GTMETRIX_API_KEY'),
    ]

];
