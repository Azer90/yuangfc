<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $table = 'floor';

    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }
}
