<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Encore\Admin\Facades\Admin;
class Entrust extends Model
{
    protected $table = 'entrust';
    protected $is_type = '';

    public function __construct($is_type='', array $attributes = [])
    {
       $this->is_type=$is_type;
        parent::__construct($attributes);
    }

    public function paginate()
    {
        $id = Request::get('id', 0);
        $province_id = Request::get('province_id', 0);
        $city_id = Request::get('city_id', 0);
        $district_id = Request::get('district_id', 0);

        $perPage = Request::get('per_page', 20);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;
        $rentsale=empty($this->is_type)?[1,2]:[3,4];
        if(Admin::user()->isAdministrator()){
            if($id>0){
                $where['id']=$id;
            }
            if($province_id>0){
                $where['province_id']=$province_id;
            }
            if($city_id>0){
                $where['city_id']=$city_id;
            }
            if($district_id>0){
                $where['district_id']=$district_id;
            }
            if (isset($where)){
                $result = self::skip($start)->whereBetween('rentsale',$rentsale)->where($where)->take($perPage)->orderBy('id', 'desc')->get()->toArray();
            }else{
                $result = self::skip($start)->whereBetween('rentsale',$rentsale)->take($perPage)->orderBy('id', 'desc')->get()->toArray();
            }
            // 运行sql获取数据数组

            $total =count($result);
        }else{
            // 运行sql获取数据数组
            $district_id=Admin::user()->district_id;
            $result = self::where('district_id',$district_id)->whereBetween('rentsale',$rentsale)->skip($start)->take($perPage)->orderBy('id', 'desc')->get()->toArray();
            $total =self::where('district_id',$district_id)->whereBetween('rentsale',$rentsale)->count();
        }

        if(!empty($result)){
            foreach ($result  as $key=>$value){
                $address[$value['province_id']]=$value['province_id'];
                $address[$value['city_id']]=$value['city_id'];
                $address[$value['district_id']]=$value['district_id'];
            }
            $name=District::whereIn('id',$address)->get(['id','name'])->pluck('name','id')->toArray();

            foreach ($result  as $key=>$value){
                $result[$key]['address']=$name[$value['province_id']].'-'.$name[$value['city_id']].'-'.$name[$value['district_id']];
            }
        }

        $result = static::hydrate($result);


        $paginator = new LengthAwarePaginator($result, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }
}
