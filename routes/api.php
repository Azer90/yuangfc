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
    $router->get('get_circle', 'DistrictController@get_circle')->name('get_circle');
    $router->get('get_floor', 'DistrictController@get_floor')->name('get_floor');

});

$attributes = [
    'namespace'     => 'Api',
//    'domain'     => 'api.yuang.com',
];
Route::group($attributes,function (Router $router) {
    $router->post('api_login', "LoginController@login")->name('api_login');
    $router->post('api_register', "LoginController@register")->name('api_register');
    $router->post('api_getOpenId', "LoginController@getOpenId")->name('api_getOpenId');
    $router->post('api_checkRegister', "LoginController@checkRegister")->name('api_checkRegister');
    $router->post('api_getCode', "LoginController@getCode")->name('api_getCode');
    $router->post('api_replaceMobile', "LoginController@replaceMobile")->name('api_replaceMobile');
    $router->post('api_hotHouse', "HouseController@hotHouse")->name('api_hotHouse');
    $router->post('api_housList', "HouseController@housList")->name('api_housList');
    $router->post('api_getAreaList', "HouseController@getAreaList")->name('api_getAreaList');
    $router->post('api_getCircleList', "HouseController@getCircleList")->name('api_getCircleList');
    $router->post('api_getFloorList', "HouseController@getFloorList")->name('api_getFloorList');
    $router->post('api_getBuild', "HouseController@getBuild")->name('api_getBuild');
    $router->post('api_getDetails', "HouseController@details")->name('api_getDetails');
    $router->post('api_getBanner', "IndexController@getBanner")->name('api_getBanner');
    $router->post('api_followHandle', "FollowController@followHandle")->name('api_followHandle');
    $router->post('api_entrust_add_data', "EntrustController@add_data")->name('api_entrust_add_data');//hkz
    $router->post('api_appoint', "AppointmentController@appoint")->name('api_appoint');
    $router->post('api_getAppointList', "AppointmentController@getAppointList")->name('api_getAppointList');
    $router->post('api_getSchedule', "AppointmentController@getSchedule")->name('api_getSchedule');
    $router->post('api_addSchedule', "AppointmentController@addSchedule")->name('api_addSchedule');
    $router->post('api_appoinrDelete', "AppointmentController@appoinrDelete")->name('api_appoinrDelete');
    $router->post('api_getCollection', "AppointmentController@getCollection")->name('api_getCollection');
    $router->post('api_delete', "AppointmentController@delete")->name('api_delete');
    $router->post('api_addAgent', "AgentController@addAgent")->name('api_addAgent');
    $router->post('api_getState', "AgentController@getState")->name('api_getState');
    $router->post('api_getEntrustList', "EntrustController@getList")->name('api_getEntrustList');//hkz
    $router->post('api_getUser', "UserController@getUser")->name('api_getUser');//hkz
    $router->post('api_deleteEntrust', "EntrustController@deleteEntrust")->name('api_deleteEntrust');//hkz
    $router->post('api_search', "IndexController@search")->name('api_search');//hkz
    $router->post('api_addWanBuy', "WanBuyController@addWanBuy")->name('api_addWanBuy');//hkz
    $router->post('api_getWanBuy', "WanBuyController@getWanBuy")->name('api_getWanBuy');//hkz
    $router->post('api_deleteWanBuy', "WanBuyController@deleteWanBuy")->name('api_deleteWanBuy');//hkz
    $router->post('api_getUserInfo', "UserController@getUserInfo")->name('api_getUserInfo');//hkz
    $router->post('api_getQr', "CreateQrController@getQr")->name('api_getQr');//hkz
    $router->post('api_getClient', "AppointmentController@getClient")->name('api_getClient');//hkz
    $router->post('api_getMyHouse', "EntrustController@getMyHouse")->name('api_getMyHouse');//hkz
    $router->post('api_getProvince', "DistrictController@getProvince")->name('api_getProvince');//hkz
    $router->post('api_getCity', "DistrictController@getCity")->name('api_getCity');//hkz
    $router->post('api_getLog', "LogController@getLog")->name('api_getLog');//hkz
    $router->post('api_delete', "LogController@delete")->name('api_delete');//hkz
    $router->post('get_agreement', "AgentController@get_agreement")->name('get_agreement');//hkz
});
	