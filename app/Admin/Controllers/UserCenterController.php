<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\FalseDelete;
use App\UserCenter;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
class UserCenterController extends Controller
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
            ->header('用户中心')
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
        $grid = new Grid(new UserCenter);
        // 在这里添加字段过滤器
        $grid->filter(function($filter){
            $filter->scope('is_delete', '已完成');
            if(Admin::user()->isAdministrator()){
                $filter->equal('province_id', '省')->select('/api/province')->load('city_id', '/api/city');
                $filter->equal('city_id', '市')->select()->load('district_id', '/api/city');
                $filter->equal('district_id', '区')->select();
            }

        });
        $grid->address('所在地');
        $grid->name('姓名');
        $grid->mobile('手机号');
        $grid->agent_name('经纪人');
        $grid->is_kf('是否看房')->using([0 => '未看房',1 => '已看房']);
        $grid->is_buy('是否交易')->using([ 0 => '未交易', 1 => '已交易']);
        $grid->remarks('备注信息');
        $grid->created_at('创建时间');
        $grid->disableExport();//禁用导出

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
            // 添加操作
            if($actions->row->is_delete==0){
                $actions->append(new FalseDelete($actions->getKey(),route('f_delete_uc')));
            }

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
        $show = new Show(UserCenter::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserCenter);

        if(Admin::user()->isAdministrator()){
            $id = request()->route('userCenter');

            $city_id = 0;
            $district_id = 0;
            if ($id)
            {
                $model = $form->model()->find($id);

                $city_id = $model->city_id;
                $district_id= $model->district_id;
            }
            $form->select('province_id','省')->options('/api/province')->load('city_id', '/api/city',$city_id)->rules('required');
            $form->select('city_id','市')->load('district_id', '/api/city',$district_id)->rules('required');
            $form->select('district_id','区')->rules('required');

        }else{
            $form->hidden('province_id')->default(Admin::user()->province_id);
            $form->hidden('city_id')->default(Admin::user()->city_id);
            $form->hidden('district_id')->default(Admin::user()->district_id);

        }
        $form->text('name', '姓名');
        $form->mobile('mobile', '电话');
        $form->text('agent_name', '经纪人');

        $form->radio('is_kf', '是否看房')->options([
            0 => '未看房',
            1 => '已看房',

        ]);
        $form->radio('is_buy', '是否交易')->options([
            0 => '未交易',
            1 => '已交易',

        ]);
        $form->text('remarks', '备注信息');
        return $form;
    }


    public function f_delete(Request $request){

        $data=$request->all();
        UserCenter::where('id',$data['id'])->update(['is_delete'=>1]);
        return Api_success('删除成功');
    }
}
