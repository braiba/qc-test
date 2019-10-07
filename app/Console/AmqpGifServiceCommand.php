<?php

namespace App\Console;

use Amqp;
use App\Services\GifService;
use App\Services\GifService\GifData;
use App\Services\GifService\Exceptions\GifException;
use function array_key_exists;
use Bschmitt\Amqp\Consumer;
use DomainException;
use Illuminate\Console\Command;
use InvalidArgumentException;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Command class for running the AMQP GIF service
 *
 * @package App\Console
 */
class AmqpGifServiceCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected $name = 'amqp:gifs:listen';

    /**
     * @inheritDoc
     */
    protected $description = 'The an AMQP service that listens for GIF data requests';

    /**
     * @var GifService
     */
    protected $gifService;

    /**
     * Constructor
     *
     * @param GifService $gifService
     */
    public function __construct(GifService $gifService)
    {
        parent::__construct();

        $this->gifService = $gifService;
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        $queueName = config('gifs.amqp.queue_name');

        Amqp::consume(
            $queueName,
            function (AMQPMessage $message, Consumer $consumer) {
                try {
                    $this->consumeAmqpMessage($message);

                    $consumer->acknowledge($message);
                } catch (\Exception $e) {
                    $this->error($e);

                    $consumer->reject($message);
                }
            },
            [
                'exchange' => config('gifs.amqp.exchange_name'),
                'exchange_type' => config('gifs.amqp.exchange_type'),
                'exchange_durable' => true,
                'queue_durable' => true,
                'persistent' => true,
                'routing' => $queueName,
            ]
        );
    }

    /**
     * Consume an AMQP message from the gifs request queue
     *
     * @param AMQPMessage $message
     *
     * @throws GifException if the GIF data cannot be retrieved
     */
    protected function consumeAmqpMessage(AMQPMessage $message)
    {
        if (!$message->has('reply_to')) {
            throw new InvalidArgumentException('Missing property: reply_to');
        }

        $replyTo = $message->get('reply_to');

        $request = json_decode($message->body, true);
        if (!$request) {
            throw new InvalidArgumentException('Message body was not valid JSON');
        }

        if (!array_key_exists('action', $request)) {
            throw new InvalidArgumentException('Missing key \'action\' in request');
        }

        $action = $request['action'];

        switch ($action) {
            case 'search':
                $gifData = $this->handleSearchRequest($request);
                break;

            case 'random':
                $gifData = $this->handleRandomRequest($request);
                break;

            default:
                throw new DomainException('Unexpected action value: \'' . $action . '\'');
        }

        Amqp::publish(
            $replyTo,
            $this->createMessageFromGifData($gifData),
            [
                'exchange' => config('gifs.amqp.exchange_name'),
                'exchange_type' => config('gifs.amqp.exchange_type'),
                'exchange_durable' => true,
            ]
        );
    }

    /**
     * Handle a 'search' request
     *
     * @param array $request the request data
     *
     * @return GifData the resulting GIF data
     *
     * @throws GifException if the GIF data could not be retrieved
     */
    protected function handleSearchRequest(array $request): GifData
    {
        if (!array_key_exists('query', $request)) {
            throw new InvalidArgumentException('Missing key \'query\' in search request');
        }

        return $this->gifService->search($request['query']);
    }

    /**
     * Handle a 'random' request
     *
     * @param array $request the request data
     *
     * @return GifData the resulting GIF data
     *
     * @throws GifException if the GIF data could not be retrieved
     */
    protected function handleRandomRequest(array $request): GifData
    {
        return $this->gifService->random();
    }

    /**
     * Generates an AMQP message body to represent the GIF data
     *
     * @param GifData $gifData the GIF data
     *
     * @return AMQPMessage the AMQP message
     */
    protected function createMessageFromGifData(GifData $gifData): AMQPMessage
    {
        $body = json_encode([
            'data' => [
                'url' => $gifData->getUrl(),
                'title' => $gifData->getTitle(),
                'width' => $gifData->getWidth(),
                'height' => $gifData->getHeight(),
                'frame_count' => $gifData->getFrameCount(),
                'size' => $gifData->getSize(),
            ],
        ]);

        return new AMQPMessage($body, ['content_type' => 'application/json', 'delivery_mode' => 2]);
    }
}
