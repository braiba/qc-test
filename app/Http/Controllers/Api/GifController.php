<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GifDataResource;
use App\Services\GifService;
use App\Services\GifService\Exceptions\GifException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\ValidationException;

class GifController extends BaseController
{
    use ValidatesRequests;

    /**
     * @var GifService
     */
    protected $gifService;

    /**
     * GifController constructor.
     *
     * @param GifService $gifService
     */
    public function __construct(GifService $gifService)
    {
        $this->gifService = $gifService;
    }

    /**
     * Endpoint that returns a GIF based on a search query
     *
     * @param Request $request the request object
     *
     * @return JsonResource a JSON response resource containing the GIF's data
     *
     * @throws ValidationException if the request is invalid
     * @throws GifException if the GIF could not be retrieved
     */
    public function search(Request $request)
    {
        $data = $this->validate(
            $request,
            [
                'query' => 'required|string'
            ]
        );

        $gifData = $this->gifService->search($data['query']);

        return new GifDataResource($gifData);
    }

    /**
     * Endpoint that returns a random GIF
     *
     * @return JsonResource a JSON response resource containing the GIF's data
     *
     * @throws GifException if the GIF could not be retrieved
     */
    public function random()
    {
        $gifData = $this->gifService->random();

        return new GifDataResource($gifData);
    }
}
