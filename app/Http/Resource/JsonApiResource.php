<?php

namespace App\Http\Resource;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Swagger\Annotations as SWG;

/**
 * Class JsonApiResource
 * @package App\Http\Resource
 *
 *
 * @SWG\Response(
 *      response="Json",
 *      description="the basic successful response",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="data",
 *              type="object"
 *          ),
 *          @SWG\Property(
 *              property="meta",
 *              type="object",
 *              @SWG\Property(
 *                  property="status",
 *                  type="number",
 *                  example=200
 *              )
 *          ),
 *          @SWG\Property(
 *              property="errors",
 *              type="object"
 *          )
 *      )
 * )
 *
 */
class JsonApiResource extends JsonResource
{
    /**
     * JsonApiResource constructor.
     * @param $resource
     * @param array|int $metaOrKey
     * @param array $errors
     * @param int $statusCode
     */
    public function __construct($resource, $metaOrKey = [], array $errors = [], int $statusCode = 200)
    {
        parent::__construct($resource);

        // Unfortunately laravel sends the collection offset key during hydration of collections
        // as 2nd parameter
        $meta = [];
        if (is_array($metaOrKey)) {
            $meta = array_merge([], $metaOrKey);
        }

        $meta['status'] = $statusCode;
        $this->additional([
            'meta' => $meta,
            'errors' => $errors,
        ]);
    }

    /**
     * @param $resource
     * @param array $meta
     * @param array $errors
     * @return JsonApiResourceCollection
     */
    public static function collection($resource, array $meta = [], array $errors = []): JsonApiResourceCollection
    {
        return new JsonApiResourceCollection($resource, get_called_class(), $meta, $errors);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // provide shortcut
        if ($this->resource === null) {
            return [];
        }

        return parent::toArray($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \InvalidArgumentException
     */
    public function toResponse($request): JsonResponse
    {
        $response = parent::toResponse($request);

        if ($this->additional['meta']['status'] !== 200) {
            $response->setStatusCode($this->additional['meta']['status']);
        }

        return $response;
    }
}
