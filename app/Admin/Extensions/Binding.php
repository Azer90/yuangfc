<?php

namespace App\Admin\Extensions;

use App\WeChatUser;
use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class Binding
{
    protected $id;
    protected $admin_users;

    public function __construct($id)
    {
        $this->id = $id;

        $this->admin_users=  DB::table('admin_users')->where('wechat_id',0)->get(['id','name'])->pluck('name','id')->tojson() ;
    }

    protected function script()
    {
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');
        $url=route('bind_admin_user');
        $listPath = Request::getRequestUri();
        return <<<SCRIPT

$('.bind_user').on('click', function () {
    var id= $(this).data('id') 
    var surplus= $(this).data('surplus') 
    swal({
        title: "绑定管理员",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "$confirm",
        showLoaderOnConfirm: true,
        cancelButtonText: "$cancel",
        input: "select",
        inputPlaceholder: "请选择管理员",
        inputAutoTrim:true,
        inputOptions:$this->admin_users,
        preConfirm: function(inputValue) {
    
        if(inputValue==''){
          swal.showValidationError("请选择管理员");	   
        }else{
             return new Promise(function(resolve) {
                $.post("$url", {'id': id, _token:LA.token,admin_uid:inputValue}, function (data) {
                 $.pjax({container:'#pjax-container', url: "$listPath" });
                         resolve(data);
       
                 });
            });
        }
       
        }
    }).then(function(result) {
        var data = result.value;
         console.log(data);
        if (typeof data === 'object') {
            if (data.code===1000) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
       

});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='bind_user' style='padding: 0 10px;' href='javascript:void(0);' title='绑定管理员' data-id='{$this->id}'><i class='fa fa-link'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}