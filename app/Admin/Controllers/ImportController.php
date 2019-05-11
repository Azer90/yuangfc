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
use Maatwebsite\Excel\Facades\Excel;


class ImportController extends Controller
{
    public function import(Request $request)
    {
        $path = $request->file('file')->store('public');
        $filePath = storage_path().'/app/'.$path;

        Excel::import(new HouseImport(),$filePath);
    }
}