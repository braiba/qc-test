<?php

namespace App\Services\GifService;

use App\Factories\GuzzleHttpClientFactory;
use App\Services\GifService\Drivers\FakerGifDriver;
use App\Services\GifService\Drivers\GiphyGifDriver;
use InvalidArgumentException;

/**
 * Factory class for GIF providers
 *
 * @package App\Services\GifService
 */
class GifProviderFactory
{
    /**
     * Creates a GIF provider
     *
     * @param string $driver the name of the driver for the GIF provider
     * @param array $config the config for the GIF provider
     *
     * @return GifDriverInterface
     *
     * @throws InvalidArgumentException
     */
    public function make(string $driver, array $config): GifDriverInterface
    {
        switch ($driver) {
            case 'faker':
                return app(FakerGifDriver::class);

            case 'giphy':
                /** @var GuzzleHttpClientFactory $httpClientFactory */
                $httpClientFactory = app(GuzzleHttpClientFactory::class);
                $clientConfig = [
                    'base_uri' => $config['base_url'],
                ];

                $httpClient = $httpClientFactory->create($clientConfig);

                return new GiphyGifDriver($httpClient, $config['api_key']);
        }

        throw new InvalidArgumentException("Unsupported driver [{$driver}]");
    }
}
