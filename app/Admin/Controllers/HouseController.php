<?php

namespace App\Admin\Controllers;

use App\Housings;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class HouseController extends Controller
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
            ->header('房源管理')
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
            ->header('新增房源')
            ->description('添加')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Housings);

        $grid->id('ID')->sortable();
        $grid->title('标题');
        $grid->type('类型');
        $grid->owner('业主');
        $grid->phone('联系方式');
        $grid->years('修建年份');
        $grid->purpose('用途');
        $grid->direction('朝向');
        $grid->room('房');
        $grid->hall('厅');
        $grid->toilet('卫');
        $grid->area('面积');
        $grid->price('价格');
        $grid->renovation('装修类型');
        $grid->floor('楼层');
        $grid->t_floor('总楼层');
        $grid->address('地址');
        $grid->desc('描述');
        $grid->remark('备注');
        $grid->created_at(trans('admin.created_at'));
       // $grid->updated_at(trans('admin.updated_at'));
        //$grid->disableExport();//禁用导出
        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $show = new Show(Housings::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->type('Type');
        $show->owner('Owner');
        $show->phone('Phone');
        $show->years('Years');
        $show->purpose('Purpose');
        $show->direction('Direction');
        $show->room('Room');
        $show->hall('Hall');
        $show->toilet('Toilet');
        $show->area('Area');
        $show->price('Price');
        $show->renovation('Renovation');
        $show->floor('Floor');
        $show->t_floor('T floor');
        $show->address('Address');
        $show->desc('Desc');
        $show->remark('Remark');
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
        $form = new Form(new Housings);

        $form->text('title', '标题');
        $form->switch('type', '类型');
        $form->text('owner', '业主');
        $form->number('phone', '联系方式');
        $form->switch('years', '修建年份');
        $form->switch('purpose', '用途');
        $form->text('direction', '朝向');
        $form->switch('room', '房');
        $form->switch('hall', '厅');
        $form->switch('toilet', '卫');
        $form->decimal('area', '面积')->default(0.0);
        $form->decimal('price', '价格')->default(0.00);
        $form->switch('renovation', '装修类型');
        $form->switch('floor', '楼层');
        $form->switch('t_floor', '总楼层');
        $form->text('address', '地址');
        $form->latlong('latitude', 'longitude', 'Position');
        $form->text('desc', '描述');
        $form->text('remark', '备注');
        $form->footer(function ($footer) {

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        return $form;
    }
}
