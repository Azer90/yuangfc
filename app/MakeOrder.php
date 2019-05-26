<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/22
 * Time: 22:56
 */

namespace App;



use Illuminate\Database\Eloquent\Model;

class MakeOrder extends Model
{
    protected $table = 'make_order';

    public function housings()
    {
        return $this->belongsTo(Housings::class,"house_id");
    }
}