<?php

namespace App\Console\Commands;

use App\Facades\Elasticsearch;
use App\Facades\Search;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Geocoder\Geocoder;

class IndexProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index Properties to the Elasticsearch';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey('AIzaSyBB4zjf7eY2CGYKtunkOlLEq0xgKpZ61pw');
        $geocoder->setCountry(config('geocoder.country', 'US'));
        $counter = 0;
        $addedProperties = [];
        $properties = json_decode(file_get_contents(storage_path() . "/properties.json"), true);
        foreach ($properties as $property) {
            $image = 'https://ik.imagekit.io/yxftwkca9e/'  . $property['image_id'] . '.jpg?ik-sdk-version=javascript-1.4.3&updatedAt=1661373876924';
            $file_headers = @get_headers($image);
            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
                $image = '/storage/icons/house.svg';
            }
            $body = [
                'id' => $property['image_id'],
                'address' => $property['street'],
                'city' => $property['citi'],
                'location' => [
                    "lat" => $geocoder->getCoordinatesForAddress($property['street'] . ' ' . $property['citi'])['lat'],
                    "lon" => $geocoder->getCoordinatesForAddress($property['street'] . ' ' . $property['citi'])['lng']
                ],
                'bed' => $property['bed'],
                'bath' => $property['bath'],
                'price' => $property['price'],
                'sqrft' => $property['sqft'],
                'image' => $image,
                'description' => $property['description']
            ];
            $request['body'][] = [
                'index' => [
                    '_index' => 'properties',
                    '_id' => intval( '00000' . $property['image_id'])
                ]
            ];
            $request['body'][] = $body;

            if ($counter != 0 && $counter % 500 == 0) {
                $response = Elasticsearch::bulk($request);
                $request = ['body' => []];
                unset($response);
                $this->info('Indexed ' . $counter . ' properties...');
            }
            $counter++;
        }
        $this->info('Number of properties: ' . count(array_unique(array_column($properties, 'street'))));
        return 0;
    }
}
