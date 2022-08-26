<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static index($parameters)
**/
class Elasticsearch extends Facade {

protected static function getFacadeAccessor() { return 'elasticsearch'; }

}
