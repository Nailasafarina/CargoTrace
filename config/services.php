<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'geocoding' => [
        'base_url' => env('GEOCODING_BASE_URL', 'https://nominatim.openstreetmap.org'),
        'timeout' => (int) env('GEOCODING_TIMEOUT', 5),
        'retry_times' => (int) env('GEOCODING_RETRY_TIMES', 2),
        'user_agent' => env('GEOCODING_USER_AGENT', 'CargoTrace/1.0 (contact@cargotrace.test)'),
        'cache_ttl' => (int) env('GEOCODING_CACHE_TTL', 86400), // 24 hours
    ],

    'weather' => [
        'provider' => env('WEATHER_PROVIDER', 'open-meteo'),
        'open_meteo_url' => env('OPEN_METEO_BASE_URL', 'https://api.open-meteo.com/v1/forecast'),
        'openweather_url' => env('OPENWEATHER_BASE_URL', 'https://api.openweathermap.org/data/2.5/weather'),
        'openweather_api_key' => env('OPENWEATHER_API_KEY'),
        'timeout' => (int) env('WEATHER_TIMEOUT', 5),
        'retry_times' => (int) env('WEATHER_RETRY_TIMES', 2),
        'cache_ttl' => (int) env('WEATHER_CACHE_TTL', 3600), // 1 hour
    ],

];
