<?php

namespace App\Admin\Controllers;

use App\AgentCheck;
use App\Http\Controllers\Controller;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\Extensions\GetPast;
use App\Admin\Extensions\Rebut;
use Illuminate\Http\Request;
class AgentCheckController extends Controller
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
            ->header('经纪人审核')
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
        $grid = new Grid(new AgentCheck);

        $grid->id('Id');
        $grid->address('所在地');
        $grid->real_name('真实姓名');
        $grid->id_card('身份证号');
        $grid->mobile('电话');
        $grid->created_at('创建时间');
        $grid->reason('驳回理由');
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
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();
            // 添加操作
            if($actions->row->state==0){
                $actions->append(new  GetPast($actions->getKey(),route('agent_examine')));
                $actions->append(new  Rebut($actions->getKey(),route('agent_rebut')));
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
        $show = new Show(AgentCheck::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AgentCheck);



        return $form;
    }

    public function agent_examine(Request $request){

        $data=$request->all();
        $user_id=AgentCheck::where('id',$data['id'])->value('user_id');
        AgentCheck::where('id',$data['id'])->update(['state'=>1]);
        User::where('id',$user_id)->update(['type'=>1]);
        return Api_success('审核成功,已通过');
    }



    public function agent_rebut(Request $request){
        $data=$request->all();
        AgentCheck::where('id',$data['id'])->update(['reason'=>$data['reason'],'state'=>2]);
        return Api_success('驳回成功');
    }
}
