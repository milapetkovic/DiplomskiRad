<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static index($parameters)
 * @method static search($parameters)
 *
 **/
class Elasticsearch extends Facade {

protected static function getFacadeAccessor() { return 'elasticsearch'; }

}
