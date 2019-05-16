<?php


namespace App\Http\Controllers\Api;


use App\Housings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HouseController extends Controller
{
    private $houseModel;
    public function __construct()
    {

    }
    /**
     * @return false|string
     * 主页热门房源
     */
    public function indexHotHouse()
    {
        $res = Housings::get(['id','title'])->toArray();
        return Api_Success($res);
    }
}