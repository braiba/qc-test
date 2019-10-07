<?php

namespace Tests\Feature\Console;

use App\Factories\GuzzleHttpClientFactory;
use Bschmitt\Amqp\Consumer;
use Bschmitt\Amqp\Publisher;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Config;
use Mockery;
use Mockery\Mock;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

class AmqpGifServiceCommandTest extends TestCase
{
    const DUMMY_API_KEY = '09ca496a-b03f-47ea-a059-b8a34857b704';
    const DUMMY_BASE_URL = 'http://dummy.base.url';
    const DUMMY_GIF_PROVIDER = 'dummy-gif-provider';
    const DUMMY_GIF_URL = 'http://dummy.gif.url';
    const DUMMY_GIF_URL_JSON = 'http:\\/\\/dummy.gif.url';
    const DUMMY_GIF_TITLE = 'Dummy GIF Title';
    const DUMMY_GIF_WIDTH = 320;
    const DUMMY_GIF_HEIGHT = 240;
    const DUMMY_GIF_FRAME_COUNT = 100;
    const DUMMY_GIF_SIZE = 1024;
    const DUMMY_REPLY_TO_QUEUE = 'dummy-reply-to-queue';
    const DUMMY_SEARCH_QUERY = 'dummy search query';

    /**
     * Test for the 'amqp:gifs:listen' console command with a 'random gif' AMQP message
     */
    public function testAmqpGifServiceCommandWithRandomGifRequest()
    {
        /*
         * Mock AMQP Consumer
         */
        /** @var Mock|Consumer $consumerMock */
        $consumerMock = Mockery::mock(Consumer::class, [new Repository()])->makePartial();
        $this->instance(Consumer::class, $consumerMock);

        $consumerMock
            ->shouldReceive('setup');

        $amqpMessage = new AMQPMessage('{"action":"random"}', ['reply_to' => self::DUMMY_REPLY_TO_QUEUE]);

        $consumerMock
            ->shouldReceive('consume')
            ->andReturnUsing(function ($queue, $callback) use ($amqpMessage, $consumerMock) {
                $callback($amqpMessage, $consumerMock);
            });

        /** @var Mock|AMQPChannel $consumerAmqpChannelMock */
        $consumerAmqpChannelMock = Mockery::mock(AMQPChannel::class);

        $consumerMock
            ->shouldReceive('getChannel')
            ->andReturn($consumerAmqpChannelMock);

        $consumerAmqpChannelMock
            ->shouldReceive('close');

        /** @var Mock|AMQPStreamConnection $consumerAmqpConnectionMock */
        $consumerAmqpConnectionMock = Mockery::mock(AMQPStreamConnection::class);

        $consumerMock
            ->shouldReceive('getConnection')
            ->andReturn($consumerAmqpConnectionMock);

        $consumerAmqpConnectionMock
            ->shouldReceive('close');

        $consumerMock
            ->shouldReceive('acknowledge');

        $consumerMock
            ->shouldNotReceive('reject');

        /*
         * Mock HTTP Client
         */
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

        /*
         * Mock AMQP Publisher
         */
        /** @var Mock|Publisher $publisherMock */
        $publisherMock = Mockery::mock(Publisher::class, [new Repository()])->makePartial();
        $this->instance(Publisher::class, $publisherMock);

        $publisherMock
            ->shouldReceive('setup');

        $dummyUrlJson = self::DUMMY_GIF_URL_JSON;
        $expectedMessageBody = <<<JSON
{"data":{"url":"{$dummyUrlJson}","title":"{$dummyTitle}","width":{$dummyWidth},"height":{$dummyHeight},"frame_count":{$dummyFrameCount},"size":{$dummySize}}}
JSON;

        $publisherMock
            ->shouldReceive('publish')
            ->withArgs(function ($routing, $message) use ($expectedMessageBody) {
                if ($routing !== self::DUMMY_REPLY_TO_QUEUE) {
                    return false;
                }

                if (!$message instanceof AMQPMessage) {
                    return false;
                }

                return ($message->getBody() === $expectedMessageBody);
            });

        /** @var Mock|AMQPChannel $publisherAmqpChannelMock */
        $publisherAmqpChannelMock = Mockery::mock(AMQPChannel::class);

        $publisherMock
            ->shouldReceive('getChannel')
            ->andReturn($publisherAmqpChannelMock);

        $publisherAmqpChannelMock
            ->shouldReceive('close');

        /** @var Mock|AMQPStreamConnection $publisherAmqpConnectionMock */
        $publisherAmqpConnectionMock = Mockery::mock(AMQPStreamConnection::class);

        $publisherMock
            ->shouldReceive('getConnection')
            ->andReturn($publisherAmqpConnectionMock);

        $publisherAmqpConnectionMock
            ->shouldReceive('close');

        $this->artisan('amqp:gifs:listen')
            ->assertExitCode(0);
    }

    /**
     * Test for the 'amqp:gifs:listen' console command with a 'search gif' AMQP message
     */
    public function testAmqpGifServiceCommandWithSearchGifRequest()
    {
        /*
         * Mock AMQP Consumer
         */
        /** @var Mock|Consumer $consumerMock */
        $consumerMock = Mockery::mock(Consumer::class, [new Repository()])->makePartial();
        $this->instance(Consumer::class, $consumerMock);

        $consumerMock
            ->shouldReceive('setup');

        $amqpMessage = new AMQPMessage(
            '{"action":"search","query":"' . self::DUMMY_SEARCH_QUERY . '"}',
            ['reply_to' => self::DUMMY_REPLY_TO_QUEUE]#
        );

        $consumerMock
            ->shouldReceive('consume')
            ->andReturnUsing(function ($queue, $callback) use ($amqpMessage, $consumerMock) {
                $callback($amqpMessage, $consumerMock);
            });

        /** @var Mock|AMQPChannel $consumerAmqpChannelMock */
        $consumerAmqpChannelMock = Mockery::mock(AMQPChannel::class);

        $consumerMock
            ->shouldReceive('getChannel')
            ->andReturn($consumerAmqpChannelMock);

        $consumerAmqpChannelMock
            ->shouldReceive('close');

        /** @var Mock|AMQPStreamConnection $consumerAmqpConnectionMock */
        $consumerAmqpConnectionMock = Mockery::mock(AMQPStreamConnection::class);

        $consumerMock
            ->shouldReceive('getConnection')
            ->andReturn($consumerAmqpConnectionMock);

        $consumerAmqpConnectionMock
            ->shouldReceive('close');

        $consumerMock
            ->shouldReceive('acknowledge');

        $consumerMock
            ->shouldNotReceive('reject');

        /*
         * Mock HTTP Client
         */
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

        /*
         * Mock AMQP Publisher
         */
        /** @var Mock|Publisher $publisherMock */
        $publisherMock = Mockery::mock(Publisher::class, [new Repository()])->makePartial();
        $this->instance(Publisher::class, $publisherMock);

        $publisherMock
            ->shouldReceive('setup');

        $dummyUrlJson = self::DUMMY_GIF_URL_JSON;
        $expectedMessageBody = <<<JSON
{"data":{"url":"{$dummyUrlJson}","title":"{$dummyTitle}","width":{$dummyWidth},"height":{$dummyHeight},"frame_count":{$dummyFrameCount},"size":{$dummySize}}}
JSON;

        $publisherMock
            ->shouldReceive('publish')
            ->withArgs(function ($routing, $message) use ($expectedMessageBody) {
                if ($routing !== self::DUMMY_REPLY_TO_QUEUE) {
                    return false;
                }

                if (!$message instanceof AMQPMessage) {
                    return false;
                }

                return ($message->getBody() === $expectedMessageBody);
            });

        /** @var Mock|AMQPChannel $publisherAmqpChannelMock */
        $publisherAmqpChannelMock = Mockery::mock(AMQPChannel::class);

        $publisherMock
            ->shouldReceive('getChannel')
            ->andReturn($publisherAmqpChannelMock);

        $publisherAmqpChannelMock
            ->shouldReceive('close');

        /** @var Mock|AMQPStreamConnection $publisherAmqpConnectionMock */
        $publisherAmqpConnectionMock = Mockery::mock(AMQPStreamConnection::class);

        $publisherMock
            ->shouldReceive('getConnection')
            ->andReturn($publisherAmqpConnectionMock);

        $publisherAmqpConnectionMock
            ->shouldReceive('close');

        $this->artisan('amqp:gifs:listen')
            ->assertExitCode(0);
    }
}
