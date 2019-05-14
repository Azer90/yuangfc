<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('admin_users', 'AdminUserController')->names('admin.admin_users');//hkz
    $router->resource('house', 'HouseController')->names('admin.house');//hkz
    $router->resource('twohouse', 'TwoHouseController')->names('admin.twohouse');//hkz
    $router->resource('renting', 'RentingController')->names('admin.renting');//hkz
    $router->resource('circle', 'CircleController')->names('admin.circle');//hkz
    $router->resource('floor', 'FloorController')->names('admin.floor');//hkz
    $router->resource('users', 'UsersController')->names('admin.users');//hkz

    $router->post('import', 'ImportController@import')->name('import');
    $router->get('export', 'ImportController@export')->name('export');

});
