<?php

namespace App\Console\Commands;

use App\Facades\Elasticsearch;
use Illuminate\Console\Command;
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
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey('AIzaSyBB4zjf7eY2CGYKtunkOlLEq0xgKpZ61pw');
        $geocoder->setCountry(config('geocoder.country', 'US'));
        $counter = 0;
        $properties = json_decode(file_get_contents(storage_path() . "/properties.json"), true);
        foreach ($properties as $property) {
            $request['index'] = 'properties';
            $request['body'] = [
                'id' => $property['image_id'],
                'address' => $property['street'] . ' ' . $property['citi'],
                'city' => $property['citi'],
                'location' => [
                    "lat" => $geocoder->getCoordinatesForAddress($property['street'] . ' ' . $property['citi'])['lat'],
                    "lon" => $geocoder->getCoordinatesForAddress($property['street'] . ' ' . $property['citi'])['lng']
                ],
                'bed' => $property['bed'],
                'bath' => $property['bath'],
                'price' => $property['price'],
                'sqrft' => $property['sqft'],
                'image' => 'https://ik.imagekit.io/yxftwkca9e/'  . $property['image_id'] . '.jpg?ik-sdk-version=javascript-1.4.3&updatedAt=1661373876924',
                'description' => $property['description']
            ];
            Elasticsearch::index($request);
            $counter++;
            if(($counter % 10) == 0)
            $this->info('Indexed ' . $counter . ' properties...');
        }
        return 0;
    }
}
