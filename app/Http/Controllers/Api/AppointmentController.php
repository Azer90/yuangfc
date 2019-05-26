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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        if(empty($data["make_id"])){
           return Api_error("缺少参数");
        }
        $res =MakeOrder::where(["make_id"=>$data["make_id"],"state"=>0,"add_schedule"=>0])
            ->paginate(10,["id","house_id","agent_id","make_id"],"",isset($data["page"])?$data["page"]:1);

        foreach ($res as $val){
            $val["house"] = $val->housings;

            //小区
            $floors = $val["house"]->floors;
            if ($floors) {
                $val["house"]["floor_name"] = $floors->name;
            } else {
                $val["house"]["floor_name"] = "";
            }
            //商圈
             $circle = $val["house"]->circle;
            if ($circle) {
                $val["house"]["circle_name"] = $circle->name;
            } else {
                $val["house"]["circle_name"] = "";
            }

            if($val["house"]["rentsale"]==1){
                $val["house"]["unit_price"] =  round(($val["house"]["price"]/$val["house"]["area"])*10000);
            }
            $val["house"]["thumd"] = "";
            if($val["house"]["pictures"]){
                $val["house"]["thumd"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $val["house"]["pictures"][0] . "?x-oss-process=image/resize,w_500";

            }

            unset($val["housings"]);
            unset($val["house"]["circle"]);
            unset($val["house"]["floors"]);
            unset($val["house"]["pictures"]);
        }
        return Api_success("预约列表获取成功",$res);
    }

    /**
     * 加入日程
     */

//note:"sdfdsa"
//rDayInfo:"全天"
//rId:"4,5"
//rName:"sdfsd"
//rPhone:"18782495926"
//rTime:"2019-5-26"
//wxApp:1
    public function addSchedule(Request $request)
    {
        $data = $request->input();

        if(empty($data["rId"])){
            return Api_error("缺少参数");
        }
        $rule =["rPhone"=>'regex:/^1[3-9]\d{9}$/',"rName"=>"required","rTime"=>"required"];
        $message =  ["rPhone"=>"手机号不正确","rName.required"=>"请输入姓名","rTime.required"=>"请选择时间"];
        $validator = Validator::make($data,$rule ,$message);
        if($validator->fails()){
            return Api_error($validator->errors()->getMessages());
        }
        switch ($data["rDayInfo"]){
            case "全天":$data["rDayInfo"]=0;
                break;
            case "上午":$data["rDayInfo"]=1;
                break;
            case "下午":$data["rDayInfo"]=2;
                break;
            case "晚上":$data["rDayInfo"]=3;
                break;
        }
        $update_data = [
            "make_name"=>$data["rName"],
            "make_mobile"=>$data["rPhone"],
            "time"=>$data["rTime"],
            "time_slot"=>$data["rDayInfo"],
            "add_schedule"=>1,
            "remark"=>$data["note"],
            "updated_at"=>date("Y-m-d H:i:s",time())
        ];

        $data["rId"] = explode(",",$data["rId"]);

        $res = MakeOrder::whereIn("id",$data["rId"])->update($update_data);

        if($res>0){
            return Api_success("预约成功");
        }else{
            return Api_error("预约失败");
        }

    }






    /**
     * 获取日程列表
     */
    public function getSchedule(Request $request)
    {
        $data = $request->input();
        if(empty($data["make_id"])){
            return Api_error("缺少参数");
        }
        $res =MakeOrder::where(["make_id"=>$data["make_id"],"add_schedule"=>1])
            ->paginate(10,["id","house_id","agent_id","make_id","time","time_slot"],"",isset($data["page"])?$data["page"]:1);

        foreach ($res as $val){
            $val["house"] = $val->housings;

            //小区
            $floors = $val["house"]->floors;
            if ($floors) {
                $val["house"]["floor_name"] = $floors->name;
            } else {
                $val["house"]["floor_name"] = "";
            }
            //商圈
            $circle = $val["house"]->circle;
            if ($circle) {
                $val["house"]["circle_name"] = $circle->name;
            } else {
                $val["house"]["circle_name"] = "";
            }

            //经纪人
            $agen = $val["house"]->user;
            if ($agen) {
                $val["house"]["circle_name"] = $agen->name;
            } else {
                $val["house"]["circle_name"] = "";
            }

            if($val["house"]["rentsale"]==1){
                $val["house"]["unit_price"] =  round(($val["house"]["price"]/$val["house"]["area"])*10000);
            }
            $val["house"]["thumd"] = "";
            if($val["house"]["pictures"]){
                $val["house"]["thumd"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $val["house"]["pictures"][0] . "?x-oss-process=image/resize,w_500";

            }

            unset($val["housings"]);
            unset($val["house"]["circle"]);
            unset($val["house"]["floors"]);
            unset($val["house"]["pictures"]);
        }
        return Api_success("预约列表获取成功",$res);
    }
}