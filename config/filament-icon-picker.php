<?php

return [

    'cache' => [
        /*
         * Whether icon listings and rendered SVGs are cached server-side.
         */
        'enabled' => env('ICON_PICKER_CACHE_ENABLED', true),

        /*
         * How long cached entries live. Any strtotime-compatible interval.
         * Custom icon caches are invalidated on upload regardless of this value.
         */
        'duration' => '7 days',

        'prefix' => 'guava-icon-picker',
    ],

    'routes' => [
        /*
         * URI prefix of the icon picker endpoints.
         */
        'prefix' => '_icon-picker',

        /*
         * Middleware of the icon picker endpoints. Access control itself comes
         * from the encrypted token issued at render time, not the middleware.
         */
        'middleware' => ['web', 'throttle:120,1'],
    ],

    /*
     * How long an issued picker token stays valid. Any strtotime-compatible
     * interval. Forms left open longer need a page reload to fetch icons again.
     */
    'token_lifetime' => '12 hours',

];
