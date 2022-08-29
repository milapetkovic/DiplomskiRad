<?php

namespace App\Http\Controllers\Api;

use App\Facades\Elasticsearch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertiesController extends Controller
{
    /**
     * @param Request $request
     * @return array|string
     */
    public function index(Request $request): array|string
    {
        try {
            $properties = [];
            $parameters = [
                'index' => 'properties',
                'body' => [
                    'from' => ($request->currentPage - 1) * $request->size,
                    'size' => $request->size ?? 12,
                    'sort' => [ 'id' => ['order' => 'asc' ] ]
                ]
            ];
            $properties =  Elasticsearch::search($parameters);
            return array_column($properties['hits']['hits'], '_source');
        } catch (\Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return $exception->getMessage();
        }
    }

    public function create()
    {
    }
}
