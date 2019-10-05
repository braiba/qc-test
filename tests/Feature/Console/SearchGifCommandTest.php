<?php

namespace Tests\Feature\Console;

use App\Factories\GuzzleHttpClientFactory;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Mockery;
use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

class SearchGifCommandTest extends TestCase
{
    const DUMMY_API_KEY = '09ca496a-b03f-47ea-a059-b8a34857b704';
    const DUMMY_BASE_URL = 'http://dummy.base.url';
    const DUMMY_GIF_PROVIDER = 'dummy-gif-provider';
    const DUMMY_SEARCH_QUERY = 'dummy search query';
    const DUMMY_GIF_URL = 'http://dummy.gif.url';
    const DUMMY_GIF_URL_JSON = 'http:\\/\\/dummy.gif.url';
    const DUMMY_GIF_TITLE = 'Dummy GIF Title';
    const DUMMY_GIF_WIDTH = 320;
    const DUMMY_GIF_HEIGHT = 240;
    const DUMMY_GIF_FRAME_COUNT = 100;
    const DUMMY_GIF_SIZE = 1024;

    /**
     * Test for the 'gif:search' console command
     */
    public function testSearchGifCommand()
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

        $dummyUrlJson = self::DUMMY_GIF_URL_JSON;
        $expectedOutput = <<<JSON
{"data":{"url":"{$dummyUrlJson}","title":"{$dummyTitle}","width":{$dummyWidth},"height":{$dummyHeight},"frame_count":{$dummyFrameCount},"size":{$dummySize}}}
JSON;

        $this->artisan('gif:search "' . self::DUMMY_SEARCH_QUERY . '"')
            ->expectsOutput($expectedOutput)
            ->assertExitCode(0);
    }
}
