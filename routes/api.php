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

$attributes = [
    'namespace'     => 'Api',
//    'domain'     => 'api.yuang.com',
];
Route::group($attributes,function (Router $router) {
    $router->post('api_login', "LoginController@login")->name('api_login');
    $router->post('api_register', "LoginController@register")->name('api_register');
    $router->post('api_getOpenId', "LoginController@getOpenId")->name('api_getOpenId');
    $router->post('api_hotHouse', "HouseController@hotHouse")->name('api_hotHouse');
    $router->post('api_housList', "HouseController@housList")->name('api_housList');
    $router->post('api_getAreaList', "HouseController@getAreaList")->name('api_getAreaList');
    $router->post('api_getCircleList', "HouseController@getCircleList")->name('api_getCircleList');
    $router->post('api_getBuild', "HouseController@getBuild")->name('api_getBuild');
    $router->post('api_getDetails', "HouseController@details")->name('api_getDetails');
    $router->post('api_getBanner', "IndexController@getBanner")->name('api_getBanner');
});
