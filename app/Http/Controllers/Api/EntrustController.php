<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/25
 * Time: 13:36
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class EntrustController extends Controller
{

    public function add_data(Request $request){
        if ($request->isMethod("post")) {
            $data=$request->all();
            $validator = Validator::make($data, [
                'addrs' => 'required',
                'cell_name' => 'required',
                'addr' => 'required',
                'name' => 'required',
                'area' => 'required',
                'price' => 'required',
                'mobile' => 'required',
                'rentsale' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                Api_error($errors->first());
            }

        }else{
            Api_error('非法请求');
        }
    }
}