<?php

namespace App\Admin\Controllers;

use App\Circle;
use App\Floor;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FloorController extends Controller
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
            ->header('楼盘管理')
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
        $grid = new Grid(new Floor);
        if(!Admin::user()->isAdministrator()){
            $grid->model()->where(['district_id'=>Admin::user()->district_id]);
        }
        // 在这里添加字段过滤器
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
                if(Admin::user()->isAdministrator()){
                    $filter->equal('province_id', '省')->select('/api/province')->load('city_id', '/api/city');
                    $filter->equal('city_id', '市')->select()->load('district_id', '/api/city');
                    $filter->equal('district_id', '区')->select()->load('circle_id', '/api/circle');
                    $filter->equal('circle_id', '商圈')->select();
                }else{
                    $circle_data=Circle::where('district_id', Admin::user()->district_id)->get(['id','name'])->pluck('name','id')->toArray();
                    $filter->equal('circle_id', '商圈')->select($circle_data);
                }

                $filter->equal('name', '楼盘名');
            });



        });
        $grid->id('ID')->sortable();
        $grid->column('circle.name','商圈名');
        $grid->name('楼盘名');
        $grid->created_at(trans('admin.created_at'));
        $grid->disableExport();//禁用导出

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
        $show = new Show(Floor::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Floor);

        if(Admin::user()->isAdministrator()){
            $id = request()->route('floor');
            $city_id = 0;
            $district_id = 0;
            $circle_id = 0;
            if ($id)
            {
                $model = $form->model()->find($id);
                $city_id = $model->city_id;
                $district_id= $model->district_id;
                $circle_id= $model->circle_id;
            }
            $form->select('province_id','省')->options('/api/province')->load('city_id', '/api/city',$city_id)->rules('required');
            $form->select('city_id','市')->load('district_id', '/api/city',$district_id)->rules('required');
            $form->select('district_id','区')->load('circle_id', '/api/circle',$circle_id)->rules('required');
            $form->select('circle_id','商圈')->rules('required');
        }else{
            $form->hidden('province_id')->default(Admin::user()->province_id);
            $form->hidden('city_id')->default(Admin::user()->city_id);
            $form->hidden('district_id')->default(Admin::user()->district_id);
            $form->select('circle_id','商圈')->options('/api/circle',['q'=>Admin::user()->district_id])->load('floor_id', '/api/floor')->rules('required');
        }

        $form->text('name', '楼盘名字')->rules('required|min:2');

        return $form;
    }
}
