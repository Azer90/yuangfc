<?php


namespace App\Http\Controllers\Api;


use App\Housings;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{

    /**
     * @return false|string
     * 主页热门房源
     */
    public function login(Request $request)
    {
        $code = $request->input("code");
        if(empty($code)){
            return Api_error("缺少参数");
        }
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=wx0d754fd3f7b4a131&secret=bb4e7d915336be487208b13b019dfce2&js_code=$code&grant_type=authorization_code";
        $res = curl_post($url);
        $wx_info = json_decode($res,true);
        $userInfo = User::where("open_id",$wx_info["openid"])->select("name,mobile,avatar,wchat_name,type,sex")->first();
        if(!empty($userInfo)){
            return Api_Success("登陆成功",$userInfo);
        }else{
            return Api_error("登陆失败");
        }

    }
}