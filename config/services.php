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

    'postmark'          => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'kosa_microservice' => [
        'base_url'               => env('KOSA_MICROSERVICE_BASE_URL', 'https://api-mcs-kosa.laravel.cloud'),
        'integration_target_key' => env('KOSA_INTEGRATION_TARGET_KEY'),
        'integration_source_key' => env('KOSA_INTEGRATION_SOURCE_KEY'),
    ],

    'ses'               => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend'            => [
        'key' => env('RESEND_KEY'),
    ],

    'slack'             => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];