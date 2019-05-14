<?php


namespace App\Http\Controllers\Api;


use App\Housings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HouseTestController extends Controller
{
    public function test()
    {
        $res = Housings::get(['id','title'])->toArray();
        return json_encode($res);
    }
}