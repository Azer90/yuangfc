<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/25
 * Time: 9:52
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Recommend;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * 关注/收藏操作
     */
    public function followHandle(Request $request)
    {
        if($request->isMethod("post")){
            $data = $request->input();
            if(empty($data["user_id"])||empty($data["rec_id"]||empty($data["type"]))){
                return Api_error("缺少参数");
            }
            $recommebd = Recommend::where(["user_id"=>$data["user_id"],"rec_id"=>$data["rec_id"],"type"=>$data["type"]])->first();
            if(empty($recommebd)){
                $res = Recommend::insert(["user_id"=>$data["user_id"],"rec_id"=>$data["rec_id"],"type"=>$data["type"]]);
                if($res>0){
                    return Api_success("关注成功");
                }
            }else{
                $res = Recommend::where(["user_id"=>$data["user_id"],"rec_id"=>$data["rec_id"],"type"=>$data["type"]])->delete();
                if($res>0){
                    return Api_success("取消成功");
                }
            }
        }
    }
}