<?php

return [
    'default' => env('GIF_PROVIDER', 'faker'),
    'providers' => [
        'faker' => [
            'driver' => 'faker',
        ],
        'giphy-beta' => [
            'driver' => 'giphy',
            'base_url' => env('GIPHY_URL', 'http://api.giphy.com'),
            'api_key' => env('GIPHY_BETA_API_KEY', ''),
        ],
        'giphy-production' => [
            'driver' => 'giphy',
            'base_url' => env('GIPHY_URL', 'http://api.giphy.com'),
            'api_key' => env('GIPHY_PRODUCTION_API_KEY', ''),
        ],
    ],
    'options' => [
        'cache_ttl' => env('GIF_CACHE_TTL', 0),
    ],
];
