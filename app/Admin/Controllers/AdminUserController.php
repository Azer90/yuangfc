<?php

namespace App\Admin\Controllers;

use App\District;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Routing\Controller;

class AdminUserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {

        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.list'))
            ->body($this->grid()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');
        $district=District::where(['level'=>3])->get(['id','name'])->pluck('name','id')->toArray();
        $grid = new Grid(new $userModel());

        $grid->id('ID')->sortable();
        $grid->username(trans('admin.username'));
        $grid->name(trans('admin.name'));
        $grid->mobile('手机号');
        $grid->district_id('管辖区域')->display(function ($province) use ($district) {
            if(empty($province)){
                $str='';
            }else{
                $str=$district[$province];
            }
            return $str;
        });
        $grid->roles(trans('admin.roles'))->pluck('name')->label();
        $grid->wechat_id('是否绑定微信(请前往微信管理用户绑定)')->display(function ($released) {

            return $released > 0 ? '<span style="color: green">已绑定</span>' : '<span style="color: red">未绑定</span>';
        });

        $grid->created_at(trans('admin.created_at'));
        //$grid->updated_at(trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->id('ID');
        $show->username(trans('admin.username'));
        $show->name(trans('admin.name'));
        $show->roles(trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->permissions(trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());
        //$admin_user=request()->route()->parameters();

        $form->display('id', 'ID');

        if (request()->isMethod('POST')) {
            $userTable = config('admin.database.users_table');
            $userNameRules = "required|unique:{$userTable}";
        } else {
            $userNameRules = 'required';
        }
        $form->text('username', trans('admin.username'))->rules($userNameRules);
        $form->text('name', trans('admin.name'))->rules('required');
        $form->text('mobile','手机号')->rules('required');
        $form->image('avatar', trans('admin.avatar'))->move('/images/avatar')->uniqueName();
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);
        $form->select('province_id','省')->options('/api/province')->load('city_id', '/api/city')->rules('required');
        $form->select('city_id','市')->load('district_id', '/api/city')->rules('required');
        $form->select('district_id','区')->rules('required');
        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });
        //保存后回调
        $form->saved(function (Form $form) {
            $user_id=User::where(['type'=>2,'mobile'=>$form->model()->mobile])->value('id');
            if(empty($user_id)){
                User::insert([
                    'name' =>  $form->model()->username,
                    'mobile' => $form->model()->mobile,
                    'password' => $form->model()->password,
                    'type' => 2,
                    'created_at' => date('Y-m-d H:s:i'),
                ]);
            }
        });
        return $form;
    }
}
