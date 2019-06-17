<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\FalseDelete;
use App\AgentCheck;
use App\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('经纪人管理')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        if(Admin::user()->isAdministrator()){

            $grid->model()->where('type',1)->orderBy('id','desc');
        }else{

           $district_id=Admin::user()->district_id;
            $grid->model()->from('users as u')->join('agent_check as ac','ac.user_id','=','u.id')->where(['u.type'=>1,'ac.district_id'=>$district_id])->orderBy('u.id','desc');
        }

        $grid->id('Id');
        $grid->name('姓名(昵称)');
        $grid->wchat_name('微信名称');
        $grid->sex('性别')->using(['0' => '保密','1' => '男', '2' => '女']);
        $grid->mobile('电话号码');
        //$grid->email('邮箱');
        $grid->type('用户类型')->using(['0' => '普通用户','1' => '经纪人', '2' => '城市管理员']);;
        $grid->avatar('头像')->gallery(['width' => 50, 'height' => 50,'zooming' => true]);
        $grid->created_at('创建时间');
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();
            // 添加操作
             $actions->append(new FalseDelete($actions->getKey(),route('f_delete_agent')));
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->mobile('Mobile');
        $show->email('Email');
        $show->password('Password');
        $show->remember_token('Remember token');
        $show->open_id('Open id');
        $show->avatar('Avatar');
        $show->wchat_name('Wchat name');
        $show->type('Type');
        $show->sex('Sex');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('name', 'Name');
        $form->mobile('mobile', 'Mobile');
        $form->email('email', 'Email');
        $form->password('password', 'Password');
        $form->text('remember_token', 'Remember token');
        $form->text('open_id', 'Open id');
        $form->image('avatar', 'Avatar');
        $form->text('wchat_name', 'Wchat name');
        $form->switch('type', 'Type');
        $form->switch('sex', 'Sex');

        return $form;
    }


    public function f_delete(Request $request){

        $data=$request->all();
        User::where('id',$data['id'])->update(['type'=>0]);
        AgentCheck::where('user_id',$data['id'])->delete();
        return Api_success('删除成功');
    }
}
