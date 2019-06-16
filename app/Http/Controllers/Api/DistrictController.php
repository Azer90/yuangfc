<?php
namespace App\Http\Controllers\Api;

use App\Circle;
use App\District;
use App\Floor;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictController extends Controller{

    public function province()
    {

        return District::where('parent_id', 0)->get(['id', DB::raw('name as text')]);
    }
    public function city(Request $request)
    {
        $provinceId = $request->get('q');

        return District::where('parent_id', $provinceId)->get(['id', DB::raw('name as text')]);
    }
    public function circle(Request $request)
    {
        $districtId = $request->get('q');

        return Circle::where('district_id', $districtId)->get(['id', DB::raw('name as text')]);
    }

    public function floor(Request $request)
    {
        $circleId = $request->get('q');

        return Floor::where('circle_id', $circleId)->get(['id', DB::raw('name as text')]);
    }

    public function agent()
    {

        return User::where('type', 1)->get(['id', DB::raw('wchat_name as text')]);
    }

    /**
     * 获取所以省份
     */
    public function getProvince()
    {
        $res =  District::where('parent_id', 0)->get(['id', "name"]);

        return Api_success("获取成功",$res);
    }

    /**
     * @param Request $request
     * @return District[]|\Illuminate\Database\Eloquent\Collection
     * 获取指定省下市
     */
    public function getCity(Request $request)
    {
        $provinceId = $request->input('province_id');

        $res = District::where('parent_id', $provinceId)->get(['id', DB::raw('name as text')]);

        return  Api_success("获取成功",$res);
    }

}