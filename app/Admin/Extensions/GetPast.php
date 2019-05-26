<?php
namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;
class GetPast
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        $listPath = Request::getRequestUri();
        $url=route('to_examine');
        return <<<SCRIPT

        $('.GetPast').on('click', function () {
             var id= $(this).data('id');
             $.post("$url", {'id': id, _token:LA.token}, function (data) {
        
                    if (typeof data === 'object') {
                        if (data.code===200) {
                            swal(data.message, '', 'success');
                        } else {
                            swal(data.message, '', 'error');
                        }
                     }
                   $.pjax({container:'#pjax-container', url: "$listPath" });
             });
        
        });

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a title='通过' class='btn btn-xs btn-success fa fa-check GetPast' data-id='{$this->id}'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}