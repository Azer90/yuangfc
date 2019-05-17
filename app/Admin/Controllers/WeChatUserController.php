<?php

namespace App\Admin\Controllers;

use App\WeChatUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use EasyWeChat;
use Illuminate\Http\Request;

class WeChatUserController extends Controller
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
            ->header('微信用户')
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
        $grid = new Grid(new WeChatUser);
        $grid->id('ID')->sortable();
        $grid->nickname('微信昵称')->style('width:200px;');
        $grid->sex('性别')->display(function ($released) {
            switch ($released){
                case 1:
                    $str='男';
                    break;
                case 2:
                    $str= '女';
                    break;
                default : $str='未知';
            }
            return $str;
        });;
        $grid->headimgurl('头像')->image(50, 50);
        $grid->country('国家');
        $grid->province('省份');
        $grid->city('城市');
        $grid->remark('备注');


        $grid->disableExport();//禁用导出
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->tools(function ($tools) {
            $tools->append(new \App\Admin\Extensions\Tools\WeChatUser());
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
        $show = new Show(WeChatUser::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeChatUser);



        return $form;
    }


    public function user_insert(Request $request){
        $next_openid=$request->get('next_openid');
        $app = EasyWeChat::officialAccount();
        $list = $app->user->list($next_openid);
        if(!empty($list['next_openid'])){
            // 先入库存openid
            $openids = (array) $list['data']['openid'];
            $wechat_user=WeChatUser::whereIn('openid', $openids)->take(count($openids))->get(['openid'])->pluck('openid')->toArray();
            $openids = array_values(array_diff($openids, $wechat_user));

            if (empty($openids) && $list['next_openid']) {
                return redirect()->route('user_insert',['next_openid'=>$list['next_openid']]);
            }


            $data = array();
            foreach ($openids as $key => $openid) {
                array_push($data, array(
                    'openid' => trim($openid),
                    'created_at' => date('Y-m-d,H:i:s',time()),
                    'status' => 0,
                ));
            }

            $data && WeChatUser::insert($data);
            if (($list['next_openid'] && $list['count'] > 0)) {
                return redirect()->route('user_insert',['next_openid'=>$list['next_openid']]);
            }

        }
        return redirect()->route('user_info_insert');

    }



    public function user_info_insert(){
        $wechat_user=WeChatUser::where(['status'=> 0])->take(100)->get(['id','openid'])->pluck('openid','id')->toArray();
        if($wechat_user){
                $app = EasyWeChat::officialAccount();
                $user_info_batchget = $app->user->select(array_values($wechat_user));

                $update = array();
                foreach ($user_info_batchget['user_info_list'] as $key => $value) {
                    $update[$value['openid']] = array_merge($value, array('tagid_list' => json_encode($value['tagid_list'])));
                }
                foreach ($wechat_user as $id => $openid) {
                    WeChatUser::where(['id'=> $id])->update(array_merge((array) $update[$openid], array('status' => 1)));
                }

            return redirect()->route('user_info_insert');
        }

        return 'success';
    }
}
