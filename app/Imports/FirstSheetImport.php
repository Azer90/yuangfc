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

    }
    public function model(array $row)
    {
        $error_info=[];
        $rules=[
            "0"=>'required',
            "1"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "2"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "3"=>'required|string',
            "4"=>'required|regex:/^1[3-9]\d{9}$/',
            "5"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "6"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "8"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "9"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "10"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "11"=>'regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "12"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "13"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "14"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "15"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "16"=>'required',
            "17"=>'required',
            "18"=>'required',
            "19"=>'required',
            "20"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "21"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "22"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        ];
        $messages=[
            "0"=>"标题必填",
            "1"=>"租售类型必须为数字",
            "2"=>"房源类型必须为数字",
            "4"=>"电话号码格式不正确",
            "5"=>"年份为数字",
            "6"=>"用途必须为数字",
            "8"=>"房必须为数字",
            "9"=>"厅必须为数字",
            "10"=>"卫必须为数字",
            "11"=>"面积为数字",
            "12"=>"价格为小数",
            "13"=>"装修档次必须为数字",
            "14"=>"楼层为数字",
            "15"=>"总楼层为数字",
            "20"=>"商圈必须为数字",
            "21"=>"楼盘必须为数字",
            "22"=>"经纪人必须为数字",
//            "16"=>'地址为必填',
//            "17"=>'房屋描述为必填'
        ];

        $validator = Validator::make($row, $rules, $messages);

        if($validator->fails()){
            $row["error_info_tips"] = $validator->errors()->getMessages();
            session()->push('error_info',$row);
            $this->error_cont++;
            session(['error_cont'=>$this->error_cont]);//失败条数
            return;
        }
        //获取当前用户所属的省市区
        $province_id = $this->user_info["province_id"];
        $city_id = $this->user_info["city_id"];
        $district_id = $this->user_info["district_id"];

        $insert_data = [$province_id,$city_id,$district_id,$row[0],$row[1],$row[2],$row[6],$row[3],$row[4],$row[5],$row[7],$row[8],
            $row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],$row[17],
            $row[19],$row[18]?$row[18]:date("Y-m-d H:i:s",time()),$row[20]?intval($row[20]):0,$row[21]?intval($row[21]):0,$row[22]?intval($row[22]):0,$row[0]];
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

           $row["error_info_tips"] = "数据重复";
           session()->push('error_info',$row);

           $this->error_cont++;
           session(['error_cont'=>$this->error_cont]);//失败条数
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