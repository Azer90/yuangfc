<?php
namespace App;

use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Facades\Admin;
use Illuminate\Pagination\LengthAwarePaginator;
class MakeOrder extends Model
{
    protected $table = 'make_order';

    public function housings()
    {
        return $this->belongsTo(Housings::class,"house_id");
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
        $where['mo.is_zy']=0;
        if(Admin::user()->isAdministrator()){
            // 运行sql获取数据数组
            if($id>0){
                $where['mo.id']=$id;
            }
            if($province_id>0){
                $where['h.province_id']=$province_id;
            }
            if($city_id>0){
                $where['h.city_id']=$city_id;
            }
            if($district_id>0){
                $where['h.district_id']=$district_id;
            }
            if(isset($where)){
                $result = self::from('make_order as mo')
                    ->join('housings as h','h.id','=','mo.house_id')
                    ->join('users as u','u.id', '=', 'mo.agent_id')
                    ->join('users as u1','u1.id', '=', 'mo.make_id')
                    ->where($where)->skip($start)->take($perPage)->orderBy('mo.id', 'desc')->get(['mo.*','h.title','u.name','u1.wchat_name','u1.mobile'])->toArray();

                $total =count($result);
            }else{
                $result = self::from('make_order as mo')->where($where)
                    ->join('housings as h','h.id','=','mo.house_id')
                    ->join('users as u','u.id', '=', 'mo.agent_id')
                    ->join('users as u1','u1.id', '=', 'mo.make_id')
                    ->skip($start)->take($perPage)->orderBy('mo.id', 'desc')->get(['mo.*','h.title','u.name','u1.wchat_name','u1.mobile'])->toArray();
                $total =self::count();
            }

        }else{
            // 运行sql获取数据数组
            $district_id=Admin::user()->district_id;
            $result = self::from('make_order as mo')->where(['h.district_id'=>$district_id,'mo.is_zy'=>0])
                ->join('housings as h','h.id','=','mo.house_id')
                ->join('users as u','u.id', '=', 'mo.agent_id')
                ->join('users as u1','u1.id', '=', 'mo.make_id')
                ->skip($start)->take($perPage)->orderBy('mo.id', 'desc')->get(['mo.*','h.title','u.name','u1.wchat_name','u1.mobile'])->toArray();
            $total =self::count();
        }

        //dd($result);
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