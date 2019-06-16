<?php

namespace App\Admin\Controllers;

use App\WantBuy;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Entrust;
use Encore\Admin\Facades\Admin;
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
            ->header('求购求租')
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
        $grid = new Grid(new Entrust('wantbuy'));
        // 在这里添加字段过滤器
        $grid->filter(function($filter){

            if(Admin::user()->isAdministrator()){
                $filter->equal('province_id', '省')->select('/api/province')->load('city_id', '/api/city');
                $filter->equal('city_id', '市')->select()->load('district_id', '/api/city');
                $filter->equal('district_id', '区')->select();
            }

        });
        $grid->id('Id');
        $grid->address('所在地');
        $grid->cell_name('小区名');
        $grid->addr('详细地址');
        $grid->name('委托人称呼');
        $grid->area('面积');
        $grid->price('价格');
        $grid->mobile('联系电话');
        $grid->rentsale('租售类型')->using([1 => '出售',2 => '出租',3 => '求购',4 => '求租']);
        $grid->type('委托类型')->using([1 => '直接委托',2 => '代委托']);
        $grid->is_buy('是否购买')->radio([
            2 => '未购买',
            3 => '已购买',
        ]);
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
        $show = new Show(Entrust::findOrFail($id));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Entrust);

        $form->number('province_id', 'Province id');
        $form->number('city_id', 'City id');
        $form->number('district_id', 'District id');
        $form->text('cell_name', 'Cell name');
        $form->text('addr', 'Addr');
        $form->text('name', 'Name');
        $form->decimal('area', 'Area')->default(0.00);
        $form->decimal('price', 'Price')->default(0.00);
        $form->mobile('mobile', 'Mobile');
        $form->switch('rentsale', 'Rentsale');
        $form->switch('state', 'State');
        $form->radio('is_buy', 'is_buy')->options([
            2 => '未购买',
            3 => '已购买',

        ]);
        $form->text('reason', 'Reason');

        return $form;
    }
}
