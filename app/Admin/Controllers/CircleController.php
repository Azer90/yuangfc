<?php

namespace App\Admin\Controllers;

use App\Circle;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CircleController extends Controller
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
            ->header('商圈管理')
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
        $grid = new Grid(new Circle);

        // 在这里添加字段过滤器
        $grid->filter(function($filter){

            $filter->column(1/2, function ($filter) {
                $filter->equal('province_id', '省')->select('/api/province')->load('city_id', '/api/city');
                $filter->equal('city_id', '市')->select('/api/province')->load('district_id', '/api/city');
                $filter->equal('district_id', '区')->select('/api/province');


                $filter->equal('name', '商圈名');
            });



        });
        $grid->id('ID')->sortable();
        $grid->name('商圈名');
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
        $show = new Show(Circle::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Circle);
        $form->select('province_id','省')->options('/api/province')->load('city_id', '/api/city')->rules('required');
        $form->select('city_id','市')->load('district_id', '/api/city')->rules('required');
        $form->select('district_id','区')->rules('required');
        $form->text('name', '商圈名字')->rules('required|min:2');

        return $form;
    }
}
