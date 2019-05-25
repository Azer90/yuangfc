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
            $data = $request->isMethod("post");
            if(empty($data["house_id"])||empty($data["agent_id"])||empty($data["make_id"])){
                return Api_error("缺少参数");
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
}