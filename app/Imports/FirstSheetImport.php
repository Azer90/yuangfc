<?php


namespace App\Imports;


use App\Housings;


use App\User;
use Encore\Admin\Facades\Admin;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FirstSheetImport implements ToModel,WithBatchInserts,WithStartRow
{
    private $housings_model;
    private $all_cont=0;
    private $success_cont=0;
    private $error_cont=0;
    private $user_info=0;
    private $agen_id=0;
    private $rules;
    private $messages;
    public function __construct()
    {
        $this->housings_model = new Housings();
        $this->user_info = Admin::user();
//        $this->agen_id = DB::name("users")->where("mobile",$this->user_info["mobile"])->value("id");
        if(!empty($this->user_info["mobile"])){
            $w[] =["mobile","=",$this->user_info["mobile"]];
            $w[] = ["type","=",2];
            $this->agen_id = User::where($w)->value("id");
        }
    }
    public function model(array $row)
    {
        $this->all_cont++;
        session(["all_cont"=>$this->all_cont]);
        $arr["title"]=$row[0];
        $arr["rentsale"]=$row[1];
        $arr["type"]=$row[2];
        $arr["owner"]=$row[3];
        $arr["phone"]=$row[4];
        $arr["years"]=$row[5];
        $arr["purpose"]=$row[6];
        $arr["direction"]=$row[7];
        $arr["room"]=$row[8];
        $arr["hall"]=$row[9];
        $arr["toilet"]=$row[10];
        $arr["area"]=$row[11];
        $arr["price"]=$row[12];
        $arr["renovation"]=$row[13];
        $arr["floor"]=$row[14];
        $arr["t_floor"]=$row[15];
        $arr["address"]=$row[16];
        $arr["desc"]=$row[17];
        $arr["creat_at"]=$row[18];
        $arr["remark"]=$row[19];
        $arr["circle_id"]=intval($row[20]);
        $arr["floor_id"]=intval($row[21]);
        $arr["agent_id"]=intval($row[22]);
        $arr["min_price"]=$row[23];

        $rules=[
            "title"=>'required',
            "rentsale"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "type"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "owner"=>'required|string',
            "phone"=>'required|regex:/^1[3-9]\d{9}$/',
            "years"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "purpose"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "direction"=>'required',
            "room"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "hall"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "toilet"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "area"=>'regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "price"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "renovation"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "floor"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "t_floor"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "address"=>'required',
            "desc"=>'required',

//            "remark"=>'required',
            "circle_id"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "floor_id"=>'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "agent_id"=>'nullable|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            "min_price"=>'nullable|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        ];
        $messages=[
            "title"=>"标题必填",
            "rentsale"=>"租售类型必须为数字",
            "type"=>"房源类型必须为数字",
            "owner"=>"缺少房主",
            "phone"=>"电话号码格式不正确",
            "years"=>"年份为数字",
            "purpose"=>"用途必须为数字",
            "direction.required"=>"朝向必填",
            "room"=>"厅必须为数字",
            "hall"=>"卫必须为数字",
            "toilet"=>"面积为数字",
            "price"=>"价格为小数",
            "renovation"=>"装修档次必须为数字",
            "floor"=>"楼层为数字",
            "t_floor"=>"总楼层为数字",
            "address"=>"地址为必填",
            "desc"=>"房屋描述为必填",

//            "remark"=>"备注为数字",
            "circle_id"=>"商圈必须为数字",
            "floor_id.required"=>"楼盘不能为空",
            "floor_id"=>"楼盘必须为数字",
            "agent_id"=>"经纪人必须为数字",
            "min_price"=>"最低价格必须为数字",
//            "16"=>'地址为必填',
//            "17"=>'房屋描述为必填'
        ];

        $validator = Validator::make($arr, $rules, $messages);

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
            $row[19],date("Y-m-d H:i:s",time()),$row[20]?intval($row[20]):0,$row[21]?intval($row[21]):0,$row[22]?intval($row[22]):(!empty($this->agen_id)?$this->agen_id:0),$row[23],$row[0]];

       $sql = 'insert INTO yuangfc_housings (province_id,city_id,district_id,title,rentsale,`type`,purpose,owner,phone,years,direction,room,hall,toilet,area,
        price,renovation,floor,t_floor,address,`desc`,remark,created_at,circle_id,floor_id,agent_id,min_price)
        SELECT ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? FROM DUAL WHERE NOT EXISTS (
        SELECT 1 FROM yuangfc_housings WHERE title= ?
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
//        return new Housings($arr);
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function startRow(): int{
        return 2;
    }
}