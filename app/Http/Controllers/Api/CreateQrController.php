<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6
 * Time: 21:44
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class CreateQrController extends Controller
{
    private $app_id = "wx0d754fd3f7b4a131";
    private $secret = "bb4e7d915336be487208b13b019dfce2";
    public function getAccessToken()
    {
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->app_id&secret=$this->secret";
        $res = curl_post($url);
        $wx_info = json_decode($res,true);

        return $wx_info;
    }

    /**
     * 获取二维码
     */
    public function getQr(Request $request)
    {
        $data = $request->input();
        if(empty($data["pages"])){
            return Api_error("缺少页面");
        }
        $token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$token['access_token'];

//        houseId: o,
//        shar_id: shar_id,
//        shar_type: shar_type,
//        pages:"pages/detail/detail"

        $requst_data=[
            "scene"=>"shar_id=".$data["shar_id"]."&shar_type=".$data['shar_type']."&id=".$data['houseId'],
            "page"=>$data["pages"],
            "width"=>70
        ];

        $requst_data = json_encode($requst_data);
        $res = curl_post($url,$requst_data);
        $filename = "minQr.jpg";

        if(!is_dir($_SERVER['DOCUMENT_ROOT']."/web/qr/")){
            mkdir($_SERVER['DOCUMENT_ROOT']."/web/qr/",777);
        }

        $file = fopen($_SERVER['DOCUMENT_ROOT']."/web/qr/".$filename,"w");//打开文件准备写入
        fwrite($file,$res);//写入
        fclose($file);//关闭

        if($file){
            return Api_success("生成成功", "https://".$_SERVER['HTTP_HOST']."/web/qr/".$filename);
        }else{
            return Api_error("生成失败");
        }
    }
}