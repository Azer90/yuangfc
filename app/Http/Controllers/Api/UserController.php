<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30
 * Time: 21:29
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * 获取请求用户
     */
    public function getUser(Request $request)
    {
            $data = $request->input();

            if(empty($data["uId"])){
                return Api_error("缺少参数");
            }
            $info = User::where("id",$data["uId"])->first(["id","name","mobile","avatar"]);
            if(!empty($info)){
                return Api_success("获取成功",$info);
            }else{
                return Api_error("获取失败");
            }

    }


    /**
     * 获取用户信息
     */
    public function getUserInfo(Request $request)
    {
        $data = $request->input();

        if(empty($data["openId"])){
            return Api_error("缺少openId");
        }

        $userInfo = User::where("open_id",$data["openId"])->select("id","name","mobile","avatar","wchat_name","type","sex")->first();
        $res = [
            "openid"=>$data["openId"],
            "userInfo"=>$userInfo
        ];
        if(!empty($userInfo)){
            $res["isRegister"] = 1;
            return Api_success("获取成功",$res);
        }else{
            $res["isRegister"] = 0;
            return Api_error("获取失败",$res);
        }
    }
}