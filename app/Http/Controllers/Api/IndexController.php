<?php


namespace App\Http\Controllers\Api;


use App\Banner;
use App\Housings;
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


    /**
     * 首页查询
     */
    public function search(Request $request)
    {
        if($request->isMethod("post")){
            $data=$request->all();
            $rentsale= $data['caseType']=='sale'? 1 :2;
            $res = Housings::from('Housings as h')->Join('floor as f','h.floor_id=f.id')->where(['h.rentsale'=>$rentsale,'f.name'=>$data['keyword']])
                ->get(['h.*']);
            return Api_success("查询成功",$res);
        }
    }
}