<?php

namespace App\Helpers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Http\Promise\Promise;
use Illuminate\Support\Facades\Log;

class Elasticsearch
{
    private ClientBuilder $clientBuilder;
    private Client $client;

    /**
     *
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->clientBuilder = ClientBuilder::create();
        $this->clientBuilder->setHosts([
            config('app.elasticsearch_host')
            . ':'
            . config('app.elasticsearch_port')]);
        $this->client = $this->clientBuilder->build();
    }

    /**
     * Index documents
     *
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
     * Search documents
     *
     * @param $parameters
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|Promise|array
     */
    public function search($parameters): \Elastic\Elasticsearch\Response\Elasticsearch|Promise|array
    {
        try {
            return $this->client->search($parameters);
        } catch (ClientResponseException|ServerResponseException $e) {
            Log::channel('elasticsearch_error')->info($e->getMessage());
            return [];
        }
    }

    /**
     * Check if index exists
     *
     * @param $parameters
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|Promise|null
     */
    public function exists($parameters): \Elastic\Elasticsearch\Response\Elasticsearch|Promise|null
    {
        try {
            return $this->client
                ->indices()
                ->exists($parameters);
        } catch (ClientResponseException|MissingParameterException|ServerResponseException $e) {
            Log::channel('elasticsearch_error')->info($e->getMessage());
            return null;
        }
    }

    /**
     * Index multiple documents
     *
     * @param $parameters
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|Promise|null
     */
    public function bulk($parameters): \Elastic\Elasticsearch\Response\Elasticsearch|Promise|null
    {
        try {
            return $this->client
                ->bulk($parameters);
        } catch (ClientResponseException|ServerResponseException $e) {
            Log::channel('elasticsearch_error')->info($e->getMessage());
            return null;
        }
    }

    /**
     * Update Document
     *
     * @param $parameters
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|Promise|null
     * @throws MissingParameterException
     */
    public function update($parameters): \Elastic\Elasticsearch\Response\Elasticsearch|Promise|null
    {
        try {
            return $this->client
                ->update($parameters);
        } catch (ClientResponseException|ServerResponseException $e) {
            Log::channel('elasticsearch_error')->info($e->getMessage());
            return null;
        }
    }
}
