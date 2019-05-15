<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
$attributes = [
    'namespace'     => 'Api',
];
Route::group($attributes, function (Router $router) {

    $router->get('province', 'DistrictController@province')->name('province');
    $router->get('city', 'DistrictController@city')->name('city');
    $router->get('circle', 'DistrictController@circle')->name('circle');
    $router->get('floor', 'DistrictController@floor')->name('floor');
    $router->get('agent', 'DistrictController@agent')->name('agent');

});

Route::group($attributes, function (Router $router) {
    $router->get('test', 'HouseTestController@test')->name('test');
});
