<?php

return [

    /*
    |--------------------------------------------------------------------------

    | Application Name

    |--------------------------------------------------------------------------

    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------

    | Application Environment

    |--------------------------------------------------------------------------

    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------

    | Application Debug Mode

    |--------------------------------------------------------------------------

    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------

    | Application URL

    |--------------------------------------------------------------------------

    */

    'url' => env('APP_URL', 'http://localhost'),

    'test_url' => env('TEST_APP_URL'),

    'production_url' => env('PRODUCTION_APP_URL', 'https://kennisbank.sitenomads.nl'),

    'timezone' => 'UTC',

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
