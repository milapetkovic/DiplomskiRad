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
        $properties = json_decode(file_get_contents(storage_path() . "/properties.json"), true);
        foreach ($properties as $property) {
            if(!in_array($property['street'], $addedProperties)) {
                $url = "http://nominatim.openstreetmap.org/";
                $nominatim = new Nominatim($url);
                $search = $nominatim->newSearch();
                $search->query($property['street'])->city( $property['citi']);
                try {
                    $result =  $nominatim->find($search);
                } catch (\Exception $e) {
                    $result = [];
                }
                if($result) {
                    $body = [
                        'id' => $property['image_id'],
                        'address' => $property['street'],
                        'city' => $property['citi'],
                        'location' => array((float)$result[0]['lon'],(float)$result[0]['lat']),
                        'bed' => $property['bed'],
                        'bath' => $property['bath'],
                        'price' => $property['price'],
                        'sqrft' => $property['sqft'],
                        'image' => '/storage/socal_pics/' . $property['image_id'] . '.jpg',
                        'description' => $property['description'] ?? ""
                    ];
                    $request['body'][] = [
                        'index' => [
                            '_index' => 'properties',
                            '_id' => intval( '00000' . $property['image_id'])
                        ]
                    ];
                    $request['body'][] = $body;
                    $this->info($property['street'] . ', ' . $property['citi']);
                }

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
