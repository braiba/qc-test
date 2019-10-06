<?php

namespace Tests\Unit\Services\GifService;

use App\Services\GifService\Drivers\FakerGifDriver;
use App\Services\GifService\GifData;
use Tests\TestCase;

/**
 * Unit tests for FakerGifDriver
 *
 * @coversDefaultClass \App\Services\GifService\Drivers\FakerGifDriver
 *
 * @package Tests\Unit\Services\GifService
 */
class FakerGifDriverTest extends TestCase
{
    const DUMMY_SEARCH_QUERY = 'dummy search query';

    /**
     * Basic test of the 'search' method
     *
     * @covers ::search
     * @covers ::__construct
     * @covers ::makeFakeGifData
     */
    public function testSearch()
    {
        /** @var FakerGifDriver $fakerGifDriver */
        $fakerGifDriver = app(FakerGifDriver::class);

        $actualResult = $fakerGifDriver->search(self::DUMMY_SEARCH_QUERY);

        $this->assertInstanceOf(GifData::class, $actualResult);
    }

    /**
     * Basic test of the 'random' method
     *
     * @covers ::random
     * @covers ::makeFakeGifData
     */
    public function testRandom()
    {
        /** @var FakerGifDriver $fakerGifDriver */
        $fakerGifDriver = app(FakerGifDriver::class);

        $actualResult = $fakerGifDriver->random();

        $this->assertInstanceOf(GifData::class, $actualResult);
    }
}
