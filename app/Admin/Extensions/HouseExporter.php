<?php
namespace App\Admin\Extensions;

use App\Circle;
use App\District;
use App\Floor;
use App\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class HouseExporter extends ExcelExporter implements WithMapping
{
    protected $fileName = '房源.xlsx';

    protected $columns = [
        'id'      => 'ID',
        'title'   => '标题',
        'province_id'   => '省',
        'city_id'   => '市',
        'district_id'   => '区/县',
        'circle_id'   => '商圈',
        'floor_id'   => '楼盘',
        'agent_id'   => '经纪人',
        'rentsale'   => '租售类型',
        'purpose'   => '用途',
        'owner'   => '业主',
        'phone'   => '联系方式',
        'years'   => '修建年份',
        'direction'   => '朝向',
        'room'   => '房',
        'hall'   => '厅',
        'toilet'   => '卫',
        'area'   => '面积',
        'price'   => '价格',
        'renovation'   => '装修类型',
        'floor'   => '楼层',
        't_floor'   => '总楼层',
        'address'   => '地址',
        'desc'   => '描述',
        'remark'   => '备注',
        'min_price'   => '最低价格',
        'providers'   => '房源提供者',

    ];

    public function map($house) : array
    {   set_time_limit(0) ;
        $district=District::all(['id','name'])->pluck('name','id')->toArray();
        $circle=Circle::all(['id','name'])->pluck('name','id')->toArray();
        $floor=Floor::all(['id','name'])->pluck('name','id')->toArray();
        $agent=User::where('type',1)->get(['id','name'])->pluck('name','id')->toArray();

        return [
            $house->id,
            $house->title,
            isset($district[ $house->province_id])?$district[ $house->province_id]:'',
            isset($district[ $house->city_id])?$district[ $house->city_id]:'',
            isset($district[ $house->district_id])?$district[ $house->district_id]:'',
            isset($circle[$house->circle_id])?$circle[$house->circle_id]:'',
            isset( $floor[$house->floor_id])?$floor[$house->floor_id]:'',
            isset( $agent[$house->agent_id])?$agent[$house->agent_id]:'',
            ($house->rentsale==1) ? '出售' : '出租',
            ($house->purpose==1)?'住宅': (($house->purpose==2)?'别墅':(($house->purpose==3)?'商铺':'写字楼')),
            $house->owner,
            $house->phone,
            $house->years,
            $house->direction,
            $house->room,
            $house->hall,
            $house->toilet,
            $house->area,
            $house->price,
            ($house->renovation==1) ? '精装修' : (($house->purpose==2)?'简装':'清水房'),
            $house->floor,
            $house->t_floor,
            $house->address,
            $house->desc,
            $house->remark,
            $house->min_price,
            isset( $agent[$house->providers])?$agent[$house->providers]:'',

        // data_get($house, 'floors.name'),    // 读取关联关系数据
        ];
    }

}