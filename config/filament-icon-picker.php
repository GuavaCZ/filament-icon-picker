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

];
