<?php


namespace App\Http\Controllers\Api;


use App\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * 获取banner
     */
    public function getBanner(Request $request)
    {
        if($request->isMethod("post")){
            $res = Banner::where("state",1)
                ->select("title","src")
                ->orderBy("sort")
                ->get();
            return Api_success("获取成功",$res);
        }
    }
}