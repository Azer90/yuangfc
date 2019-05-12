<?php
namespace App\Http\Controllers\Api;

use App\District;
use App\Http\Controllers\Controller;
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
}