<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Google Application Credentials
    |--------------------------------------------------------------------------
    |
    | Configure the authentication for Google APIs.
    | For service account auth, download the JSON key from Google Cloud Console
    | and place it in the storage/app directory.
    |
    */

    'service' => [
        'enable' => env('GOOGLE_SERVICE_ENABLED', false),
        'file' => env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION', storage_path('app/google-service-account.json')),
    ],

    'client_id' => env('GOOGLE_CLIENT_ID', ''),
    'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
    'redirect_uri' => env('GOOGLE_REDIRECT', ''),
    'developer_key' => env('GOOGLE_DEVELOPER_KEY', ''),

    'scopes' => [
        'https://www.googleapis.com/auth/spreadsheets',
        'https://www.googleapis.com/auth/drive',
    ],

    'access_type' => 'offline',
    'approval_prompt' => 'force',
];
