<?php
namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\ExcelExporter;

class HouseExporter extends ExcelExporter
{
    protected $fileName = '房源.xlsx';

    protected $columns = [
        'id'      => 'ID',
        'title'   => '标题',
    ];
}