<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layouts.vue');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/{any}', function () {
    return view('layouts.vue');
})->where('any', '.*');

Route::post('/properties/search','App\Http\Controllers\Api\PropertiesController@search')->name('properties.search.page');
Route::post('/county/properties', 'App\Http\Controllers\Api\CountiesController@propertiesByCounty');
