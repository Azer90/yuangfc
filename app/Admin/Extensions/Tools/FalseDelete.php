<?php
namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;

class FalseDelete
{
    protected $id;
    protected $url;
    public function __construct($id,$url)
    {
        $this->id = $id;
        $this->url = $url;
    }

    protected function script()
    {
        $trans = [
            'delete_confirm' => trans('admin.delete_confirm'),
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
        ];
        return <<<SCRIPT

$('.grid-false-delect').on('click', function () {
     
      var id=$(this).data('id');
   
       swal({
        title: "{$trans['delete_confirm']}",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "{$trans['confirm']}",
        showLoaderOnConfirm: true,
        cancelButtonText: "{$trans['cancel']}",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '{$this->url}',
                    data: {
                     
                        _token:LA.token,
                        id: id
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
       
        if (typeof data === 'object') {
            if (data.code===200) {
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

        return "<a class='grid-false-delect' style='margin-left:10px ' href='javascript:void(0);' data-id='{$this->id}'><i class='fa fa-trash'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}