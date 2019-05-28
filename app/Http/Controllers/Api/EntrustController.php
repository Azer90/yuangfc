<?php

namespace App\Http\Controllers\Api;

use App\Entrust;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class EntrustController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 添加委托
     */
    public function add_data(Request $request){
        if ($request->isMethod("post")) {
            $data=$request->all();
            $rules=[
                'addrs' => 'required',
                'cell_name' => 'required',
                'addr' => 'required',
                'name' => 'required',
                'area' => 'required',
                'price' => 'required',
                'mobile' => 'required',
                'rentsale' => 'required',
            ];
            $messages = [
                'addrs.required' => '省市区必选',
                'cell_name.required' => '小区名必填',
                'addr.required' => '详细地址必填',
                'name.required' => '称呼必填',
                'area.required' => '面积必填',
                'price.required' => '价格必填',
                'mobile.required' => '联系电话必填',
                'rentsale.required' => '租售类型必选',
            ];
            $validator = Validator::make($data, $rules,$messages);

            if ($validator->fails()) {
                $errors = $validator->errors();
              return  Api_error($errors->first());
            }

            $_data=[
                'province_id'=>$data['addrs'][0],
                'city_id'=>$data['addrs'][1],
                'district_id'=>$data['addrs'][2],
                'cell_name'=>$data['cell_name'],
                'addr'=>$data['addr'],
                'name'=>$data['name'],
                'area'=>$data['area'],
                'price'=>$data['price'],
                'mobile'=>$data['mobile'],
                'rentsale'=>(int)$data['rentsale'],
                'state'=>0,
                'reason'=>'',
                'created_at'=>date('Y-m-d H:i:s'),
            ];
        $id=Entrust::insertGetId($_data);
        if($id){
            return  Api_success('提交成功,等待管理员审核');
        }

        }else{
            return  Api_error('非法请求');
        }
    }

    /**
     *获取委托列表
     */
    public function getList(Request $request)
    {
        $data = $request->input();
        if(empty($data["uId"])){
            return Api_error("缺少参数");
        }
//        Entrust::where("u_id",$data["id"])->paginate(10,"");
    }
}