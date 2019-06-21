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
    $router->resource('banner', 'BannerController')->names('admin.banner');//hkz
    $router->resource('tag', 'TagController')->names('admin.tag');//hkz
    $router->resource('wechat_user', 'WeChatUserController',['only' => ['index','destroy']])->names('admin.wechat_user');//hkz
    $router->resource('users', 'UsersController',['only' => ['index','destroy']])->names('admin.users');//hkz
    $router->resource('agent', 'AgentController',['only' => ['index','destroy']])->names('admin.agent');//hkz
    $router->resource('agent_check', 'AgentCheckController',['only' => ['index','destroy']])->names('admin.agent_check');//hkz
    $router->resource('entrust', 'EntrustController')->names('admin.entrust');//hkz
    $router->resource('makeorder', 'MakeOrderController')->names('admin.makeorder');//hkz
    $router->resource('wantbuy', 'WantBuyController')->names('admin.wantbuy');//hkz
    $router->resource('finish', 'FinishController')->names('admin.finish');//hkz
    $router->resource('userCenter', 'UserCenterController')->names('admin.userCenter');//hkz

    $router->post('import', 'ImportController@import')->name('import');
    $router->get('export', 'ImportController@export')->name('export');
    $router->get('user_insert', 'WeChatUserController@user_insert')->name('user_insert');
    $router->get('user_info_insert', 'WeChatUserController@user_info_insert')->name('user_info_insert');
    $router->post('bind_admin_user', 'WeChatUserController@bind_admin_user')->name('bind_admin_user');

    $router->any('wechat_check/{wecode_id}', 'LoginAuthController@wechat_check')->name('wechat_check');
    $router->post('sweep_code_check', 'LoginAuthController@sweep_code_check')->name('sweep_code_check');
    $router->post('to_examine', 'EntrustController@to_examine')->name('to_examine');
    $router->post('rebut', 'EntrustController@rebut')->name('rebut');
    $router->post('agent_examine', 'AgentCheckController@agent_examine')->name('agent_examine');
    $router->post('agent_rebut', 'AgentCheckController@agent_rebut')->name('agent_rebut');
    $router->post('f_delete_w', 'WantBuyController@f_delete')->name('f_delete_w');
    $router->post('f_delete_e', 'EntrustController@f_delete')->name('f_delete_e');
    $router->post('f_delete_m', 'MakeOrderController@f_delete')->name('f_delete_m');
    $router->post('f_delete_agent', 'AgentController@f_delete')->name('f_delete_agent');
    $router->post('finish_center', 'HouseController@finish_center')->name('finish_center');
    $router->post('add_userCenter', 'WantBuyController@add_userCenter')->name('add_userCenter');
    $router->post('add_userCenter_m', 'MakeOrderController@add_userCenter')->name('add_userCenter_m');
    $router->post('f_delete_uc', 'UserCenterController@f_delete')->name('f_delete_uc');

});
