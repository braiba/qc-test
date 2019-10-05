<?php

namespace Tests\Unit\Services\GifService;

use App\Services\GifService\Drivers\GiphyGifDriver;
use App\Services\GifService\Exceptions\GifConnectionException;
use App\Services\GifService\Exceptions\GifNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Mockery;
use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

/**
 * Unit tests for GiphyGifDriver
 *
 * @coversDefaultClass \App\Services\GifService\Drivers\GiphyGifDriver
 *
 * @package Tests\Unit\Services\GifService
 */
class GiphyGifDriverTest extends TestCase
{
    const DUMMY_API_KEY = '09ca496a-b03f-47ea-a059-b8a34857b704';
    const DUMMY_SEARCH_QUERY = 'dummy search query';
    const DUMMY_GIF_URL = 'http://dummy.gif.url';
    const DUMMY_GIF_TITLE = 'Dummy GIF Title';
    const DUMMY_GIF_WIDTH = 320;
    const DUMMY_GIF_HEIGHT = 240;
    const DUMMY_GIF_FRAME_COUNT = 100;
    const DUMMY_GIF_SIZE = 1024;

    /**
     * Basic test of the 'search' method
     *
     * @covers ::search
     * @covers ::__construct
     * @covers ::makeApiRequest
     * @covers ::makeGifDataFromGifObject
     */
    public function testSearch()
    {
        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        $giphyGifDriver = new GiphyGifDriver($httpClientMock, self::DUMMY_API_KEY);

        /** @var Mock|ResponseInterface $responseMock */
        $responseMock = Mockery::mock(ResponseInterface::class);

        $expectedRequestParams = [
            'query' => [
                'api_key' => self::DUMMY_API_KEY,
                'q' => self::DUMMY_SEARCH_QUERY,
                'limit' => 1,
            ],
        ];
        $httpClientMock
            ->shouldReceive('request')
            ->with('get', '/v1/gifs/search', $expectedRequestParams)
            ->andReturn($responseMock);

        $responseMock
            ->shouldReceive('getStatusCode')
            ->andReturn(200);

        /** @var Mock|StreamInterface $bodyMock */
        $bodyMock = Mockery::mock(StreamInterface::class);

        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($bodyMock);

        $dummyUrl = self::DUMMY_GIF_URL;
        $dummyTitle = self::DUMMY_GIF_TITLE;
        $dummyWidth = self::DUMMY_GIF_WIDTH;
        $dummyHeight = self::DUMMY_GIF_HEIGHT;
        $dummyFrameCount = self::DUMMY_GIF_FRAME_COUNT;
        $dummySize = self::DUMMY_GIF_SIZE;
        $responseBody = <<<JSON
{
    "data": [
        {
            "url": "{$dummyUrl}",
            "title": "{$dummyTitle}",
            "images": {
                "original": {
                    "width": {$dummyWidth},
                    "height": {$dummyHeight},
                    "frames": {$dummyFrameCount},
                    "size": {$dummySize}
                }
            }
        }
    ]
}
JSON;
        $bodyMock
            ->shouldReceive('getContents')
            ->andReturn($responseBody);

        $actualResult = $giphyGifDriver->search(self::DUMMY_SEARCH_QUERY);

        $this->assertEquals(self::DUMMY_GIF_URL, $actualResult->getUrl());
        $this->assertEquals(self::DUMMY_GIF_TITLE, $actualResult->getTitle());
        $this->assertEquals(self::DUMMY_GIF_WIDTH, $actualResult->getWidth());
        $this->assertEquals(self::DUMMY_GIF_HEIGHT, $actualResult->getHeight());
        $this->assertEquals(self::DUMMY_GIF_FRAME_COUNT, $actualResult->getFrameCount());
        $this->assertEquals(self::DUMMY_GIF_SIZE, $actualResult->getSize());
    }

    /**
     * Test of the 'search' method where the API response contains no entries
     *
     * @covers ::search
     */
    public function testSearchWithNoResults()
    {
        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        $giphyGifDriver = new GiphyGifDriver($httpClientMock, self::DUMMY_API_KEY);

        /** @var Mock|ResponseInterface $responseMock */
        $responseMock = Mockery::mock(ResponseInterface::class);

        $expectedRequestParams = [
            'query' => [
                'api_key' => self::DUMMY_API_KEY,
                'q' => self::DUMMY_SEARCH_QUERY,
                'limit' => 1,
            ],
        ];
        $httpClientMock
            ->shouldReceive('request')
            ->with('get', '/v1/gifs/search', $expectedRequestParams)
            ->andReturn($responseMock);

        $responseMock
            ->shouldReceive('getStatusCode')
            ->andReturn(200);

        /** @var Mock|StreamInterface $bodyMock */
        $bodyMock = Mockery::mock(StreamInterface::class);

        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($bodyMock);

        $bodyMock
            ->shouldReceive('getContents')
            ->andReturn('{"data":[]}');

        $this->expectException(GifNotFoundException::class);

        $giphyGifDriver->search(self::DUMMY_SEARCH_QUERY);
    }

    /**
     * Basic test of the 'random' method
     *
     * @covers ::random
     * @covers ::makeApiRequest
     * @covers ::makeGifDataFromGifObject
     */
    public function testRandom()
    {
        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        $giphyGifDriver = new GiphyGifDriver($httpClientMock, self::DUMMY_API_KEY);

        /** @var Mock|ResponseInterface $responseMock */
        $responseMock = Mockery::mock(ResponseInterface::class);

        $expectedRequestParams = [
            'query' => [
                'api_key' => self::DUMMY_API_KEY,
            ],
        ];
        $httpClientMock
            ->shouldReceive('request')
            ->with('get', '/v1/gifs/random', $expectedRequestParams)
            ->andReturn($responseMock);

        $responseMock
            ->shouldReceive('getStatusCode')
            ->andReturn(200);

        /** @var Mock|StreamInterface $bodyMock */
        $bodyMock = Mockery::mock(StreamInterface::class);

        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($bodyMock);

        $dummyUrl = self::DUMMY_GIF_URL;
        $dummyTitle = self::DUMMY_GIF_TITLE;
        $dummyWidth = self::DUMMY_GIF_WIDTH;
        $dummyHeight = self::DUMMY_GIF_HEIGHT;
        $dummyFrameCount = self::DUMMY_GIF_FRAME_COUNT;
        $dummySize = self::DUMMY_GIF_SIZE;
        $responseBody = <<<JSON
{
    "data": {
        "url": "{$dummyUrl}",
        "title": "{$dummyTitle}",
        "images": {
            "original": {
                "width": {$dummyWidth},
                "height": {$dummyHeight},
                "frames": {$dummyFrameCount},
                "size": {$dummySize}
            }
        }
    }
}
JSON;
        $bodyMock
            ->shouldReceive('getContents')
            ->andReturn($responseBody);

        $actualResult = $giphyGifDriver->random();

        $this->assertEquals(self::DUMMY_GIF_URL, $actualResult->getUrl());
        $this->assertEquals(self::DUMMY_GIF_TITLE, $actualResult->getTitle());
        $this->assertEquals(self::DUMMY_GIF_WIDTH, $actualResult->getWidth());
        $this->assertEquals(self::DUMMY_GIF_HEIGHT, $actualResult->getHeight());
        $this->assertEquals(self::DUMMY_GIF_FRAME_COUNT, $actualResult->getFrameCount());
        $this->assertEquals(self::DUMMY_GIF_SIZE, $actualResult->getSize());
    }

    /**
     * Tests that a GuzzleException on the API request results in a GifConnectionException
     *
     * @covers ::makeApiRequest
     */
    public function testMakeApiRequestWithGuzzleException()
    {
        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        $giphyGifDriver = new GiphyGifDriver($httpClientMock, self::DUMMY_API_KEY);

        /** @var Mock|RequestException $requestExceptionMock */
        $requestExceptionMock = Mockery::mock(RequestException::class);
        $httpClientMock
            ->shouldReceive('request')
            ->andThrow($requestExceptionMock);

        $this->expectException(GifConnectionException::class);

        $giphyGifDriver->random();
    }

    /**
     * Tests that a non-200 response from the API request results in a GifConnectionException
     *
     * @covers ::makeApiRequest
     */
    public function testMakeApiRequestWithNonOkResponse()
    {
        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        $giphyGifDriver = new GiphyGifDriver($httpClientMock, self::DUMMY_API_KEY);

        /** @var Mock|ResponseInterface $responseMock */
        $responseMock = Mockery::mock(ResponseInterface::class);

        $httpClientMock
            ->shouldReceive('request')
            ->andReturn($responseMock);

        $responseMock
            ->shouldReceive('getStatusCode')
            ->andReturn(400);

        $this->expectException(GifConnectionException::class);

        $giphyGifDriver->random();
    }

    /**
     * Tests that a non-JSON response from the API request results in a GifConnectionException
     *
     * @covers ::makeApiRequest
     */
    public function testMakeApiRequestWithNonJsonResponse()
    {
        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        $giphyGifDriver = new GiphyGifDriver($httpClientMock, self::DUMMY_API_KEY);

        /** @var Mock|ResponseInterface $responseMock */
        $responseMock = Mockery::mock(ResponseInterface::class);

        $httpClientMock
            ->shouldReceive('request')
            ->andReturn($responseMock);

        $responseMock
            ->shouldReceive('getStatusCode')
            ->andReturn(200);

        /** @var Mock|StreamInterface $bodyMock */
        $bodyMock = Mockery::mock(StreamInterface::class);

        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($bodyMock);

        $bodyMock
            ->shouldReceive('getContents')
            ->andReturn('dummy non-json response body');

        $this->expectException(GifConnectionException::class);

        $giphyGifDriver->random();
    }
}
