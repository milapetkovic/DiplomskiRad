<?php


namespace App\Helpers;


use App\Facades\Elasticsearch;
use Eastwest\Json\Json;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Helper
{
    /**
     * @param $indexName
     * @return mixed
     */
    public static function checkIfIndexExist($indexName)
    {
        return Elasticsearch::exists([
            'index' => $indexName
        ]);
    }

    public static function createPropertiesIndex()
    {
        $body = Json::decode(
            File::get(app_path('Helpers/IndexSettings/index-properties.json')),
            true
        );

   /*     $url = config()elastic_host . ':' . $website->elastic_port . '/' . $index;
        $response = Http::withHeaders($this->headers)
            ->put($url, $body);

        if ($response->ok()) {}*/
    }

}
