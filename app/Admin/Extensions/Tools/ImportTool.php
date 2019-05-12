<?php
namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class ImportTool extends AbstractTool
{
    private $import_url;
    public function __construct($url)
    {
        $this->import_url = $url;
    }
    protected function script()
    {
        $url = Request::getRequestUri();

        return <<<EOT
        
        $(".close").on("click",function(){
              $(".speed").css("display","none");  
        })
    $('input:file.import').change(function () {
           var file= this.files[0];
           var formData = new FormData();
           formData.append("file",file);
           formData.append("_token",LA.token);
          swal({
              title: "确认导入?",
              text: file.name,
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "确认",
              cancelButtonText: "取消",
              closeOnConfirm: false,
              closeOnCancel: false,
              preConfirm:function(inputValue){
              if(inputValue){
                       $.ajax({
                           url: ' $this->import_url',
                           data: formData,
                           type: 'Post',
                           dataType: "json",
                           cache: false,//上传文件无需缓存
                           processData: false,//用于对data参数进行序列化处理 这里必须false
                           contentType: false, //必须
                           beforeSend:function(XMLHttpRequest){
                                $(".speed").css("display","block");
                           }, 
                           success: function (result) {
                           if(result.code==1){
                             $(".state").text("导入成功").css("color","green");
                           }
                              
//                              $.pjax({container:'#pjax-container', url: '$url' });
                           }
                       })
                   }           
               }
            })
    });

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        return view('admin.tools.import');
    }
}