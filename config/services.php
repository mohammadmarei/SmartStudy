<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
        // Note: Anthropic does not currently publish a "Sonnet 4.7" model id.
        // Keep this configurable so you can switch without code changes.
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'version' => env('ANTHROPIC_VERSION', '2023-06-01'),
    ],
    // SSL/TLS Certificate Verification
    // Set to 'false' or false to disable (Windows development environments)
    // Set to a path string to use a specific CA bundle file
    // Leave empty to use system defaults or php.ini settings
    'http_verify_cert' => env('HTTP_VERIFY_CERT', ''),
];
