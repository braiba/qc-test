<?php

namespace Tests\Unit\Services;

use App\Services\GifService;
use App\Services\GifService\Drivers\GiphyGifDriver;
use App\Services\GifService\GifData;
use App\Services\GifService\GifProviderFactory;
use Illuminate\Support\Facades\Config;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Unit tests for GifService
 *
 * @coversDefaultClass \App\Services\GifService
 *
 * @package Tests\Unit\Services
 */
class GifServiceTest extends TestCase
{
    const DUMMY_SEARCH_QUERY = 'dummy-search-query';
    const DUMMY_GIF_PROVIDER = 'dummy-gif-provider';
    const DUMMY_GIF_DRIVER = 'dummy-gif-driver';

    /**
     * Basic test of the 'search' method
     *
     * @covers ::search
     * @covers ::__construct
     *
     * @uses \App\Services\GifService\GifData
     */
    public function testSearch()
    {
        /** @var Mock|GifProviderFactory $gifProviderFactoryMock */
        $gifProviderFactoryMock = Mockery::mock(GifProviderFactory::class);

        $gifService = new GifService($this->app, $gifProviderFactoryMock);

        $dummyProviderConfig = [
            'driver' => self::DUMMY_GIF_DRIVER,
        ];
        Config::set('gifs.providers.' . self::DUMMY_GIF_PROVIDER, $dummyProviderConfig);

        /** @var Mock|GiphyGifDriver $gifDriverMock */
        $gifDriverMock = Mockery::mock(GiphyGifDriver::class);

        $gifProviderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(self::DUMMY_GIF_DRIVER, $dummyProviderConfig)
            ->andReturn($gifDriverMock);

        $dummyGifData = new GifData();

        $gifDriverMock
            ->shouldReceive('search')
            ->once()
            ->with(self::DUMMY_SEARCH_QUERY)
            ->andReturn($dummyGifData);

        $actualResult = $gifService->search(self::DUMMY_SEARCH_QUERY, self::DUMMY_GIF_PROVIDER);

        $this->assertSame($dummyGifData, $actualResult);
    }

    /**
     * Basic test of the 'random' method
     *
     * @covers ::random
     */
    public function testRandom()
    {
        /** @var Mock|GifProviderFactory $gifProviderFactoryMock */
        $gifProviderFactoryMock = Mockery::mock(GifProviderFactory::class);

        $gifService = new GifService($this->app, $gifProviderFactoryMock);

        $dummyProviderConfig = [
            'driver' => self::DUMMY_GIF_DRIVER,
        ];
        Config::set('gifs.providers.' . self::DUMMY_GIF_PROVIDER, $dummyProviderConfig);

        /** @var Mock|GiphyGifDriver $gifDriverMock */
        $gifDriverMock = Mockery::mock(GiphyGifDriver::class);

        $gifProviderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(self::DUMMY_GIF_DRIVER, $dummyProviderConfig)
            ->andReturn($gifDriverMock);

        $dummyGifData = new GifData();

        $gifDriverMock
            ->shouldReceive('random')
            ->once()
            ->andReturn($dummyGifData);

        $actualResult = $gifService->random(self::DUMMY_GIF_PROVIDER);

        $this->assertSame($dummyGifData, $actualResult);
    }

    /**
     * Tests that if we make two calls with the same provider, the factory is only asked to generate the provider once
     *
     * @covers ::getDefaultProvider
     * @covers ::getProvider
     *
     * @uses \App\Services\GifService\GifData
     */
    public function testGetProviderUsesCache()
    {
        /** @var Mock|GifProviderFactory $gifProviderFactoryMock */
        $gifProviderFactoryMock = Mockery::mock(GifProviderFactory::class);

        $gifService = new GifService($this->app, $gifProviderFactoryMock);

        Config::set('gifs.default', self::DUMMY_GIF_PROVIDER);
        $dummyProviderConfig = [
            'driver' => self::DUMMY_GIF_DRIVER,
        ];
        Config::set('gifs.providers.' . self::DUMMY_GIF_PROVIDER, $dummyProviderConfig);

        /** @var Mock|GiphyGifDriver $gifDriverMock */
        $gifDriverMock = Mockery::mock(GiphyGifDriver::class);

        $gifProviderFactoryMock
            ->shouldReceive('make')
            ->once()
            ->with(self::DUMMY_GIF_DRIVER, $dummyProviderConfig)
            ->andReturn($gifDriverMock);

        $dummyGifData = new GifData();

        $gifDriverMock
            ->shouldReceive('random')
            ->twice()
            ->andReturn($dummyGifData);

        $gifService->random();
        $gifService->random();
    }
}
