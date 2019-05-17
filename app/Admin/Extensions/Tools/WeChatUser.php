<?php
namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class WeChatUser extends AbstractTool
{
    protected function script()
    {
        $url = Request::getRequestUri();
        $get_url=route('user_insert');
        return <<<EOT

$('#wechat_user').click(function () {

       $.get("$get_url", { next_openid: ""} , function(result){
                if(result=='success'){
                    toastr.success('同步用户成功');
                  $.pjax({container:'#pjax-container', url: "$url" });
                }
          });

   

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        return view('admin.tools.wechat_user');
    }
}