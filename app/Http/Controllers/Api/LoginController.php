<?php


namespace App\Http\Controllers\Api;


use App\Housings;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{

    public function getOpenId(Request $request)
    {
        $code = $request->input("code");
        if(empty($code)){
            return Api_error("缺少参数");
        }
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=wx0d754fd3f7b4a131&secret=bb4e7d915336be487208b13b019dfce2&js_code=$code&grant_type=authorization_code";
        $res = curl_post($url);
        $wx_info = json_decode($res,true);
        if(!$wx_info["errcode"]){
            return Api_success("获取成功",$wx_info);
        }else{
            return Api_error("获取失败",$wx_info);
        }
    }

    /**
     * @return false|string
     * 登陆
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
        $userInfo = User::where("open_id",$wx_info["openid"])->select("id","name","mobile","avatar","wchat_name","type","sex")->first();
        $res = [
            "openid"=>$wx_info["openid"],
            "userInfo"=>$userInfo
        ];
        if(!empty($userInfo)){
            return Api_success("登陆成功",$res);
        }else{
            return Api_error("登陆失败",$res);
        }
    }

    /**
     * 注册
     */
    public function register(Request $request)
    {
        $data = $request->input();

        if($request->isMethod("post")){


            if(empty($data["openid"])||empty($data["userInfo"])){
                return Api_error("缺少参数");
            }

            $res=0;
            $info = User::where("open_id",$data["openid"])->first();//查询当前用户是否存在

            if(empty($info)){
                $insert = [
                    "open_id" => $data["openid"],
                    "wchat_name" => $data["userInfo"]["nickName"],
                    "avatar" => $data["userInfo"]["avatarUrl"],
                    "sex" => $data["userInfo"]["gender"],
                    "email" => "",
                    "created_at" => date("Y-m-d H:i:s")
                ];
                $res = User::insert($insert);//添加新用户
            }else{
                if($data["userInfo"]["nickName"]!=$info["wchat_name"]||$data["userInfo"]["avatarUrl"]!=$info["avatar"]|| $data["userInfo"]["gender"]!=$info["sex"]){
                    $insert = [
                        "open_id" => $data["openid"],
                        "wchat_name" => $data["userInfo"]["nickName"],
                        "avatar" => $data["userInfo"]["avatarUrl"],
                        "sex" => $data["userInfo"]["gender"],
                        "updated_at" => date("Y-m-d H:i:s")
                    ];
                    //信息变化，修改用户信息
                    $res = User::where("open_id",$data["openid"])->update($insert);
                }
            }
           if($res > 0){
               return Api_success("注册成功",$res);
            }else{
               return Api_error("注册失败",$res);
           }
        }
    }


    /**
     * 注册检测
     */
    public function checkRegister(Request $request)
    {
        if($request->isMethod("post")){
            $data = $request->input();
            if(empty($data["openId"])){
                return Api_error("缺少参数");
            }
            $res = User::where("open_id",$data["openId"])->value("mobile");
            if($res){
                return Api_success("获取成功",$res);
            }else{
                return Api_error("获取失败");
            }
        }
    }
}