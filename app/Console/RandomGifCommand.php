<?php

namespace App\Console;

use App\Services\GifService\GifData;

/**
 * Console command for retrieving a random GIF
 *
 * @package App\Console
 */
class RandomGifCommand extends AbstractGifCommand
{
    /**
     * @inheritDoc
     */
    protected $name = 'gif:random';

    /**
     * @inheritDoc
     */
    protected $description = 'Retrieves a random GIF';

    /**
     * @inheritDoc
     */
    public function getGif(): GifData
    {
        return $this->gifService->random();
    }
}
