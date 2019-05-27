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
            $validator = Validator::make($data, ["mobile"=>'regex:/^1[3-9]\d{9}$/',"code"=>"required"], ["mobile"=>"手机号不正确","code.required"=>"请输入验证码"]);
            if($validator->fails()){
                return Api_error($validator->errors()->getMessages());
            }
            $res=0;
            $code = Verification::where("mobile",$data["mobile"])->orderBy("create_time","desc")->value("code");

            if($code!=$data["code"]||empty($code)){
                return Api_error("验证码错误");
            }
            $check = User::where("open_id",$data["openid"])->first();
            if($check){
               return Api_error("该账号已注册");
            }
            $check_mobile = User::where("mobile",$data["mobile"])->first();
            if($check_mobile){
                $insert = [
                    "open_id" => $data["openid"],
                    "wchat_name" => isset($data["userInfo"]["nickName"])?$data["userInfo"]["nickName"]:$data["userInfo"]["userInfo"]["wchat_name"],
                    "avatar" => isset($data["userInfo"]["avatarUrl"])?$data["userInfo"]["avatarUrl"]:$data["userInfo"]["userInfo"]["avatar"],
                    "sex" => isset($data["userInfo"]["gender"])?$data["userInfo"]["gender"]:$data["userInfo"]["userInfo"]["sex"],
                    "mobile"=>$data["mobile"],
                    "email" => "",
                    "updated_at" => date("Y-m-d H:i:s")
                ];
                $res = User::where("mobile" , $data["mobile"])->update($insert);//更新新用户
            }else{
                $insert = [
                    "open_id" => $data["openid"],
                    "wchat_name" => isset($data["userInfo"]["nickName"])?$data["userInfo"]["nickName"]:$data["userInfo"]["userInfo"]["wchat_name"],
                    "avatar" => isset($data["userInfo"]["avatarUrl"])?$data["userInfo"]["avatarUrl"]:$data["userInfo"]["userInfo"]["avatar"],
                    "sex" => isset($data["userInfo"]["gender"])?$data["userInfo"]["gender"]:$data["userInfo"]["userInfo"]["sex"],
                    "mobile"=>$data["mobile"],
                    "email" => "",
                    "created_at" => date("Y-m-d H:i:s")
                ];
                $res = User::insert($insert);//添加新用户
            }


            $userInfo = User::where("open_id",$data["openid"])->first();
           if($res > 0){
               Verification::where("mobile",$data["mobile"])->delete();
               $reg_res = [
                   "openid"=>$data["openid"],
                   "userInfo"=>$userInfo,
                   "isRegister"=>1
               ];
               return Api_success("注册成功",$reg_res);
           }else{
               return Api_error("注册失败");
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
            $validator = Validator::make($data, ["mobile"=>'regex:/^1[3-9]\d{9}$/'], ["mobile"=>"手机号不正确"]);
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
                    "code"=>$str,
                    "create_time"=>date("Y-m-d H:i:s",time())
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

    /**
     * 更换手机号
     */
    public function replaceMobile(Request $request)
    {
        if($request->isMethod("post")){
            $data = $request->input();
            if(empty($data["openId"])){
                    return Api_error("缺少参数");
            }
            $validator = Validator::make($data, ["mobile"=>'regex:/^1[3-9]\d{9}$/',"code"=>"required"], ["mobile"=>"手机号不正确","code.required"=>"请输入验证码"]);
            if($validator->fails()){
                return Api_error($validator->errors()->getMessages());
            }
            $user = User::where("open_id",$data["openId"])->first();
            if(empty($user)){
                return Api_error("用户不存在");
            }
            $code = Verification::where("mobile",$data["mobile"])->value("code");

            if($code != $data["code"]){
                return Api_error("验证码不正确");
            }

            $res = User::where("open_id",$data["openId"])->update(["mobile"=>$data["mobile"],"updated_at"=>date("Y-m-d H:i:s",time())]);
            if($res>0){
                return Api_success("号码修改成功");
            }else{
                return Api_error("修改失败");
            }

        }
    }
}
//14	18782495926	129985	2019-05-27 20:01:14