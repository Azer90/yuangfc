<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30
 * Time: 21:29
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * 获取请求用户
     */
    public function getUser(Request $request)
    {
            $data = $request->input();

            if(empty($data["uId"])){
                return Api_error("缺少参数");
            }
            $info = User::where("id",$data["uId"])->first(["id","name","mobile","avatar"]);
            return Api_success("获取成功",$info);
    }
}