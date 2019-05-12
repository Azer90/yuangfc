<?php


namespace App\Imports;


use App\Housings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FirstSheetImport implements ToModel,WithBatchInserts,WithStartRow
{
    private $housings_model;
    public function __construct()
    {
        $this->housings_model = new Housings();
    }
    public function model(array $row)
    {
//        $data=[
//            "title"=>$row[0],
//            "rentsale"=>$row[1],
//            "type"=>0,
//            "purpose"=>$row[5],
//            "owner"=>$row[2],
//            "phone"=>$row[3],
//            "years"=>$row[4],
//            "direction"=>$row[6],
//            "room"=>$row[7],
//            "hall"=>$row[8],
//            "toilet"=>$row[9],
//            "area"=>$row[10],
//            "price"=>$row[11],
//            "renovation"=>$row[12],
//            "floor"=>$row[13],
//            "t_floor"=>$row[14],
//            "address"=>$row[15],
//            "desc"=>$row[16],
//            "remark"=>$row[18],
////            "latitude"=>$row[0],
////            "longitude"=>$row[0],
//            "created_at"=>time(),
//            "circle_id"=>$row[19],
//            "floor_id"=>$row[20],
//            "agent_id"=>$row[21],
//        ];

        $insert_data = [$row[0],$row[1],1,$row[5],$row[2],$row[3],$row[4],$row[6],$row[7],
            $row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],
            $row[18],date("Y-m-d H:i:s",time()),$row[19]?$row[19]:0,$row[20]?$row[20]:0,$row[21]?$row[21]:0,$row[0]];
       $sql = 'insert INTO housings (title,rentsale,`type`,purpose,owner,phone,years,direction,room,hall,toilet,area,
        price,renovation,floor,t_floor,address,`desc`,remark,created_at,circle_id,floor_id,agent_id)
        SELECT ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? FROM DUAL WHERE NOT EXISTS (
        SELECT 1 FROM housings WHERE title= ?
)';
       try{
           $res = DB::insert($sql,$insert_data);
           dump($res);
       }catch (\Exception $e){
           dump($e->getMessage());
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