<?php

namespace App\Imports;

use App\House;
use App\Housings;
use Maatwebsite\Excel\Concerns\ToModel;

class HouseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!isset($row[0])){
            return null;
        }
        dump($row);

//        return new Housings([
//
//        ]);
    }
}
