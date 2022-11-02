<?php

namespace App\Http\Controllers\Api;

use App\Facades\Elasticsearch;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertiesController extends Controller
{
    /**
     * Return All Properties
     *
     * @param Request $request
     * @return array|string
     */
    public function index(Request $request): array|string
    {
        try {
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
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return $exception->getMessage();
        }
    }

    /**
     * Return All Properties
     *
     * @param Request $request
     * @return array|string
     */
    public function indexGeoJson(Request $request): array|string
    {
        try {
            $parameters = [
                'index' => 'properties',
                'body' => [
                    'from' => ($request->currentPage - 1) * $request->size,
                    'size' => $request->size ?? 10000,
                    'sort' => [ 'id' => ['order' => 'asc' ] ]
                ]
            ];
            $properties =  Elasticsearch::search($parameters);
            $properties =  array_column($properties['hits']['hits'], '_source');

            $features = array();

            foreach($properties as $key => $value) {
                $features[] = array(
                    'type' => 'Feature',
                    'geometry' => array('type' => 'Point', 'coordinates' => array((float)$value['location'][0],(float)$value['location'][1])),
                    'properties' => array('name' => $value['address'], 'id' => $value['id']),
                );
            };

            $allfeatures = array('type' => 'FeatureCollection', 'features' => $features);
            return json_encode($allfeatures, JSON_PRETTY_PRINT);


        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return $exception->getMessage();
        }
    }

    /**
     * Full-text Search
     *
     * @param Request $request
     * @return array|string
     */
    public function search(Request $request): array|string
    {
        try {
            if($request->searchQuery)
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'query' => [
                            "query_string" => [
                                "query" => $request->searchQuery,
                                "fields" => ["description", "address", "city"]
                            ]
                        ]
                    ]
                ];
            else
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'size' => 10
                    ]
                ];
            $properties =  Elasticsearch::search($parameters);
            return array_column($properties['hits']['hits'], '_source');
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return ["We couldn't find any properties for you"];
        }
    }

    /**
     * Full-text Search
     *
     * @param Request $request
     * @return array
     */
    public function autocomplete(Request $request): array
    {
        try {
            if($request->searchQuery && strlen($request->searchQuery))
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'size' => 10,
                        'query' => [
                            "query_string" => [
                                "query" => $request->searchQuery,
                                "fields" => ["description", "address", "city"]
                            ]
                        ]
                    ]
                ];
            else
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'size' => 10
                    ]
                ];
            $results =  Elasticsearch::search($parameters);
            $resultsData = array_column($results['hits']['hits'], '_source');
            return array_column($resultsData, 'address');
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return ["We couldn't find any properties for you"];
        }
    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function drawPolygonSearch(Request $request): array
    {
        try {
            if($request->searchQuery && strlen($request->searchQuery))
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'query' => [
                            "bool" => [
                                "must" => [
                                    "query_string" => [ "query" => $request->searchQuery, "fields" => ["description", "address", "city"]]
                                ],
                                "filter" => [
                                    "geo_polygon" => [
                                        "location" => [
                                            "points" => $request->pointsArray ?? []
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ];
            else
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'query' => [
                            "bool" => [
                                "filter" => [
                                    "geo_polygon" => [
                                        "location" => [
                                            "points" => $request->pointsArray ?? []
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ];
            $results =  Elasticsearch::search($parameters);
            return array_column($results['hits']['hits'], '_source');
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return ["We couldn't find any properties for you"];
        }
    }

    public function schoolDistance(Request $request) {
        try {
            $parameters = [
                'index' => 'public_schools',
                'body' => [
                    'query' => [
                        "bool" => [
                            'must' => [
                                "query_string" => [
                                    "query" =>  "*",
                                    "fields" => ["address"]
                                ]
                            ],
                            "filter" => [
                                "geo_distance" => [
                                    "location" => [
                                        "lat" => 40.735832,
                                        "lon" => -111.9298
                                    ]
                                ]
                            ]
                        ],
                    ]
                ]
            ];
            $results =  Elasticsearch::search($parameters);
            return array_column($results['hits']['hits'], '_source');
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return [];
        }
    }

    public function drawDistanceSearch(Request $request)
    {

    }
}
