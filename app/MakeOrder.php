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

        $perPage = Request::get('per_page', 20);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;
        if(Admin::user()->isAdministrator()){
            // 运行sql获取数据数组
            $result = self::from('make_order as mo')->where('mo.add_schedule',1)
                ->join('housings as h','h.id','=','mo.house_id')
                ->join('users as u','u.id', '=', 'mo.agent_id')
                ->join('users as u1','u1.id', '=', 'mo.make_id')
                ->skip($start)->take($perPage)->orderBy('mo.id', 'desc')->get(['mo.*','h.title','u.name','u1.wchat_name'])->toArray();
            $total =self::where('add_schedule',1)->count();
        }else{
            // 运行sql获取数据数组
            $district_id=Admin::user()->district_id;
            $result = self::from('make_order as mo')->where(['mo.add_schedule'=>1,'h.district_id'=>$district_id])
                ->join('housings as h','h.id','=','mo.house_id')
                ->join('users as u','u.id', '=', 'mo.agent_id')
                ->join('users as u1','u1.id', '=', 'mo.make_id')
                ->skip($start)->take($perPage)->orderBy('mo.id', 'desc')->get(['mo.*','h.title','u.name','u1.wchat_name'])->toArray();
            $total =self::where('add_schedule',1)->count();
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