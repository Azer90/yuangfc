<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/10
 * Time: 22:32
 */

namespace App\Admin\Controllers;



use App\Http\Controllers\Controller;
use App\Imports\HouseImport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;


class ImportController extends Controller
{
    public function import(Request $request)
    {

        $path = $request->file('file')->store('public');

        if(pathinfo($path,PATHINFO_EXTENSION)!="xls"){
            return "文件类型错误";
        }
        $filePath = storage_path().'/app/'.$path;
        $import = new HouseImport();

        $import->onlySheets(0);
        Excel::import($import,$filePath);
        return response()->json(['code' => 1, 'state' => 'CA']);
    }
}