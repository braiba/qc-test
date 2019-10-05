<?php

namespace Tests\Unit\Services\GifService;

use App\Services\GifService\GifData;
use Tests\TestCase;

/**
 * Unit tests for GifData
 *
 * @coversDefaultClass \App\Services\GifService\GifData
 *
 * @package Tests\Unit\Services
 */
class GifDataTest extends TestCase
{
    const DUMMY_URL = 'http://dummy.gif.url';
    const DUMMY_TITLE = 'Dummy GIF Title';
    const DUMMY_WIDTH = 320;
    const DUMMY_HEIGHT = 240;
    const DUMMY_FRAME_COUNT = 100;
    const DUMMY_SIZE = 1024;

    /**
     * Test the getters and setters
     *
     * @covers \App\Services\GifService\GifData
     */
    public function testGettersAndSetters()
    {
        $gifData = new GifData();

        $gifData->setUrl(self::DUMMY_URL);
        $this->assertEquals(self::DUMMY_URL, $gifData->getUrl());

        $gifData->setTitle(self::DUMMY_TITLE);
        $this->assertEquals(self::DUMMY_TITLE, $gifData->getTitle());

        $gifData->setWidth(self::DUMMY_WIDTH);
        $this->assertEquals(self::DUMMY_WIDTH, $gifData->getWidth());

        $gifData->setHeight(self::DUMMY_HEIGHT);
        $this->assertEquals(self::DUMMY_HEIGHT, $gifData->getHeight());

        $gifData->setFrameCount(self::DUMMY_FRAME_COUNT);
        $this->assertEquals(self::DUMMY_FRAME_COUNT, $gifData->getFrameCount());

        $gifData->setSize(self::DUMMY_SIZE);
        $this->assertEquals(self::DUMMY_SIZE, $gifData->getSize());
    }
}
