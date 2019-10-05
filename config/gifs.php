<?php

return [
    'default' => env('GIF_PROVIDER', 'giphy-production'),
    'providers' => [
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
];
