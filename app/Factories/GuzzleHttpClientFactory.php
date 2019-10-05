<?php

namespace App\Factories;

use GuzzleHttp\Client;

/**
 * Factory class for GuzzleHttp\Client. This exists to allow us to mock out calls to the constructor
 *
 * @package App\Factories
 */
class GuzzleHttpClientFactory
{
    /**
     * Creates a GuzzleHttp client
     *
     * @param array $config the config for the client
     *
     * @return Client the GuzzleHttp client
     */
    public function create(array $config = []): Client
    {
        return new Client($config);
    }
}
