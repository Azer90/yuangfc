<?php


namespace App\Http\Controllers\Api;


use App\Housings;
use App\Http\Controllers\Controller;
use App\Libs\Sms;
use App\User;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            $res["isRegister"] = 1;
            return Api_success("登陆成功",$res);
        }else{
            $res["isRegister"] = 0;
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

//            $data["userInfo"] = json_decode($data["userInfo"],true);

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

            $userInfo = User::where("open_id",$data["openid"])->first();
           if($res > 0){
               return Api_success("注册成功",$userInfo);
            }else{
               return Api_error("注册失败",$userInfo);
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


    /**
     * 获取验证码
     */
    public function getCode(Request $request)
    {
        $sms = new Sms();
        if($request->isMethod("post")){
            $data = $request->input();
            if(empty($data["mobile"])){
                return Api_error("请输入电话号码");
            }
            $validator = Validator::make($data, ["mobile"=>'regex:/^1[3-9]\d{9}$/',], ["mobile"=>"手机号不正确"]);
            if($validator->fails()){
              return Api_error($validator->errors()->getMessages());
            }
            $str = '';
            for ($i=0;$i<6;$i++){
                $str.=mt_rand(0,9);
            }
            $sms_res = $sms->send($data["mobile"],$str);
            if($sms_res=="success"){
                $insert_data = [
                    "mobile"=>$data["mobile"],
                    "code"=>$str
                ];
                $res = Verification::insert($insert_data);
                if($res>0){
                    return Api_success("验证码获取成功");
                }
            }else{
                return Api_error("验证码获取失败");
            }
        }
    }
}