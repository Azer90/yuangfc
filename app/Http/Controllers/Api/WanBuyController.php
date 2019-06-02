<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/2
 * Time: 17:18
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\WantBuy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WanBuyController extends Controller
{
    /**
     * 添加求购信息
     */
    public function addWanBuy(Request $request)
    {
        $data = $request->input();

        if(empty($data["u_id"])){
            return Api_error("缺少参数");
        }
        $rules=[
            "real_name"=>"required",
            "mobile"=>[
                "required",
                "regex:/^1[0-9][0-9]|15[012356789]|17[0123456789]|18[0-9]|14[57]/"
            ],
            "area"=>"required",
            "price"=>"required",
            "layout"=>"required",
            "region"=>"required"
        ];
        $messages=[
            "real_name.required"=>'请输入真实姓名',
            "mobile.required"=>"请输入手机号",
            "mobile.regex"=>"手机号格式不正确",
            "area.required"=>"请填写面积",
            "price.required"=>"请填写价格",
            "layout.required"=>"请填写户型",
            "region.required"=>"请选择区域"
        ];
        $validator = Validator::make($data, $rules,$messages);
        if($validator->fails()){
            return Api_error($validator->errors()->getMessages());
        }

        $insert=[
            "user_id"=>$data["u_id"],
            "real_name"=>$data["real_name"],
            "mobile"=>$data["mobile"],
            "area"=>$data["area"],
            "price"=>$data["price"],
            "ament"=>$data["layout"],
            "province_id"=>$data["region"][0],
            "city_id"=>$data["region"][1],
            "district_id"=>$data["region"][2],
        ];

        $res = WantBuy::insert($insert);

        if($res>0){
            return Api_success("提交成功");
        }else{
            return Api_error("提交失败");
        }

    }
}