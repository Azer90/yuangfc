<?php

namespace App\Admin\Controllers;

use App\Housings;
use App\MakeOrder;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
class MakeOrderController extends Controller
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
            ->header('预约管理')
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
        $grid = new Grid(new MakeOrder);
        $grid->id('Id');
        $grid->title('预约房源')->modal('房源信息', function ($model) {
            $comments = Housings::where("id",$model->house_id)->take(1)->get()->map(function ($comment) {
                $data=$comment->only(['id','title','address','type', 'rentsale','owner','phone']);
                if($data['rentsale']==1){
                    $data['rentsale']='出售';
                }elseif($data['rentsale']==2){
                    $data['rentsale']='出租';
                }else{
                    $data['rentsale']='未知';
                }
                if($data['type']==1){
                    $data['type']='新房';
                }elseif($data['type']==2){
                    $data['type']='二手房';
                }else{
                    $data['type']='未知';
                }
                return $data;
            });

            return new Table(['ID','标题','地址', '房源类型', '租售类型', '业主', '联系方式'], $comments->toArray(),['table-hover']);
        });;
        $grid->name('经纪人姓名');
        $grid->wchat_name('预约人微信名');
        $grid->make_name('称呼');
        $grid->make_mobile('电话');
        $grid->time('预约日期');
        $grid->time_slot('时间段')->using([0 => '全天',1 => '上午',2 => '下午',3 => '晚上']);
        $grid->remark('备注');

        $grid->is_divide('是否分成')->radio([
            0 => '未分成',
            1 => '已分成',
        ]);
        $grid->state('是否看房')->radio([
            0 => '未看房',
            1 => '已看房',
            2 => '已取消',
        ]);
        $grid->created_at('创建时间');

        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();
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
        $show = new Show(MakeOrder::findOrFail($id));

        $show->id('Id');
        $show->house_id('House id');
        $show->agent_id('Agent id');
        $show->make_id('Make id');
        $show->make_name('Make name');
        $show->make_mobile('Make mobile');
        $show->ID_card('ID card');
        $show->time('Time');
        $show->time_slot('Time slot');
        $show->remark('Remark');
        $show->is_divide('Is divide');
        $show->state('State');
        $show->add_schedule('Add schedule');
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
        $form = new Form(new MakeOrder);

        $form->number('house_id', 'House id');
        $form->number('agent_id', 'Agent id');
        $form->number('make_id', 'Make id');
        $form->text('make_name', 'Make name');
        $form->text('make_mobile', 'Make mobile');
        $form->text('ID_card', 'ID card');
        $form->date('time', 'Time')->default(date('Y-m-d'));
        $form->switch('time_slot', 'Time slot');
        $form->text('remark', 'Remark');

        $form->radio('is_divide', 'Is divide')->options([
            0 => '未分成',
            1 => '已分成',
        ]);
        $form->radio('state', 'State')->options([
            0 => '未看房',
            1 => '已看房',
            2 => '已取消',
        ]);
        $form->switch('add_schedule', 'Add schedule');

        return $form;
    }
}
