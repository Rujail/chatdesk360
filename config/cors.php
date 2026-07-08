<?php

return [

    // 🔴 IMPORTANT: include ALL your routes
    'paths' => [
        'api/*',
        'chat/*',
        'visitor/*',
        'widget/*',
        'livechat/*',
    ],

    'allowed_methods' => ['*'],

    // ⚠️ for testing (OK), later restrict it
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // IMPORTANT for your case (AJAX JSON requests)
    'supports_credentials' => false,
];