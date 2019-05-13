<?php


namespace App\Imports;


use App\Housings;

use Encore\Admin\Facades\Admin;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FirstSheetImport implements ToModel,WithBatchInserts,WithStartRow
{
    private $housings_model;
    private $success_cont=0;
    private $error_cont=0;
    private $user_info=0;
    private $rules;
    private $messages;
    public function __construct()
    {
        $this->housings_model = new Housings();
        $this->user_info = Admin::user();
        $this->rules=[
            "0"=>'required|string',
            "1"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "2"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "3"=>'required|string',
            "4"=>'required|regex:/^1[3-9]\d{9}$/',
            "5"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "6"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "8"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "9"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "10"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "11"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "12"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "13"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "14"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "15"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "16"=>'string',
            "17"=>'string',
            "20"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "21"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "22"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        ];
        $this->messages=[
            "1.numeric"=>"房源类型必须为数字",
            "2.numeric"=>"房源类型必须为数字",
            "4.mobile"=>"电话号码格式不正确",
            "5.numeric"=>"年份为数字",
            "6.numeric"=>"用途必须为数字",
            "8.numeric"=>"房必须为数字",
            "9.numeric"=>"厅必须为数字",
            "10.numeric"=>"卫必须为数字",
            "11.float"=>"面积为数字",
            "12.float"=>"价格为小数",
            "13.numeric"=>"装修档次必须为数字",
            "14.numeric"=>"楼层为数字",
            "15.float"=>"总楼层为数字",
            "20.float"=>"商圈必须为数字",
            "21.float"=>"楼盘必须为数字",
            "22.float"=>"经纪人必须为数字",
        ];
    }
    public function model(array $row)
    {
dump($row);
        $this->validator = Validator::make($row, $this->rules, $this->messages);

        if($this->validator->fails()){
            dump($this->validator->errors()->getMessages());
            return;
//            $this->validator->errors()->getMessages();
        }
        //获取当前用户所属的省市区
        $province_id = $this->user_info["province_id"];
        $city_id = $this->user_info["city_id"];
        $district_id = $this->user_info["district_id"];

        $insert_data = [$province_id,$city_id,$district_id,$row[0],$row[1],$row[2],$row[6],$row[3],$row[4],$row[5],$row[7],$row[8],
            $row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],$row[17],
            $row[19],date("Y-m-d H:i:s",time()),$row[20]?intval($row[20]):0,$row[21]?intval($row[21]):0,$row[22]?intval($row[22]):0,$row[0]];
       $sql = 'insert INTO housings (province_id,city_id,district_id,title,rentsale,`type`,purpose,owner,phone,years,direction,room,hall,toilet,area,
        price,renovation,floor,t_floor,address,`desc`,remark,created_at,circle_id,floor_id,agent_id)
        SELECT ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? FROM DUAL WHERE NOT EXISTS (
        SELECT 1 FROM housings WHERE title= ?
)';

       try{
          DB::insert($sql,$insert_data);
          $this->success_cont++;
          session(["success_cont"=>$this->success_cont]);//成功条数
       }catch (\Exception $e){
           $this->error_cont++;
           $error_info=[];
           session(['error_cont'=>$this->error_cont]);//失败条数
           if(empty(session("error_info"))){
               session(['error_info'=>$error_info]);
               session()->push('error_info',$this->error_cont);//失败数据信息
           }else{

               session()->push('error_info',$row);
           }
       }
//        return new Housings($data);
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function startRow(): int{
        return 2;
    }
}