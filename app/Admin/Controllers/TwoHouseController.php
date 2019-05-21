<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\HouseExporter;
use App\Admin\Extensions\Tools\ImportTool;
use App\Housings;
use App\User;
use App\Tags;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\MessageBag;
class TwoHouseController extends Controller
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
            ->header('二手房管理')
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
        if(Admin::user()->isAdministrator()){
            $where=['type'=>2];
        }else{
            $district_id=Admin::user()->district_id;
            $where=['type'=>2,'district_id'=>$district_id];
        }

        $grid->model()->where($where);
        // 在这里添加字段过滤器
        $grid->filter(function($filter){

            $filter->column(1/2, function ($filter) {
                $filter->equal('owner', '业主');
                $filter->equal('phone', '联系方式')->mobile();
                $filter->year('years', '修建年份');
                $filter->equal('direction', '朝向');
            });

            $filter->column(1/2, function ($filter) {
                $filter->equal('room', '房')->integer();
                $filter->equal('hall', '厅')->integer();
                $filter->equal('toilet', '卫')->integer();
                $filter->equal('type','租售类型')->radio([
                    0 => 'All',
                    1 => '出租',
                    2 => '出售',
                ]);
                $filter->equal('purpose','用途')->radio([
                    0 => 'All',
                    1 => '住宅',
                    2 => '别墅',
                    3 => '商铺',
                    4 => '写字楼'
                ]);
                $filter->equal('renovation','装修类型')->radio([
                    0 => 'All',
                    1 => '精装修',
                    2 => '简装',
                    3 => '清水房'
                ]);
            });


        });
        $grid->id('ID')->sortable();
        $grid->title('标题')->limit(13)->modal('更多', function ($model) {
            $comments = $model->where("id",$model->id)->take(1)->get()->map(function ($comment) {
                return $comment->only(['title','address','desc', 'remark']);
            });

            return new Table(['标题','地址', '描述', '备注'], $comments->toArray(),['table-hover']);
        });

        $grid->rentsale('租售')->display(function ($released) {
            switch ($released){
                case 1:
                    $str='出售';
                    break;
                case 2:
                    $str= '出租';
                    break;
                default : $str='';
            }
            return $str;
        });
        $grid->purpose('用途')->display(function ($released) {
            switch ($released){
                case 1:
                    $str='住宅';
                    break;
                case 2:
                    $str= '别墅';
                    break;
                case 3:
                    $str= '商铺';
                    break;
                case 4:
                    $str= '写字楼';
                    break;
                default:$str="";
            }
            return $str;
        });
        $grid->type('类型')->display(function ($released) {
            switch ($released){
                case 1:
                    $str='新房';
                    break;
                case 2:
                    $str= '二手房';
                    break;
                default:$str='';
            }
            return $str;
        });
        $grid->owner('业主');
        $grid->phone('联系方式');
        $grid->years('修建年份');

        $grid->direction('朝向');
        $grid->room('房');
        $grid->hall('厅');
        $grid->toilet('卫');
        $grid->area('面积');
        $grid->price('价格');
        $grid->renovation('装修类型')->display(function ($released) {
            switch ($released){
                case 1:
                    $str='精装修';
                    break;
                case 2:
                    $str= '简装';
                    break;
                case 3:
                    $str= '清水房';
                    break;
                default:$str='';
            }
            return $str;
        });
        $grid->floor('楼层');
        $grid->t_floor('总楼层');
        //$grid->created_at(trans('admin.created_at'));

        $grid->pictures('房源相册')->gallery(['width' => 40, 'height' => 40,'zooming' => true]);

        $grid->exporter(new HouseExporter());
        $grid->actions(function ($actions) {
            $actions->disableView();
            //$actions->append('<a href="' . LayerPhoto(url('admin/scenic/get/photo'),1) . '"><i class="fa fa-eye"></i>查看相册</a>');
        });

        $grid->tools(function ($tools) {
            $tools->append(new ImportTool(route('import')));
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
        if(Admin::user()->isAdministrator()){

            $form->select('province_id','省')->options('/api/province')->load('city_id', '/api/city');
            $form->select('city_id','市')->load('district_id', '/api/city');
            $form->select('district_id','区')->load('circle_id', '/api/circle');
            $form->select('circle_id','商圈')->load('floor_id', '/api/floor');
            $form->select('floor_id','楼盘');
            $form->select('agent_id','经纪人')->options('/api/agent')->rules('required');
        }else{
            $form->hidden('province_id')->default(Admin::user()->province_id);
            $form->hidden('city_id')->default(Admin::user()->city_id);
            $form->hidden('district_id')->default(Admin::user()->district_id);
            $form->select('circle_id','商圈')->options('/api/circle',['q'=>Admin::user()->district_id])->load('floor_id', '/api/floor')->rules('required');
            $form->select('floor_id','楼盘')->rules('required');
            $form->select('agent_id','经纪人')->options('/api/agent');
        }
        $form->text('title', '标题')->rules('required|min:3');
        $form->radio('rentsale', '租售类型')->options([1 => '出售', 2 => '出租'])->rules('required');
        $form->radio('type', '房源类型')->options([1 => '新房', 2 => '二手房'])->rules('required');
        $form->radio('purpose', '用途')->options([1 => '住宅', 2 => '别墅', 3 => '商铺', 4 => '写字楼'])->rules('required');
        $form->text('owner', '业主姓名')->rules('required');
        $form->mobile('phone', '联系方式')->rules('required');
        $form->datetime('years', '修建年份')->format('YYYY')->default(date('Y'))->rules('required');

        $form->text('direction', '朝向')->placeholder('填写朝向,如:坐南朝北,南,等')->rules('required');
        $form->slider('room', '房')->options(['max' => 10, 'min' => 1, 'step' => 1, 'postfix' => '房'])->rules('required');
        $form->slider('hall', '厅')->options(['max' => 10, 'min' => 1, 'step' => 1, 'postfix' => '厅'])->rules('required');
        $form->slider('toilet', '卫')->options(['max' => 10, 'min' => 1, 'step' => 1, 'postfix' => '卫'])->rules('required');
        $form->decimal('area', '面积')->default(0.0)->rules('required');
        $form->decimal('price', '价格')->default(0.00)->rules('required');
        $form->radio('renovation', '装修类型')->options([1 => '精装修', 2 => '简装', 3 => '清水房'])->rules('required');
        $form->number('floor', '楼层')->min(1)->max(100)->rules('required');
        $form->number('t_floor', '总楼层')->min(1)->max(100)->rules('required');
        $form->address('latitude', 'longitude','地址','address');
        $form->textarea('desc', '描述');
        $form->textarea('remark', '备注');
        // 多图
        $form->multipleImage('pictures','图片')->removable()->sortable()->move('/images/house/'.date('Y-m-d'))->uniqueName();
        $form->radio('setup', '设置')->options([0=>'不设置',1 => '热门']);
        $form->multipleSelect('tags','标签')->options(Tags::all()->pluck('name', 'id'));
        $states = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];

        $form->switch('is_display','是否显示')->states($states);
        $form->saving(function (Form $form) {
            if(count($form->tags)>4){
                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => '标签不能超过3个',
                ]);

                return back()->with(compact('error'));
            }
            $agent_id=$form->agent_id;
            if(empty($agent_id)){
                $user_id=User::where(['type'=>2,'mobile'=>Admin::user()->mobile])->value('id');
                $form->agent_id=empty($user_id)?0:$user_id;
            }

        });
        return $form;
    }
}
