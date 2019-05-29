<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\GetPast;
use App\Admin\Extensions\Rebut;
use App\Entrust;
use App\Tags;
use App\User;
use App\Housings;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
class EntrustController extends Controller
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
            ->header('委托管理')
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
        $grid = new Grid(new Entrust);

        $grid->id('Id');
        $grid->address('所在地');
        $grid->cell_name('小区名');
        $grid->addr('详细地址');
        $grid->name('委托人称呼');
        $grid->area('面积');
        $grid->price('价格');
        $grid->mobile('联系电话');
        $grid->rentsale('租售类型')->using([1 => '出售',2 => '出租']);
        $grid->state('审核状态')->display(function ($state) {
            switch ($state){
                case 1:
                    $str= "<span style='color: #5034ff'>通过</span>";
                    break;
                case 2:
                    $str= "<span style='color: #ff231c'>驳回</span>";
                    break;
                default:
                    $str= "<span style='color: #ff690c'>未审核</span>";

            }
            return $str;
        });
        $grid->reason('驳回理由');
        $grid->created_at('创建时间');

        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();
            // 添加操作
            if($actions->row->state==0){
                $actions->append(new  GetPast($actions->getKey()));
                $actions->append(new  Rebut($actions->getKey()));
            }

        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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

        $show->id('Id');
        $show->province_id('Province id');
        $show->city_id('City id');
        $show->district_id('District id');
        $show->cell_name('Cell name');
        $show->addr('Addr');
        $show->name('Name');
        $show->area('Area');
        $show->price('Price');
        $show->mobile('Mobile');
        $show->rentsale('Rentsale');
        $show->state('State');
        $show->reason('Reason');
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
        $form->text('reason', 'Reason');

        return $form;
    }


    public function to_examine(Request $request){

        $data=$request->all();
        Entrust::where('id',$data['id'])->update(['state'=>1]);
        return Api_success('审核成功,已通过');
    }



    public function rebut(Request $request){
        $data=$request->all();
        Entrust::where('id',$data['id'])->update(['reason'=>$data['reason'],'state'=>2]);
        return Api_success('驳回成功');
    }


}