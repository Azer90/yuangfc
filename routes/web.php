<?php

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
use Illuminate\Routing\Router;
Route::get('/', function () {
    return view('welcome');
});

$attributes = [
    'prefix'     => 'wechat',
];
Route::group($attributes, function (Router $router) {
    $router->any('/', 'WeChatController@serve');

});

