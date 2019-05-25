<?php


namespace App\Http\Controllers\Api;


use App\Circle;
use App\District;
use App\Floor;
use App\Housings;
use App\Http\Controllers\Controller;
use App\MakeOrder;
use App\Recommend;
use App\Tags;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HouseController extends Controller
{
    public function hotHouse(Request $request)
    {
        if ($request->isMethod("post")) {
            $city_code = $request->input("city_code");
            //热门二手房出
            $two_hand_w = ["is_display" => 1, "setup" => 1, "type" => 2, "rentsale" => 1, "city_id" => $city_code];
            $two_hand = $this->getHot($two_hand_w);
            //热门租房
            $renting_w = ["is_display" => 1, "setup" => 1, "rentsale" => 2, "city_id" => $city_code];
            $renting = $this->getHot($renting_w);
            //热门新房
            $new_house_w = ["is_display" => 1, "setup" => 1, "type" => 1, "rentsale" => 1, "city_id" => $city_code];
            $new_house = $this->getHot($new_house_w);

            $res = [
                "two_hand" => $two_hand,
                "renting" => $renting,
                "new_house" => $new_house,
            ];
            return Api_success("热门获取成功", $res);
        }
    }

    /**
     * @param $w
     * 热门查询语句
     */
    public function getHot($w)
    {
        $res = Housings::where($w)
            ->select("id", "title", "type", "purpose", "room", "hall", "toilet", "floor_id", "district_id", "circle_id", "area", "direction", "price", DB::raw('price/area AS unit_price'), "pictures")
            ->limit(3)
            ->orderBy("created_at", "desc")
            ->get();
        $all_tag = Tags::get(["id","name"])->toArray();
        foreach ($res as $item) {
            $item["thumd"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $item["pictures"][0] . "?x-oss-process=image/resize,w_500";
            if ($w["rentsale"] != 2) {
                $item["unit_price"] = round($item["unit_price"] * 10000);
            }

            //标签
            $tag = [];
            foreach ($all_tag as $v){
                if(in_array($v["id"],$item["tags"])){
                    $tag[] = $v["name"];
                };
            }
            $item["tag"] =$tag;

            //小区
            $floors = $item->floors;
            if ($floors) {
                $item["floor_name"] = $floors->name;
            } else {
                $item["floor_name"] = "";
            }

            //区
            $district = $item->district;
            if ($district) {
                $item["district_name"] = $district->name;
            } else {
                $item["district_name"] = "";
            }

            $circle = $item->circle;
            if ($circle) {
                $item["circle_name"] = $circle->name;
            } else {
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
        if ($request->isMethod("post")) {
            $city_code = $request->input("city_code");
            if (empty($city_code)) {
                return Api_error("缺少参数");
            }
            $area = District::where("parent_id", $city_code)->select("id", "name")->get();
            return Api_success("区域获取成功", $area);
        }
    }

    /**
     * 获取指定商圈列表
     */
    public function getCircleList(Request $request)
    {
        if ($request->isMethod("post")) {
            $area_code = $request->input("area_code");
            if (empty($area_code)) {
                return Api_error("缺少参数");
            }
            $circle = Circle::where("district_id", $area_code)->select("id", "name")->get();
            return Api_success("区域获取成功", $circle);
        }
    }

    /**
     * list_type 列表类型 1：二手房  2：新房  3：租房
     * 房源列表
     */
    public function housList(Request $request)
    {
        if ($request->isMethod("post")) {
            $search_data = $request->input();
            if (empty($search_data["list_type"])) {
                return Api_error("缺少参数");
            }

            switch ($search_data["list_type"]) {
                case 1 :
                    $where = ["is_display" => 1, "type" => 2, "rentsale" => 1];//二手房
                    break;
                case 2 :
                    $where = ["is_display" => 1, "type" => 1, "rentsale" => 1];//新房
                    break;
                case 3 :
                    $where = ["is_display" => 1, "rentsale" => 2];//租房
                    break;
                default:
                    $where = [];
            }

            if (isset($search_data["city_code"]) && $search_data["city_code"]){
                 $where[] = ["city_id", "=", $search_data["city_code"]];
            }

            if (isset($search_data["rentsale"]) && $search_data["rentsale"]) {
                $where["rentsale"] =  $search_data["rentsale"];
            }

            if (isset($search_data["region_id"]) && $search_data["region_id"]) {
                $where[] = ["district_id", "=", $search_data["region_id"]];
            }

            if (isset($search_data["circle_id"]) && $search_data["circle_id"]) {
                $where[] = ["circle_id", "=", $search_data["circle_id"]];
            }
            if (isset($search_data["floor_id"]) && $search_data["floor_id"]) {
                $where[] = ["floor_id", "=", $search_data["floor_id"]];
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

            if (isset($search_data["purpose"]) && $search_data["purpose"]) {
                $where[] = ["purpose", "=", $search_data["purpose"]];
            }

            $res = Housings::where($where)
                ->orderByDesc("created_at")
                ->paginate(10, ["id", "title", "type", "purpose", "rentsale", "room", "hall", "toilet", "floor_id", "district_id", "circle_id", "area", "direction", "price", DB::raw('price/area AS unit_price'), "pictures","tags"]);

            $all_tag = Tags::get(["id","name"])->toArray();

            foreach ($res as $item) {
                $item["thumd"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $item["pictures"][0] . "?x-oss-process=image/resize,w_500";
//                $item["thumd"] = Storage::disk(config('admin.upload.disk'))->url($item["pictures"][0])."?x-oss-process=image/resize,w_500";
                if ($search_data["list_type"] != 3) {
                    $item["unit_price"] = round($item["unit_price"] * 10000);
                    $item["price_unit"] = "万";
                } else {
                    $item["price_unit"] = "元/月";
                }

                //标签
                $tag = [];
                foreach ($all_tag as $v){
                    if(in_array($v["id"],$item["tags"])){
                        $tag[] = $v["name"];
                    };
                }
                $item["tag"] =$tag;

                //小区
                $floors = $item->floors;
                if ($floors) {
                    $item["floor_name"] = $floors->name;
                } else {
                    $item["floor_name"] = "";
                }

                //区
                $district = $item->district;
                if ($district) {
                    $item["district_name"] = $district->name;
                } else {
                    $item["district_name"] = "";
                }

                $circle = $item->circle;
                if ($circle) {
                    $item["circle_name"] = $circle->name;
                } else {
                    $item["circle_name"] = "";
                }

                unset($item["pictures"]);
                unset($item["floors"]);
                unset($item["district"]);
                unset($item["circle"]);
            }
            return Api_success("房源信息获取成功", $res);
        }
    }

    /**
     * 搜索楼盘
     */
    public function getBuild(Request $request)
    {
        if ($request->isMethod("post")) {
            $data = $request->input();
            if (empty($data["city_code"])) {
                return Api_error("缺少参数");
            }
            $page = 1;
            if (isset($data["inputPage"]) && $data["inputPage"]) {
                $page = $data["inputPage"];
            }
            $res = Floor::where("name", "like", "%" . $data["buildInputText"] . "%")
                ->where("city_id", "=", $data["city_code"])
                ->orderByDesc("created_at")
                ->paginate(40, ["id", "name","district_id"], "", $page);

            foreach ($res as $v) {
                $res["district"] = $v->district;
            }

           return Api_success("楼盘获取成功",$res);
        }
    }

    /**
     * 房源详情
     */
    public function details(Request $request)
    {
        if($request->isMethod("post")){
            $data = $request->input();
            if(empty($data["house_id"])){
                return Api_error("缺少参数");
            }
            $res = Housings::find($data["house_id"],["id","rentsale","title","price","area","agent_id","floor_id","district_id","purpose","years","direction","room","hall","toilet","renovation","floor","t_floor", DB::raw('price/area AS unit_price'),"pictures","desc"]);

            $pictures =[];
            foreach ($res["pictures"] as $item){
                $pictures[] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $item . "?x-oss-process=image/resize,w_500";
            }
            $res["pictures"] = $pictures;


            switch ($res["purpose"]){
                case 1:
                    $res["use"] ="住宅";
                    break;
                case 2:
                    $res["use"] ="别墅";
                    break;
                case 3:
                    $res["use"] ="商铺";
                    break;
                case 4:
                    $res["use"] ="写字楼";
                    break;
            }

            switch ($res["renovation"]){
                case 1:
                    $res["renovation"] ="精装修";
                    break;
                case 2:
                    $res["renovation"] ="简装";
                    break;
                case 3:
                    $res["renovation"] ="清水房";
                    break;
            }
            $res["houseFloor"] = "";
            $one_Third = ceil($res["t_floor"]/3);
            if($res["floor"] <= $one_Third){
                $res["houseFloor"]="低层";
            }else{
                if($one_Third<$res["floor"] && $res["floor"]<= $one_Third*2){
                    $res["houseFloor"]="中层";
                }else{
                    if($one_Third*2 < $res["floor"]){
                        $res["houseFloor"]="高层";
                    }
                }
            }

            //区
            $district = $res->district;
            if($district){
                $res["region_name"] = $district->name;
            }else{
                $res["region_name"] = "";
            }
            //单价
            $res["unit_price"] = round($res["unit_price"] * 10000);
            //小区
            $floor = $res->floors;
            if ($floor) {
                $res["floor_name"] = $floor->name;
            } else {
                $res["floor_name"] = "";
            }
            //经纪人
            $agent = User::where(["id"=>$res["agent_id"],"type"=>1])->first(["id","name","mobile","avatar","open_id"]);
            //是否关注
            $is_follow = 0;
            if(isset($data["make_id"])&&!empty($data["make_id"])){
                $is_follow = Recommend::where(["user_id"=>$data["make_id"],"rec_id"=>$agent["id"],"type"=>1])->count();
            }

            //是否收藏
            $is_collection = 0;
            if(isset($data["make_id"])&&!empty($data["make_id"])){
                $is_collection = Recommend::where(["user_id"=>$data["make_id"],"rec_id"=>$agent["id"],"type"=>2])->count();
            }
            //是否预约
            $makeOrder = 0;
            if(isset($data["make_id"])&&!empty($data["make_id"])){
                $where = ["house_id"=>$data["house_id"],"make_id"=>$res["make_id"],"state"=>0];
                $makeOrder = MakeOrder::where($where)->count();
            }

            //推荐  条件：区、室、面积
            $recommend_where=[
                "district_id"=>$res["district_id"],
                "room"=>$res["room"],
            ];
            $recommend_where[] = ["area",">=",$res["area"]-20];
            $recommend_where[] = ["area","<=",$res["area"]+20];
//            $recommend_where[] = ["id","!=",$data["house_id"]];
            $recommend = Housings::where($recommend_where)->select(["id","title","area","price","room","hall","floor_id", DB::raw('price/area AS rec_unit_price'),"pictures"])->get();

            foreach ($recommend as $item){
                if($item["pictures"]){
                    $item["img"] = "https://" . config("filesystems.disks.oss.bucket") . "." . config("filesystems.disks.oss.endpoint") . "/" . $item["pictures"][0] . "?x-oss-process=image/resize,w_500";
                }

                $item["rec_unit_price"] = round($item["rec_unit_price"] * 10000);

                $recommend_floor = $item->floors;
                if (!empty($recommend_floor)) {
                    $item["floor_name"] = $recommend_floor->name;
                } else {
                    $item["floor_name"] = "";
                }
            }

            $houseInfo = [
                "house" =>$res,
                "agent" => $agent,
                "make" =>$makeOrder,
                "recommend" =>$recommend,
                "is_follow" =>$is_follow,
                "is_collection"=>$is_collection
            ];
            return Api_success("获取成功",$houseInfo);
        }
    }
}