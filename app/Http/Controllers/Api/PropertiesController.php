<?php

namespace App\Http\Controllers\Api;

use App\Facades\Elasticsearch;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

const COUNTIES_INDEX = 'counties_secondary';
const PROPERTIES_INDEX = 'properties';
const SCHOOLS_INDEX = 'public_schools';
const AUTOCOMPLETE_SIZE = 5;
const SEARCH_SIZE = 100;

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
                'index' => PROPERTIES_INDEX,
                'body' => [
                    'from' => ($request->currentPage - 1) * $request->size,
                    'size' => $request->size ?? 1000,
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
     * @param Request $request
     * @return Application|Factory|View|string
     */
    public function propertiesAmenities(Request $request): View|Factory|string|Application
    {
        try {
            $parameters = [
                'index' => 'properties',
                'body' => [
                    'query' => [
                        "bool" => [
                            "must" => [
                                [
                                    "query_string" => [
                                        "query" => $request->details,
                                        "fields" => ["description", "address", "city"]
                                    ]
                                ],
                                [
                                    "range" => [
                                        "bath" => [
                                            "gte" => $request->noBaths ?? 1,
                                        ]
                                    ]
                                ],
                                [
                                    "range" => [
                                        "bed" => [
                                            "gte" => $request->noBeds ?? 1,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "size" => 100
                ]
            ];
            $results =  Elasticsearch::search($parameters);
            $properties = array_column($results['hits']['hits'], '_source');
            if (isset($request->schoolProximity) && $request->schoolProximity != 'no') {
               foreach ($properties as $key => $property) {
                   $parameters = [
                       'index' => 'public_schools',
                       'body' => [
                           'query' => [
                               "bool" => [
                                   "must" => [
                                       "match_all" => new \stdClass()
                                   ],
                                   "filter" => [
                                       "geo_distance" => [
                                           "distance" => $request->schoolProximity,
                                           "geometry" => [
                                               "lat" => $property['location'][1],
                                               "lon" => $property['location'][0]
                                           ]
                                       ]
                                   ]
                               ]
                           ]
                       ]
                   ];
                   $schools =  Elasticsearch::search($parameters);
                   if(!$schools['hits']['total']['value']) {
                       unset($properties[$key]);
                   }
               }
            }
            $heading = 'Properties with at least ' . $request->noBeds . ' bedrooms and ' . $request->noBaths . ' bathrooms';
            return view('search', compact('request', 'properties', 'heading'));
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
                        'size' => SEARCH_SIZE
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
            if($request->searchQuery)
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'query' => [
                            "query_string" => [
                                "query" => $request->searchQuery,
                                "fields" => ["description", "address", "city"]
                            ]
                        ],
                        'size' => AUTOCOMPLETE_SIZE
                    ]
                ];
            else
                $parameters = [
                    'index' => 'properties',
                    'body' => [
                        'size' => AUTOCOMPLETE_SIZE
                    ]
                ];
            $properties =  Elasticsearch::search($parameters);
            $properties = array_column($properties['hits']['hits'], '_source');
             if(!$properties || count($properties) < AUTOCOMPLETE_SIZE) {
                $parameters = [
                    'index' => 'counties_secondary',
                    'body' => [
                        'query' => [
                            "query_string" => [
                                "query" => $request->searchQuery ?? "*",
                                "fields" => ["CountyName", 'AdminRegion']
                            ]
                        ],
                        "size" => AUTOCOMPLETE_SIZE,
                        "_source" => ['CountyName', 'AdminRegion']
                    ]
                ];
                $counties =  Elasticsearch::search($parameters);
                $counties = array_column($counties['hits']['hits'], '_id');
                foreach ($counties as $county) {
                    $size = max((AUTOCOMPLETE_SIZE - count($properties)), 0);
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
                                                    'index' => 'counties_secondary',
                                                    'id' => $county,
                                                    'path' => 'geometry'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            "size" => $size
                        ]
                    ];
                    $propertiesCounty =  Elasticsearch::search($parameters);
                    $propertiesCounty = array_column($propertiesCounty['hits']['hits'], '_source');
                    $properties = array_merge($properties, $propertiesCounty);
                }
            }
            return array_column($properties, 'address');
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
                        ],
                        "size" => 1000
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
                                        "location" => [ "points" => $request->pointsArray ?? [] ]
                                    ]
                                ]
                            ],
                        ],
                        "size" => 1000
                    ]
                ];
            $results =  Elasticsearch::search($parameters);
            return array_column($results['hits']['hits'], '_source');
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return ["We couldn't find any properties for you"];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function schoolDistance(Request $request) {
        try {
            $parameters = [
                'index' => 'public_schools',
                'body' => [
                    'query' => [
                        "bool" => [
                            "must" => [
                                "match_all" => new \stdClass()
                            ],
                            "filter" => [
                                "geo_distance" => [
                                    "distance" => "1km",
                                    "geometry" => [
                                        "lat" => 40.735832,
                                        "lon" => -111.9298
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $results =  Elasticsearch::search($parameters);
            return $results;
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return [$exception->getMessage()];
        }
    }

    /**
     * @param Request $request
     * @return mixed|string[]
     */
    public function get(Request $request): mixed
    {
        try {
           $parameters = [
                'index' => 'properties',
                'body' => [
                    'query' => [
                        "match" => [
                            "id" => $request->id
                        ]
                    ]
                ]
            ];
            $results =  Elasticsearch::search($parameters);
            $response = [];
            $response['property'] = array_column($results['hits']['hits'], '_source')[0];
            $parameters = [
                'index' => 'public_schools',
                'body' => [
                    'query' => [
                        "bool" => [
                            "must" => [
                                "match_all" => new \stdClass()
                            ],
                            "filter" => [
                                "geo_distance" => [
                                    "distance" => "1.5km",
                                    "geometry" => [
                                        "lat" => $response['property']['location'][1],
                                        "lon" => $response['property']['location'][0]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $results =  Elasticsearch::search($parameters);
            $response['schools'] = array_column($results['hits']['hits'], '_source');

            return $response;
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return [$exception->getMessage()];
        }
    }

    /**
     * @param Request $request
     * @return View|Factory|array|Application
     */
    public function searchLanding(Request $request): View|Factory|array|Application
    {
        try {
            $properties = $this->search($request);
            $heading = 'Search results for... ' . $request->searchQuery;
            $parameters = [
                'index' => 'counties_secondary',
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
            $counties =  Elasticsearch::search($parameters);
            $counties = array_column($counties['hits']['hits'], '_id');
            foreach ($counties as $county) {
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
                                                'index' => 'counties_secondary',
                                                'id' => $county,
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
                $propertiesCounty =  Elasticsearch::search($parameters);
                $propertiesCounty = array_column($propertiesCounty['hits']['hits'], '_source');
                $properties = array_merge($properties, $propertiesCounty);
            }
            return view('search', compact('request', 'properties', 'heading'));
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
            return [$exception->getMessage()];
        }
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function closeProperties(Request $request): mixed
    {
        try {
            $ipAddress = request()->ip() == '127.0.0.1' ? trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com")) : request()->getIp();
            $geo_ip = geoip()->getLocation($ipAddress);
            $parameters = [
                'index' => 'properties',
                'body' => [
                    "size" => 4,
                    "sort" => [
                        [
                            "_geo_distance" => [
                                "location" => [
                                    "lat" => $geo_ip->lat,
                                    "lon" => $geo_ip->lon
                                ],
                                "order" => "asc",
                                "unit" => "km",
                                "mode" => "min",
                                "distance_type" => "arc",
                                "ignore_unmapped" => "true"
                            ]
                        ]
                    ]
                ]
            ];
            $properties =  Elasticsearch::search($parameters);

            return $properties['hits']['hits'];
        } catch (Exception $exception) {
            Log::channel('error.log')->info($exception->getMessage());
           return [$exception->getMessage()];
        }
    }
}
