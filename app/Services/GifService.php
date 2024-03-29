<?php

namespace App\Services;

use App\Services\GifService\Exceptions\GifException;
use App\Services\GifService\GifData;
use App\Services\GifService\GifProviderFactory;
use App\Services\GifService\GifDriverInterface;
use function array_key_exists;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;

/**
 * A service for retrieving GIF data
 *
 * @package App\Services
 */
class GifService
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The gif provider factory instance.
     *
     * @var GifProviderFactory
     */
    protected $factory;

    /**
     * @var int
     */
    protected $cacheTtl;

    /**
     * @var array|GifDriverInterface[]
     */
    protected $providerMap = [];

    /**
     * GifService constructor.
     *
     * @param Application $app
     * @param GifProviderFactory $factory
     */
    public function __construct(Application $app, GifProviderFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
        $this->cacheTtl = config('gifs.options.cache_ttl');
    }

    /**
     * Returns a single GIF for a search query
     *
     * @param string $query the search query
     * @param string|null $provider the name of the provider to use, or null for the default
     *
     * @return GifData the data for the gif
     *
     * @throws GifException if an error occurs retrieving the gif
     */
    public function search($query, $provider = null): GifData
    {
        if ($this->cacheTtl === 0) {
            return $this->getProvider($provider)->search($query);
        }

        $provider = ($provider ?: $this->getDefaultProvider());
        $cacheKey = 'gifs:' . $provider . ':search:' . $query;

        /** @var GifData $gifData */
        $gifData = Cache::get($cacheKey);
        if (!$gifData) {
            $gifData = $this->getProvider($provider)->search($query);
        }

        Cache::put($cacheKey, $gifData, $this->cacheTtl);

        return $gifData;
    }

    /**
     * Returns a random GIF
     *
     * @param string|null $provider the name of the provider to use, or null for the default
     *
     * @return GifData the data for the gif
     *
     * @throws GifException if an error occurs retrieving the gif
     */
    public function random($provider = null): GifData
    {
        return $this->getProvider($provider)->random();
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultProvider()
    {
        return $this->app['config']['gifs.default'];
    }

    /**
     * @param string|null $provider the name of the provider to use, or null for the default
     *
     * @return GifDriverInterface
     */
    protected function getProvider($provider = null): GifDriverInterface
    {
        $provider = ($provider ?: $this->getDefaultProvider());

        if (!array_key_exists($provider, $this->providerMap)) {
            $config = $this->app['config']['gifs.providers'][$provider];
            $driver = $config['driver'];
            $this->providerMap[$provider] = $this->factory->make($driver, $config);
        }

        return $this->providerMap[$provider];
    }
}
