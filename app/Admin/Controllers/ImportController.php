<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/10
 * Time: 22:32
 */

namespace App\Admin\Controllers;



use App\Exports\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Imports\HouseImport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;


class ImportController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     * 导入excel
     */
    public function import(Request $request)
    {

        $path = $request->file('file')->store('public');

        if(pathinfo($path,PATHINFO_EXTENSION)!="xls" && pathinfo($path,PATHINFO_EXTENSION)!="xlsx"){
            return "文件类型错误";
        }
        $filePath = storage_path().'/app/'.$path;
        $import = new HouseImport();

        $import->onlySheets(0);
        $res = Excel::import($import,$filePath);
        $all_cont = session("all_cont");
        $success_cont = session("success_cont");
        $error_cont = session("error_cont");
        $error_info = session("error_info");
        session(["error_info_live"=>$error_info]);
        session()->forget('success_cont');
        session()->forget('error_cont');
        session()->forget('error_info');
        session()->forget('all_cont');

        $res_info=[];
        if(!empty($error_info)){
            foreach ($error_info as $k=>$v){
                $res_info[] =$error_info[$k];
            }
        }

       $data =[
           "all_cont"=>$all_cont?$all_cont:0,
           "success_cont"=>$success_cont?$success_cont:0,
           "error_cont"=>$error_cont?$error_cont:0,
           "error_info"=>$res_info,
       ];
        unlink($filePath);
        return response()->json(['code' => 1,'message' =>'导入完成', 'data' => $data]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * 导出
     */
    public function export()
    {
        $data=[];
        $error_info=session("error_info_live");
        if(!empty($error_info)){
            foreach ($error_info as $key => $value){
                foreach ($value as $k=>$val){
                    if($k==="error_info_tips"){
                        $str = "";
                        if(is_array($val)){
                            foreach ($val as $v){
                                $str.=$v[0]." ";
                            }
                        }else{
                            $str=$val;
                        }
                            $data[$key][]=$str;
                    }else{
                        $data[$key][]=$val;
                    }
                }
            }
        }
        $export = new InvoicesExport($data);

        return Excel::download($export, '失败数据.xlsx');
    }
}