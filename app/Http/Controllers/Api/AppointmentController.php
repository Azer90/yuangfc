<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/25
 * Time: 23:07
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\MakeOrder;
use Illuminate\Http\Request;


class AppointmentController extends Controller
{
    /**
     * 预约看房
     */
    public function appoint(Request $request)
    {

        if($request->isMethod("post")){
            $data = $request->input();

            if(empty($data["house_id"])||empty($data["agent_id"])||empty($data["make_id"])){
                return Api_error("缺少参数");
            }
            $exist =MakeOrder::where(["house_id"=>$data["house_id"],"agent_id"=>$data["agent_id"],"make_id"=>$data["make_id"]])->count();

            if($exist){
                return Api_error("该房源已预约");
            }

            $data["created_at"] = date("Y-m-d H:i:s",time());
            $res = MakeOrder::insert($data);
            if($res>0){
                return Api_success("预约成功");
            }else{
                return Api_error("预约失败");
            }
        }
    }

    /**
     * 获取预约列表
     */
    public function getAppointList(Request $request)
    {
        $data = $request->input();
        if(empty($data["user_id"])){
           return Api_error("缺少参数");
        }

        MakeOrder::where("make_id",$data["user_id"])->paginate(10,["id,house_id,agent_id,make_id"],isset($data["page"])?isset($data["page"]):1);
    }
}