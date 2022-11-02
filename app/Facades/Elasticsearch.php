<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static index($parameters)
 * @method static bulk($parameters)
 * @method static search($parameters)
 * @method static exists($parameters)
 * @method static update($parameters)
 *
 **/
class Elasticsearch extends Facade {

protected static function getFacadeAccessor() { return 'elasticsearch'; }

}
