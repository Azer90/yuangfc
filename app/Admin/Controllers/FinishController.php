<?php

namespace App\Admin\Controllers;

use App\Housings;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Table;
use App\User;
use App\Tags;
class FinishController extends Controller
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
            ->header('完成中心')
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

        $grid = new Grid(new Housings);
        //dd($grid);
        if(Admin::user()->isAdministrator()){
            $where=['is_zy'=>1];
        }else{
            $district_id=Admin::user()->district_id;
            $where=['is_zy'=>1,'district_id'=>$district_id];
        }

        $grid->model()->where($where)->orderBy('id','desc');
        // 在这里添加字段过滤器
        $grid->filter(function($filter){

            $filter->column(1/2, function ($filter) {
                if(Admin::user()->isAdministrator()){
                    $filter->equal('province_id', '省')->select('/api/province')->load('city_id', '/api/city');
                    $filter->equal('city_id', '市')->select()->load('district_id', '/api/city');
                    $filter->equal('district_id', '区')->select();
                }
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
        $grid->title('标题')->limit(12)->modal('更多', function ($model) {
            $comments = $model->where("id",$model->id)->take(1)->get()->map(function ($comment) {
                return $comment->only(['title','address','desc', 'remark']);
            });

            return new Table(['标题','地址', '描述', '备注'], $comments->toArray(),['table-hover']);
        });
        $grid->providers('房源提供者')->display(function ($providers) {
            return   User::where('id',$providers)->value('name');
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
        $grid->min_price('最低价格');
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

        $grid->tags('标签')->display(function ($tag) {

            return Tags::wherein('id',$tag)->get(['name'])->pluck('name');

        })->label('primary');
        $grid->pictures('房源相册')->gallery(['width' => 40, 'height' => 40,'zooming' => true]);
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

        $form->number('province_id', 'Province id');
        $form->number('city_id', 'City id');
        $form->number('district_id', 'District id');
        $form->number('circle_id', 'Circle id');
        $form->number('floor_id', 'Floor id');
        $form->number('agent_id', 'Agent id');
        $form->text('title', 'Title');
        $form->switch('rentsale', 'Rentsale');
        $form->switch('type', 'Type');
        $form->switch('purpose', 'Purpose');
        $form->text('owner', 'Owner');
        $form->mobile('phone', 'Phone');
        $form->text('years', 'Years');
        $form->text('direction', 'Direction');
        $form->switch('room', 'Room');
        $form->switch('hall', 'Hall');
        $form->switch('toilet', 'Toilet');
        $form->decimal('area', 'Area')->default(0.0);
        $form->decimal('price', 'Price')->default(0.00);
        $form->switch('renovation', 'Renovation');
        $form->switch('floor', 'Floor');
        $form->switch('t_floor', 'T floor');
        $form->text('address', 'Address');
        $form->textarea('desc', 'Desc');
        $form->text('remark', 'Remark');
        $form->text('latitude', 'Latitude');
        $form->text('longitude', 'Longitude');
        $form->switch('setup', 'Setup');
        $form->text('pictures', 'Pictures');
        $form->switch('is_display', 'Is display');
        $form->text('tags', 'Tags');
        $form->decimal('min_price', 'Min price')->default(0.00);
        $form->number('providers', 'Providers');
        $form->switch('is_zy', 'Is zy');

        return $form;
    }
}
