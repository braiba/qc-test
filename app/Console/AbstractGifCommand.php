<?php

namespace App\Console;

use App\Services\GifService;
use App\Services\GifService\Exceptions\GifException;
use App\Services\GifService\GifData;
use Illuminate\Console\Command;

/**
 * Abstract command class for retrieving a single GIF
 *
 * @package App\Console
 */
abstract class AbstractGifCommand extends Command
{
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
        try {
            $gifData = $this->getGif();
        } catch (GifException $e) {
            $this->error($e);
            return;
        }

        $output = $this->generateJsonOutput($gifData);

        $this->line($output);
    }

    /**
     * Retrieve data about the GIF
     *
     * @return GifData the resulting GIF data
     *
     * @throws GifException if the GIF cannot be retrieved
     */
    protected abstract function getGif(): GifData;

    /**
     * Generates a JSON string that represents the GIF data
     *
     * @param GifData $gifData the GIF data
     *
     * @return string the JSON string
     */
    protected function generateJsonOutput(GifData $gifData)
    {
        return json_encode([
            'data' => [
                'url' => $gifData->getUrl(),
                'title' => $gifData->getTitle(),
                'width' => $gifData->getWidth(),
                'height' => $gifData->getHeight(),
                'frame_count' => $gifData->getFrameCount(),
                'size' => $gifData->getSize(),
            ],
        ]);
    }
}
