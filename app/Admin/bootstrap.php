<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Grid;
use Encore\Admin\Form;
use App\Admin\Extensions\Tools\Address;
use Encore\Admin\Facades\Admin;
Encore\Admin\Form::forget(['map', 'editor']);
Admin::js(asset('layer/layer.js'));

Form::extend('address', Address::class);
// 覆盖`admin`命名空间下的视图
app('view')->prependNamespace('admin', resource_path('views/admin'));


Grid::init(function (Grid $grid) {

    $grid->actions(function (Grid\Displayers\Actions $actions) {
        $actions->disableView();
    });
});
Form::init(function (Form $form) {

    // 去掉`重置`按钮
    $form->disableReset();
    $form->disableEditingCheck();

    $form->disableCreatingCheck();

    $form->disableViewCheck();

});