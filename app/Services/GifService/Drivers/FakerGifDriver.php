<?php

namespace App\Services\GifService\Drivers;

use App\Services\GifService\GifData;
use App\Services\GifService\GifDriverInterface;
use Faker\Generator as Faker;

/**
 * Implementation of GifDriverInterface that uses Faker to generates dummy data
 *
 * @package App\Services\GifService\Drivers
 */
class FakerGifDriver implements GifDriverInterface
{
    /**
     * @var Faker
     */
    protected $faker;

    /**
     * FakerGifDriver constructor.
     *
     * @param Faker $faker
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * @inheritDoc
     */
    public function search($query): GifData
    {
        return $this->makeFakeGifData();
    }

    /**
     * @inheritDoc
     */
    public function random(): GifData
    {
        return $this->makeFakeGifData();
    }

    /**
     * Create a GifData object using data generated by Faker
     *
     * @return GifData
     */
    protected function makeFakeGifData()
    {
        $gifData = new GifData();

        $gifData->setUrl($this->faker->url);
        $gifData->setTitle($this->faker->words(3, true));
        $gifData->setWidth($this->faker->numberBetween(64, 128));
        $gifData->setHeight($this->faker->numberBetween(64, 128));
        $gifData->setFrameCount($this->faker->numberBetween(12, 120));
        $gifData->setSize($this->faker->numberBetween(1024, 10240));

        return $gifData;
    }
}