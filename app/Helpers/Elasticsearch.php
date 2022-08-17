<?php

namespace App\Helpers;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

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
        $this->clientBuilder = $this->clientBuilder->build();
    }
}
