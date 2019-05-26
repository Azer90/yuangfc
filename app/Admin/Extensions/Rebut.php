<?php
namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;
class Rebut
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        $listPath = Request::getRequestUri();
        $url=route('rebut');
        return <<<SCRIPT

        $('.rebut').on('click', function () {
             var id= $(this).data('id');
                   //输入层
           layer.prompt({
              formType: 2,
              value: '',
              title: '驳回理由',
           
             }, function(value, index, elem){
                $.post("$url", {'id': id, _token:LA.token,reason:value}, function (data) {
            
                        if (typeof data === 'object') {
                            if (data.code===200) {
                                swal(data.message, '', 'success');
                            } else {
                                swal(data.message, '', 'error');
                            }
                         }
                       $.pjax({container:'#pjax-container', url: "$listPath" });
                 });
              layer.close(index);
            });
        
        });

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a title='驳回' class='btn btn-xs btn-danger fa fa-close rebut' style='margin-left:5px ' data-id='{$this->id}'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}