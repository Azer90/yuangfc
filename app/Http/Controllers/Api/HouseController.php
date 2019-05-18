<?php


namespace App\Http\Controllers\Api;


use App\Housings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseController extends Controller
{
    public function hotHouse(Request $request)
    {
        if($request->isMethod("post")){

            //热门二手房出
            $two_hand_w = ["is_display"=>1,"setup"=>1,"type"=>2,"rentsale"=>1];
            $two_hand = $this->getHot($two_hand_w);
            //热门租房
            $renting_w = ["is_display"=>1,"setup"=>1,"rentsale"=>2];
            $renting = $this->getHot($renting_w);
            //热门新房
            $new_house_w = ["is_display"=>1,"setup"=>1,"type"=>1,"rentsale"=>1];
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
            ->select("id","title","type","purpose","room","hall","toilet","floor_id","area","direction","price",DB::raw('price/area AS unit_price'),"pictures")
            ->limit(3)
            ->orderBy("created_at","desc")
            ->get();
        foreach ($res as $item){
            $item["thumd"] = "https://".config("filesystems.disks.oss.bucket").".".config("filesystems.disks.oss.endpoint")."/".$item["pictures"][0]."?x-oss-process=image/resize,w_500";
            $item["unit_price"] = $item["unit_price"]*1000;
            $item["floor_name"] = $item->floors->name;
        }
        return $res;
    }
}