<?php

namespace Tests\Unit\Services\GifService;

use App\Services\GifService\Drivers\GiphyGifDriver;
use App\Services\GifService\GifProviderFactory;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Unit tests for GifProviderFactory
 *
 * @coversDefaultClass \App\Services\GifService\GifProviderFactory
 *
 * @package Tests\Unit\Services\GifService
 */
class GifProviderFactoryTest extends TestCase
{
    const DUMMY_BASE_URI = 'http://dummybase.uri';
    const DUMMY_API_KEY = '09ca496a-b03f-47ea-a059-b8a34857b704';

    /**
     * Test of the 'make' method with the giphy driver
     *
     * @covers ::make
     */
    public function testMakeGiphy()
    {
        $gifProviderFactory = new GifProviderFactory();

        $config = [
            'base_url' => self::DUMMY_BASE_URI,
            'api_key' => self::DUMMY_API_KEY,
        ];
        $actualResult = $gifProviderFactory->make('giphy', $config);

        $this->assertInstanceOf(GiphyGifDriver::class, $actualResult);
    }

    /**
     * Test of the 'make' method with an unknown driver
     *
     * @covers ::make
     */
    public function testMakeUnknownDriver()
    {
        $gifProviderFactory = new GifProviderFactory();

        $this->expectException(InvalidArgumentException::class);

        $gifProviderFactory->make('unknown-driver', []);
    }
}
