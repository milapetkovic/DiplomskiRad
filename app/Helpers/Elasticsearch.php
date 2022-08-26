<?php

namespace App\Helpers;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Http\Promise\Promise;
use Illuminate\Support\Facades\Log;

class Elasticsearch
{
    private $clientBuilder;
    private $client;

    /**
     *
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->clientBuilder = ClientBuilder::create();
        $this->clientBuilder->setHosts(["127.0.0.1:9200"]);
        $this->client = $this->clientBuilder->build();
    }

    /**
     * @param $parameters
     * @return void
     */
    public function index($parameters): void
    {
        try {
            $this->client->index($parameters);
        } catch (ClientResponseException|MissingParameterException|ServerResponseException $e) {
            Log::channel('elasticsearch_error')->info($e->getMessage());
        }
    }

    /**
     * @param $parameters
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|Promise|array
     */
    public function search($parameters): \Elastic\Elasticsearch\Response\Elasticsearch|Promise|array
    {
        try {
            return $this->client->search($parameters);
        } catch (ClientResponseException|ServerResponseException $e) {
        }
        return [];
    }
}
