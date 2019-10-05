<?php

namespace App\Console;

use App\Services\GifService\GifData;

/**
 * Console command for searching for a GIF
 *
 * @package App\Console
 */
class SearchGifCommand extends AbstractGifCommand
{
    /**
     * @inheritDoc
     */
    protected $signature = 'gif:search {query}';

    /**
     * @inheritDoc
     */
    protected $description = 'Retrieves a random GIF';

    /**
     * @inheritDoc
     */
    public function getGif(): GifData
    {
        return $this->gifService->search($this->argument('query'));
    }
}
