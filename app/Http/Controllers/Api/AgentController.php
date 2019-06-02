<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/29
 * Time: 19:24
 */

namespace App\Http\Controllers\Api;


use App\AgentCheck;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    /**
     * 添加经纪人
     */
    public function addAgent(Request $request)
    {
        $data = $request->input();
        if(empty($data["id"])){
            return Api_error("缺少参数");
        }
        if(empty($data["region"])){
            return Api_error("请选择省市区");
        }

        $insert_data = [
            "user_id" => $data["id"],
            "real_name" => $data["name"],
            "id_card" => $data["id_card"],
            "mobile" => $data["phoneNum"],
            "province_id"=>$data["region"][0],
            "city_id"=>$data["region"][1],
            "district_id"=>$data["region"][2],
            "created_at"=>date("Y-m-d H:i:s",time())
        ];

        $rules=[
            "real_name"=>"required",
            "id_card"=>[
                "required",
                "regex:/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9xX]$/"
            ],
            "mobile"=>[
                "required",
                "regex:/^1[0-9][0-9]|15[012356789]|17[0123456789]|18[0-9]|14[57]/"
            ]
        ];
        $messages=[
            "real_name.required"=>'请输入真实姓名',
            "id_card.required"=>"请输入身份证号",
            "id_card.regex"=>"身份证格式不正确",
            "mobile.required"=>"请输入手机号",
            "mobile.regex"=>"手机号格式不正确",
        ];
        $validator = Validator::make($insert_data, $rules,$messages);
        if($validator->fails()){
            return Api_error($validator->errors()->getMessages());
        }

        $check = AgentCheck::where(["user_id"=>$data["id"],"state"=>0])->first();
        if(!empty($check)){
            return Api_error("信息正在审核中，请不要重复提交");
        }

        $res = AgentCheck::insert($insert_data);
        if($res>0){
            return Api_success("提交成功");
        }else{
            return Api_error("提交失败");
        }
    }


    /**
     * 获取用户申请经纪人状态
     */
    public function getState(Request $request)
    {
        $data = $request->input();
        if(empty($data["id"])){
            return Api_error("缺少参数");
        }

        $res = AgentCheck::where("user_id",$data["id"])->orderBy("created_at","desc")->value("state");

        if(is_null($res)){
            $state = 3;
        }else{
            $state = $res;
        }
        return Api_success("获取成功",$state);
    }
}