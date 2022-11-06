<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/properties','App\Http\Controllers\Api\PropertiesController@index');
Route::post('/properties-geojson','App\Http\Controllers\Api\PropertiesController@indexGeoJson');
Route::post('/properties/autocomplete','App\Http\Controllers\Api\PropertiesController@autocomplete');
Route::post('/properties/search','App\Http\Controllers\Api\PropertiesController@search');
Route::post('/properties/close','App\Http\Controllers\Api\PropertiesController@closeProperties');

Route::post('/properties/draw-polygon-search', 'App\Http\Controllers\Api\PropertiesController@drawPolygonSearch');
Route::post('/properties/draw-distance-search', 'App\Http\Controllers\Api\PropertiesController@drawDistanceSearch');
Route::post('/counties/by-name', 'App\Http\Controllers\Api\CountiesController@searchByName');
Route::post('/properties/school-distance', 'App\Http\Controllers\Api\PropertiesController@schoolDistance');
Route::post('/properties/get', 'App\Http\Controllers\Api\PropertiesController@get');
//Route::post('/properties/amenities', 'App\Http\Controllers\Api\PropertiesController@propertiesAmenities');

