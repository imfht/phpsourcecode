<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'table'  => 'cache',
            'connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache'),
        ],

        'memcached' => [
            'driver'  => 'memcached',
            'servers' => [
                [
                    'host' => '127.0.0.1', 'port' => 11211, 'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        /**自定义仓库*/
        'person' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/person'),
        ],
        'category' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/category'),
        ],
        'article' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/article'),
        ],
        'project' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/project'),
        ],
        'page' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/page'),
        ],
        'menu' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/menu'),
        ],
        'ads' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/ads'),
        ],
        'adspaces' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/adspaces'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => 'laravel',

];
