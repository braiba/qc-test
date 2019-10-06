<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for GifData objects
 *
 * @method string getUrl()
 * @method string getTitle()
 * @method int getWidth()
 * @method int getHeight()
 * @method int getFrameCount()
 * @method int getSize()
 *
 * @package App\Http\Resources
 */
class GifDataResource extends JsonResource
{
    /**
     * @inheritDoc
     */
    public function toArray($request)
    {
        return [
            'url' => $this->getUrl(),
            'title' => $this->getTitle(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'frame_count' => $this->getFrameCount(),
            'size' => $this->getSize(),
        ];
    }
}
