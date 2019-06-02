<?php

namespace App\Admin\Controllers;

use App\WantBuy;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WantBuyController extends Controller
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
            ->header('求购信息')
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
        $grid = new Grid(new WantBuy);

        $grid->id('Id');
        $grid->real_name('姓名');
        $grid->mobile('手机号');
        $grid->address('地址');
        $grid->area('面积');
        $grid->price('价钱');
        $grid->ament('房型');
        $grid->created_at('创建时间');
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
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
        $show = new Show(WantBuy::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->province_id('Province id');
        $show->city_id('City id');
        $show->district_id('District id');
        $show->real_name('Real name');
        $show->mobile('Mobile');
        $show->area('Area');
        $show->price('Price');
        $show->ament('Ament');
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
        $form = new Form(new WantBuy);

        $form->number('user_id', 'User id');
        $form->number('province_id', 'Province id');
        $form->number('city_id', 'City id');
        $form->number('district_id', 'District id');
        $form->text('real_name', 'Real name');
        $form->mobile('mobile', 'Mobile');
        $form->decimal('area', 'Area')->default(0.00);
        $form->decimal('price', 'Price')->default(0.00);
        $form->text('ament', 'Ament');

        return $form;
    }
}
