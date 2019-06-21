<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Encore\Admin\Facades\Admin;
class UserCenter extends Model
{
    protected $table = 'user_center';

    public function paginate()
    {
        $id = Request::get('id', 0);
        $_scope_ = Request::get('_scope_', '');

        $province_id = Request::get('province_id', 0);
        $city_id = Request::get('city_id', 0);
        $district_id = Request::get('district_id', 0);

        $perPage = Request::get('per_page', 20);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;
        if(empty($_scope_)){
            $where['is_delete']=0;
        }else{
            $where['is_delete']=1;
        }

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

            $result = self::skip($start)->where($where)->take($perPage)->orderBy('id', 'desc')->get()->toArray();

            // 运行sql获取数据数组

            $total =count($result);
        }else{
            // 运行sql获取数据数组
            $district_id=Admin::user()->district_id;
            $result = self::where('district_id',$district_id)->where($where)->skip($start)->take($perPage)->orderBy('id', 'desc')->get()->toArray();
            $total =self::where('district_id',$district_id)->where($where)->count();
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
