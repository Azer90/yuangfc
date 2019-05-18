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
    $router->resource('wechat_user', 'WeChatUserController',['only' => ['index','destroy']])->names('admin.wechat_user');//hkz
    $router->resource('users', 'UsersController',['only' => ['index','destroy']])->names('admin.users');//hkz

    $router->post('import', 'ImportController@import')->name('import');
    $router->get('export', 'ImportController@export')->name('export');
    $router->get('user_insert', 'WeChatUserController@user_insert')->name('user_insert');
    $router->get('user_info_insert', 'WeChatUserController@user_info_insert')->name('user_info_insert');
    $router->post('bind_admin_user', 'WeChatUserController@bind_admin_user')->name('bind_admin_user');

});
