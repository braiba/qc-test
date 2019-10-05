<?php

namespace App\Services\GifService;

use App\Services\GifService\Exceptions\GifException;

/**
 * Interface for drivers used by GifService
 *
 * @package App\Services\GifService
 */
interface GifDriverInterface
{
    /**
     * Returns a single GIF for a search query
     *
     * @param string $query the search query
     *
     * @return GifData the data for the gif
     *
     * @throws GifException if the GIF cannot be returned
     */
    public function search($query): GifData;

    /**
     * Returns a random GIF
     *
     * @return GifData the data for the gif
     *
     * @throws GifException if the GIF cannot be returned
     */
    public function random(): GifData;
}
