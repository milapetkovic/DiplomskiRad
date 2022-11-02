<?php

namespace App\Http\Controllers\Api;

use App\Facades\Elasticsearch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

const COUNTIES_INDEX = 'counties_secondary';

class CountiesController extends Controller
{

    /**
     * Get counties by autocomplete typing
     *
     * @param Request $request
     * @return array|string
     */
    public function searchByName(Request $request): array|string
    {
        try {
            $parameters = [
                'index' => COUNTIES_INDEX,
                'body' => [
                    'query' => [
                        "query_string" => [
                            "query" => $request->searchQuery ?? "*",
                            "fields" => ["CountyName", 'AdminRegion']
                        ]
                    ],
                    "size" => 8,
                    "_source" => ['CountyName', 'AdminRegion']
                ]
            ];
            $properties =  Elasticsearch::search($parameters);
            return array_column($properties['hits']['hits'], '_source', '_id');
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return $exception->getMessage();
        }
    }

    public function propertiesByCounty(Request $request)
    {
        try {
            $parameters = [
                'index' => 'properties',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                "query_string" => [
                                    "query" =>  "*",
                                    "fields" => ["address"]
                                ]
                            ],
                            'filter' => [
                                'geo_shape' => [
                                    'location' => [
                                        'relation' => 'WITHIN',
                                        'indexed_shape' => [
                                            'index' => COUNTIES_INDEX,
                                            'id' => $request->selectedId,
                                            'path' => 'geometry'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "size" => "10"
                ]
            ];
            $properties =  Elasticsearch::search($parameters);
            $properties = array_column($properties['hits']['hits'], '_source');
            $heading = 'Properties in County: ' . $request->selectedValue;
            return view('search', compact('request', 'properties', 'heading'));
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return $exception->getMessage();
        }
    }

}
