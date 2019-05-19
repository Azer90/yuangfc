<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'district';

    public function Housings()
    {
        $this->hasMany(Housings::class);
    }
}
