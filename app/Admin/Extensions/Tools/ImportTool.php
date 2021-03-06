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
        $dowload=route("export");
        return <<<EOT
        
        $(".close").on("click",function(){
              $(".speed").css("display","none");  
              $(".success_count").text(0);
              $(".error_count").text(0);
              $(".fail_info").text("");
              $(".state").text("正在导入...");
             $.pjax({container:'#pjax-container', url: '$url' });
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
                                $(".file_name").text(file.name);
                           }, 
                           success: function (result) {
                               if(result.code==1){
                                     var res_data = result.data;
                                     $(".state").text(result.message).css("color","green");
                                     $(".success_count").text(res_data.success_cont);
                                     $(".error_count").text(res_data.error_cont);
                                     if(res_data.error_cont>0){
                                      $(".fail_info").html('<a target="_blank" href="$dowload">导出失败数据</a>'); 
                                     }
                               }
                              
//                              $.pjax({container:'#pjax-container', url: '$url' });
                           },
                           error:function(){
                                    $(".state").text("导入失败")
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

        $url = $this->get_host()."/template/房源导入模板.xls";

        return view('admin.tools.import',compact('url'));
    }
    function get_host(){
        $scheme = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
        $url = $scheme.$_SERVER['HTTP_HOST'];
        return $url;
    }
}