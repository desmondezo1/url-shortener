<?php

return [
    /*
    |--------------------------------------------------------------------------
    | URL Shortener Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the URL shortener service.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | This is the base URL used for constructing shortened URL links.
    | If not specified, the application's URL will be used.
    |
    */
    'base_url' => env('SHORT_URL_BASE', null),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | The time (in seconds) that URL mappings will be stored in the cache.
    | Default is 1 hour (3600 seconds).
    |
    */
    'cache_ttl' => env('URL_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Short Code Length
    |--------------------------------------------------------------------------
    |
    | The length of the generated short codes. Longer codes allow for more
    | unique URLs but result in longer shortened URLs.
    |
    */
    'code_length' => env('SHORT_CODE_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | Maximum Collision Resolution Attempts
    |--------------------------------------------------------------------------
    |
    | The maximum number of attempts to resolve a collision when generating
    | a short code before throwing an exception.
    |
    */
    'max_collision_attempts' => env('MAX_COLLISION_ATTEMPTS', 5),
];
