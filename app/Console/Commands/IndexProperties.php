<?php

namespace App\Console\Commands;

use App\Facades\Elasticsearch;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use maxh\Nominatim\Exceptions\NominatimException;
use maxh\Nominatim\Nominatim;

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
     * @throws NominatimException|GuzzleException
     */
    public function handle(): int
    {
        $counter = 0;
        $addedProperties = [];
        $request = [];
        $properties = json_decode(file_get_contents(storage_path() . "/places.json"), true);
        foreach ($properties as $property) {
            if(!in_array($property['street'], $addedProperties)) {
                $image = 'https://ik.imagekit.io/yxftwkca9e/'  . $property['image_id'] . '.jpg?ik-sdk-version=javascript-1.4.3&updatedAt=1661373876924';
                $file_headers = @get_headers($image);
                if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
                    $image = '/storage/icons/house.svg';
                }
                $url = "http://nominatim.openstreetmap.org/";
                $nominatim = new Nominatim($url);
                $search = $nominatim->newSearch();
                $search->query('Street W, Brawley')->city('Salton City');
                $result =  $nominatim->find($search);
                $body = [
                    'id' => $property['image_id'],
                    'address' => $property['street'],
                    'city' => $property['citi'],
                    'location' => [
                        "lat" => $result[0]['lat'],
                        "lon" => $result[0]['lat']
                    ],
                    'bed' => $property['bed'],
                    'bath' => $property['bath'],
                    'price' => $property['price'],
                    'sqrft' => $property['sqft'],
                    'image' => $image,
                    'description' => $property['description'] ?? ""
                ];
                $request['body'][] = [
                    'index' => [
                        '_index' => 'properties_secondary',
                        '_id' => intval( '00000' . $property['image_id'])
                    ]
                ];
                $request['body'][] = $body;

                if ($counter != 0 && $counter % 10 == 0) {
                    $response = Elasticsearch::bulk($request);
                    $request = ['body' => []];
                    unset($response);
                    $this->info('Indexed ' . $counter . ' properties...');
                }
                $counter++;
                $addedProperties[] = $property['street'];
            }
        }
        if(isset($request['body']) && count($request['body']))
            Elasticsearch::bulk($request);
        $this->info('Number of properties: ' . count(array_unique(array_column($properties, 'street'))));
        return 0;
    }
}
