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
    'amqp' => [
        'exchange_name' => env('GIF_AMQP_EXCHANGE_NAME', 'quidco'),
        'exchange_type' => env('GIF_AMQP_EXCHANGE_TYPE', 'topic'),
        'queue_name' => env('GIF_AMQP_QUEUE_NAME', 'gifs'),
    ],
    'options' => [
        'cache_ttl' => env('GIF_CACHE_TTL', 0),
    ],
];
