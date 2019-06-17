<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/17
 * Time: 20:05
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * 获取日志
     */
    public function getLog(Request $request)
    {
        $data =  $request->input();
        if(empty($data["user_id"])){
            return Api_error("缺少参数");
        }

        $res = Log::where(["user_id"=>$data["user_id"],"is_read"=>0])->orderBy("created_at","desc")->first();
        if($res){
           return Api_success("获取成功",$res);
        }else{
            return Api_error("获取失败");
        }
    }

    /**
     * @param Request $request
     * 修改为已读
     */
    public function delete(Request $request)
    {
        $data =  $request->input();
        if(empty($data["user_id"])||empty($data["log_id"])){
            return Api_error("缺少参数");
        }

        $res = Log::where(["user_id"=>$data["user_id"],"id"=>$data["log_id"]])->update(["is_read"=>1,"updated_at"=>date("Y-m-d H:i:s",time())]);
        if($res>0){
            return Api_success("修改成功");
        }else{
            return Api_error("修改失败");
        }

    }


}