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
            "ID_card" => $data["id_card"],
            "mobile" => $data["phoneNum"],
            "province_id"=>$data["region"][0],
            "city_id"=>$data["region"][1],
            "district_id"=>$data["region"][2],
        ];

        $rules=[
            "real_name"=>"required",
            "ID_card"=>"regex:/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx])|([1−9]\d5\d2((0[1−9])|(10|11|12))(([0−2][1−9])|10|20|30|31)\d2[0−9Xx])/",
        ];
        $messages=[
            "real_name.required"=>'请输入真实姓名',
            "ID_card"=>"身份证格式不正确",
        ];
        $validator = Validator::make($data, $rules,$messages);
        if($validator->fails()){
            return Api_error( $validator->errors()->getMessages());
        }
        $res = AgentCheck::insert($insert_data);
        if($res>0){
            return Api_success("提交成功");
        }else{
            return Api_error("提交失败");
        }
    }
}