<?php

namespace Tests\Feature\Http;

use App\Factories\GuzzleHttpClientFactory;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Mockery;
use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

/**
 * Feature tests for the GIF API ontroller
 *
 * @package Tests\Feature\Http
 */
class GifControllerTest extends TestCase
{
    const DUMMY_API_KEY = '09ca496a-b03f-47ea-a059-b8a34857b704';
    const DUMMY_BASE_URL = 'http://dummy.base.url';
    const DUMMY_GIF_PROVIDER = 'dummy-gif-provider';
    const DUMMY_SEARCH_QUERY = 'dummy search query';
    const DUMMY_GIF_URL = 'http://dummy.gif.url';
    const DUMMY_GIF_TITLE = 'Dummy GIF Title';
    const DUMMY_GIF_WIDTH = 320;
    const DUMMY_GIF_HEIGHT = 240;
    const DUMMY_GIF_FRAME_COUNT = 100;
    const DUMMY_GIF_SIZE = 1024;

    /**
     * Test for the '/api/gif/search' GET endpoint
     */
    public function testSearchEndpoint()
    {
        Config::set('gifs.default', self::DUMMY_GIF_PROVIDER);
        $dummyProviderConfig = [
            'driver' => 'giphy',
            'base_url' => self::DUMMY_BASE_URL,
            'api_key' => self::DUMMY_API_KEY,
        ];
        Config::set('gifs.providers.' . self::DUMMY_GIF_PROVIDER, $dummyProviderConfig);

        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        /** @var Mock|GuzzleHttpClientFactory $httpClientFactoryMock */
        $httpClientFactoryMock = Mockery::mock(GuzzleHttpClientFactory::class);

        $this->app->instance(GuzzleHttpClientFactory::class, $httpClientFactoryMock);

        $httpClientFactoryMock
            ->shouldReceive('create')
            ->andReturn($httpClientMock);

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

        $response = $this->json('GET', '/api/gif/search', ['query' => self::DUMMY_SEARCH_QUERY]);

        $this->assertEquals(200, $response->getStatusCode());

        $expectedResult = [
            'data' => [
                'url' => self::DUMMY_GIF_URL,
                'title' => self::DUMMY_GIF_TITLE,
                'width' => self::DUMMY_GIF_WIDTH,
                'height' => self::DUMMY_GIF_HEIGHT,
                'frame_count' => self::DUMMY_GIF_FRAME_COUNT,
                'size' => self::DUMMY_GIF_SIZE,
            ],
        ];
        $actualResult = json_decode($response->getContent(), true);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test for the '/api/gif/random' GET endpoint
     */
    public function testRandomEndpoint()
    {
        Config::set('gifs.default', self::DUMMY_GIF_PROVIDER);
        $dummyProviderConfig = [
            'driver' => 'giphy',
            'base_url' => self::DUMMY_BASE_URL,
            'api_key' => self::DUMMY_API_KEY,
        ];
        Config::set('gifs.providers.' . self::DUMMY_GIF_PROVIDER, $dummyProviderConfig);

        /** @var Mock|Client $httpClientMock */
        $httpClientMock = Mockery::mock(Client::class);

        /** @var Mock|GuzzleHttpClientFactory $httpClientFactoryMock */
        $httpClientFactoryMock = Mockery::mock(GuzzleHttpClientFactory::class);

        $this->app->instance(GuzzleHttpClientFactory::class, $httpClientFactoryMock);

        $httpClientFactoryMock
            ->shouldReceive('create')
            ->andReturn($httpClientMock);

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

        $response = $this->json('GET', '/api/gif/random');

        $this->assertEquals(200, $response->getStatusCode());

        $expectedResult = [
            'data' => [
                'url' => self::DUMMY_GIF_URL,
                'title' => self::DUMMY_GIF_TITLE,
                'width' => self::DUMMY_GIF_WIDTH,
                'height' => self::DUMMY_GIF_HEIGHT,
                'frame_count' => self::DUMMY_GIF_FRAME_COUNT,
                'size' => self::DUMMY_GIF_SIZE,
            ],
        ];
        $actualResult = json_decode($response->getContent(), true);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
