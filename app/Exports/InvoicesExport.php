<?php


namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExport implements FromArray,WithHeadings
{
    protected $invoices;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        return $this->invoices;
    }
    public function headings(): array
    {
        return [
            '标题',
            '类型 (售:shou,租:zu)',
            '房源类型',
            '业主',
            '联系方式',
            '年份',
            '用途',
            '朝向',
            '几房',
            '几厅',
            '几卫',
            '面积',
            '价格,售(万元),租(元)',
            '装修档次',
            '楼层',
            '总楼层',
            '地址',
            '房屋描述',
            '添加时间(不填为导入时间)',
            '备注',
            '商圈ID',
            '楼盘ID',
            '经纪人UID',
            '错误信息',
        ];
    }
}