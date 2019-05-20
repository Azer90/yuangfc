<?php


namespace App\Http\Controllers\Api;


use App\Circle;
use App\District;
use App\Floor;
use App\Housings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseController extends Controller
{
    public function hotHouse(Request $request)
    {
        if($request->isMethod("post")){
            $city_code = $request->input("city_code");
            //热门二手房出
            $two_hand_w = ["is_display"=>1,"setup"=>1,"type"=>2,"rentsale"=>1,"city_id"=>$city_code];
            $two_hand = $this->getHot($two_hand_w);
            //热门租房
            $renting_w = ["is_display"=>1,"setup"=>1,"rentsale"=>2,"city_id"=>$city_code];
            $renting = $this->getHot($renting_w);
            //热门新房
            $new_house_w = ["is_display"=>1,"setup"=>1,"type"=>1,"rentsale"=>1,"city_id"=>$city_code];
            $new_house = $this->getHot($new_house_w);

            $res = [
                "two_hand" => $two_hand,
                "renting" => $renting,
                "new_house" => $new_house,
            ];
            return Api_success("热门获取成功",$res);
        }
    }

    /**
     * @param $w
     * 热门查询语句
     */
    public function getHot($w)
    {
        $res =  Housings::where($w)
            ->select("id","title","type","purpose","room","hall","toilet","floor_id","district_id","circle_id","area","direction","price",DB::raw('price/area AS unit_price'),"pictures")
            ->limit(3)
            ->orderBy("created_at","desc")
            ->get();
        foreach ($res as $item){
            $item["thumd"] = "https://".config("filesystems.disks.oss.bucket").".".config("filesystems.disks.oss.endpoint")."/".$item["pictures"][0]."?x-oss-process=image/resize,w_500";
            if($w["rentsale"]!=2){
                $item["unit_price"] = $item["unit_price"]*10000;
            }
            //小区
            $floors = $item->floors;
            if($floors){
                $item["floor_name"] =$floors->name;
            }else{
                $item["floor_name"] = "";
            }

            //区
            $district = $item->district;
             if($district){
                 $item["district_name"] =$district->name;
             }else{
                 $item["district_name"] = "";
             }

            $circle = $item->circle;
            if($circle){
                $item["circle_name"] =$circle->name;
            }else{
                $item["circle_name"] = "";
            }

            unset($item["pictures"]);
            unset($item["floors"]);
        }
        return $res;
    }

    /**
     * 获取区域列表
     */
    public function getAreaList(Request $request)
    {
        if($request->isMethod("post")) {
            $city_code= $request->input("city_code");
            if(empty($city_code)){
                return Api_error("缺少参数");
            }
            $area = District::where("parent_id",$city_code)->select("id","name")->get();
            return Api_success("区域获取成功",$area);
        }
    }
    /**
     * 获取指定商圈列表
     */
    public function getCircleList(Request $request)
    {
        if($request->isMethod("post")) {
            $area_code= $request->input("area_code");
            if(empty($area_code)){
                return Api_error("缺少参数");
            }
            $circle = Circle::where("district_id",$area_code)->select("id","name")->get();
            return Api_success("区域获取成功",$circle);
        }
    }
    
    /**
     * list_type 列表类型 1：二手房  2：新房  3：租房
     * 房源列表
     */
    public function housList(Request $request)
    {
        if($request->isMethod("post")){
            $search_data = $request->input();
            if(empty($search_data["list_type"])||empty($search_data["city_code"])){
                return Api_error("缺少参数");
            }

            switch ($search_data["list_type"]){
                case 1 : $where = ["is_display"=>1,"type"=>2,"rentsale"=>1,"city_id"=>$search_data["city_code"]];//二手房
                    break;
                case 2 : $where = ["is_display"=>1,"type"=>1,"rentsale"=>1,"city_id"=>$search_data["city_code"]];//新房
                    break;
                case 3 : $where = ["is_display"=>1,"rentsale"=>2,"city_id"=>$search_data["city_code"]];//租房
                    break;
                default:$where=[];
            }

//            {area:0
//            circle_id:0
//            city_code:"510100"
//            list_type:"1"
//            maxArea:0
//            maxPrice:0
//            minArea:0
//            minPrice:0
//            page:1
//            price:0
//            purpose:0
//            region_id:0
//            rentsale:1
//            room:0}

            if(isset($search_data["rentsale"])&&$search_data["rentsale"]){
                $where[]=["rentsale","=",$search_data["rentsale"]];
            }

            if(isset($search_data["circle_id"])&&$search_data["circle_id"]){

                $where[]=["circle_id","=",$search_data["circle_id"]];
            }
            if(isset($search_data["min_price"])&&($search_data["min_price"]||$search_data["max_price"])){

                $where[]=["price",">=",$search_data["min_price"]];
                $where[]=["price","<=",$search_data["max_price"]];
            }
            if(isset($search_data["min_area"])&&$search_data["min_area"]&&$search_data["max_area"]){

                $where[]=["area",">=",$search_data["min_area"]];
                $where[]=["area","<=",$search_data["max_area"]];
            }
            if(isset($search_data["room"])&&$search_data["room"]){
                $where[] = ["room","=",$search_data["room"]];
            }
            if(isset($search_data["purpose"])&&$search_data["purpose"]){
                $where[] = ["purpose","=",$search_data["purpose"]];
            }

            $res = Housings::where($where)
                ->orderByDesc("created_at")
                ->paginate(10,["id","title","type","purpose","rentsale","room","hall","toilet","floor_id","district_id","circle_id","area","direction","price",DB::raw('price/area AS unit_price'),"pictures"]);

            foreach ($res as $item){
                $item["thumd"] = "https://".config("filesystems.disks.oss.bucket").".".config("filesystems.disks.oss.endpoint")."/".$item["pictures"][0]."?x-oss-process=image/resize,w_500";
                if($search_data["list_type"]!=3){
                    $item["unit_price"] = $item["unit_price"]*10000;
                    $item["price_unit"] = "万";
                }else{
                    $item["price_unit"] = "元/月";
                }

                //小区
                $floors = $item->floors;
                if($floors){
                    $item["floor_name"] =$floors->name;
                }else{
                    $item["floor_name"] = "";
                }

                //区
                $district = $item->district;
                if($district){
                    $item["district_name"] =$district->name;
                }else{
                    $item["district_name"] = "";
                }

                $circle = $item->circle;
                if($circle){
                    $item["circle_name"] =$circle->name;
                }else{
                    $item["circle_name"] = "";
                }

                unset($item["pictures"]);
                unset($item["floors"]);
                unset($item["district"]);
                unset($item["circle"]);
            }
            return Api_success("房源信息获取成功",$res);
        }
    }

    /**
     * 搜索楼盘
     */
    public function getBuild(Request $request)
    {
        if($request->isMethod("post")){
              $data = $request->input();
                  if(empty($data["city_code"])){
                    return Api_error("缺少参数");
              }

              $res = Floor::where("name","like","%".$data["buildInputText"]."%")
                  ->where("city_id","=",$data["city_code"])
                  ->paginate(40,["id","name"],"",$data["inputPage"]);

              foreach ($res as $v){
                  $res["district"] = $v->district;
              }
dump($res);
        }
    }
}