<?php


namespace App\Http\Controllers\Api;


use App\Banner;
use App\Housings;
use App\Http\Controllers\Controller;
use App\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                ->get()->each(function($item){
                    $item["src"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $item["src"] . "?x-oss-process=image/resize,w_500";
                });
            return Api_success("获取成功",$res);
        }
    }


    /**
     * 首页查询
     */
    public function search(Request $request)
    {
        if($request->isMethod("post")){
            $search_data=$request->all();
//            $rentsale= $search_data['caseType']=='sale'? 1 :2;
//            'h.rentsale'=>$rentsale,
//            $where = ['f.name'=>$search_data['keyword']];
            $where = [];

            if (isset($search_data["buildInputText"]) && $search_data["buildInputText"]){
                $where[] = ["f.name", "=", $search_data["buildInputText"]];
            }

            if (isset($search_data["city_code"]) && $search_data["city_code"]){
                $where[] = ["h.city_id", "=", $search_data["city_code"]];
            }

            if (isset($search_data["rentsale"]) && $search_data["rentsale"]) {
                $where["rentsale"] =  $search_data["rentsale"];
            }

            if ($search_data["minPrice"] || $search_data["maxPrice"]) {
                $where[] = ["price", ">=", $search_data["minPrice"]];
                $where[] = ["price", "<=", $search_data["maxPrice"]];
            }else{
                if(!empty($search_data["price"])){
                    //出售价格
                    if($search_data["rentsale"]==1){
                        switch ($search_data["price"]){
                            case 1:
                                $where[] = ["price","<=",30];
                                break;
                            case 2:
                                $where[] = ["price",">=",30];
                                $where[] = ["price","<=",50];
                                break;
                            case 3:
                                $where[] = ["price",">=",50];
                                $where[] = ["price","<=",80];
                                break;
                            case 4:
                                $where[] = ["price",">=",80];
                                $where[] = ["price","<=",100];
                                break;
                            case 5:
                                $where[] = ["price",">=",100];
                                $where[] = ["price","<=",120];
                                break;
                            case 6:
                                $where[] = ["price",">=",150];
                                $where[] = ["price","<=",200];
                                break;
                            case 7:
                                $where[] = ["price",">=",200];
                                break;
                        }

                    }

                    if($search_data["rentsale"]==2){
                        switch ($search_data["price"]){
                            case 1:
                                $where[] = ["price","<=",500];
                                break;
                            case 2:
                                $where[] = ["price",">=",500];
                                $where[] = ["price","<=",800];
                                break;
                            case 3:
                                $where[] = ["price",">=",800];
                                $where[] = ["price","<=",1000];
                                break;
                            case 4:
                                $where[] = ["price",">=",1000];
                                $where[] = ["price","<=",1200];
                                break;
                            case 5:
                                $where[] = ["price",">=",1200];
                                $where[] = ["price","<=",1500];
                                break;
                            case 6:
                                $where[] = ["price",">=",1500];
                                $where[] = ["price","<=",2000];
                                break;
                            case 7:
                                $where[] = ["price",">=",2000];
                                break;
                        }
                    }
                }
            }


            if ($search_data["minArea"] || $search_data["maxArea"]) {

                $where[] = ["area", ">=", $search_data["minArea"]];
                $where[] = ["area", "<=", $search_data["maxArea"]];
            }else{
                if(!empty($search_data["area"])){
                    //面积
                    $area_list = explode(":",$search_data["area"]);
                    $where[] = ["area", ">=", $area_list[0]];
                    $where[] = ["area", "<=", $area_list[1]];
                }
            }

            if (isset($search_data["room"]) && $search_data["room"]) {
                $where[] = ["room", "=", $search_data["room"]];
            }

            $res = Housings::from('housings as h')
                ->Leftjoin('floor as f','h.floor_id','=','f.id')
                ->Leftjoin('district as d','h.district_id','=','d.id')
                ->Leftjoin('circle as c','h.circle_id','=','c.id')
                ->where($where)
                ->paginate(10,["h.id as id", "title", "type", "purpose", "rentsale", "room", "hall", "toilet", "area", "direction", "price", DB::raw('price/area AS unit_price'), "pictures","tags","f.name as floor_name","d.name as district_name","c.name as circle_name"]);


            $all_tag = Tags::get(["id","name"])->toArray();
            foreach ($res as $item) {
                $item["thumd"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $item["pictures"][0] . "?x-oss-process=image/resize,w_500";
//                $item["thumd"] = Storage::disk(config('admin.upload.disk'))->url($item["pictures"][0])."?x-oss-process=image/resize,w_500";
                if ($item["rentsale"] == 1) {
                    $item["unit_price"] = round($item["unit_price"] * 10000);
                    $item["price_unit"] = "万";
                } else {
                    $item["price_unit"] = "元/月";
                }

                //标签
                $tag = [];
                if($all_tag){
                    foreach ($all_tag as $v){
                        if(in_array($v["id"],$item["tags"])){
                            $tag[] = $v["name"];
                        };
                    }
                }
                $item["tag"] =$tag;
            }




            return Api_success("查询成功",$res);
        }
    }
}