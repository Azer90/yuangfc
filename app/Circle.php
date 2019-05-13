<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Circle extends Model
{
    protected $table = 'circle';
    public function floor()
    {
        return $this->hasMany(Floor::class);
    }
}
