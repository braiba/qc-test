<?php

namespace App\Services\GifService\Drivers;

use App\Services\GifService\Exceptions\GifConnectionException;
use App\Services\GifService\Exceptions\GifNotFoundException;
use App\Services\GifService\GifData;
use App\Services\GifService\GifDriverInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Implementation of GifDriverInterface that uses the Giphy API
 *
 * @package App\Services\GifService\Drivers
 */
class GiphyGifDriver implements GifDriverInterface
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * GiphyGifProvider constructor.
     *
     * @param Client $httpClient
     * @param string $apiKey
     */
    public function __construct(Client $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritDoc
     */
    public function search($query): GifData
    {
        $params = [
            'q' => $query,
            'limit' => 1,
        ];
        $response = $this->makeApiRequest('get', '/v1/gifs/search', $params);

        if (empty($response['data'])) {
            throw new GifNotFoundException('No gifs were returned for the search query \'' . $query . '\'');
        }

        return $this->makeGifDataFromGifObject($response['data'][0]);
    }

    /**
     * @inheritDoc
     */
    public function random(): GifData
    {
        $response = $this->makeApiRequest('get', '/v1/gifs/random');

        return $this->makeGifDataFromGifObject($response['data']);
    }

    /**
     * Makes a request to the Giphy API
     *
     * @param string $method the method
     * @param string $url the endpoint
     * @param array $params additional query params
     *
     * @return array the response as an object decoded from the JSON response
     *
     * @throws GifConnectionException if an error occurs making or parsing the request
     */
    protected function makeApiRequest(string $method, string $url, array $params = []): array
    {
        $params['api_key'] = $this->apiKey;

        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                [
                    RequestOptions::QUERY => $params,
                ]
            );
        } catch (GuzzleException $ex) {
            throw new GifConnectionException('A connection error occurred', $ex->getCode(), $ex);
        }

        if ($response->getStatusCode() !== 200) {
            throw new GifConnectionException('Unexpected response code: ' . $response->getStatusCode());
        }

        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        if ($json === null) {
            throw new GifConnectionException('Invalid Response: ' . $body);
        }

        return $json;
    }

    /**
     * Create a GifData object from the 'GIF Object' returned by the Giphy API
     * @see https://developers.giphy.com/docs/api/schema
     *
     * @param array $gifObject
     *
     * @return GifData
     */
    protected function makeGifDataFromGifObject(array $gifObject)
    {
        $gifData = new GifData();

        $gifOriginal = $gifObject['images']['original'];

        $gifData->setUrl($gifObject['url']);
        $gifData->setTitle($gifObject['title']);
        $gifData->setWidth($gifOriginal['width']);
        $gifData->setHeight($gifOriginal['height']);
        $gifData->setFrameCount($gifOriginal['frames']);
        $gifData->setSize($gifOriginal['size']);

        return $gifData;
    }
}
