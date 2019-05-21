<?php

namespace App;





use Illuminate\Database\Eloquent\Model;

class Housings extends Model
{
    public function setPicturesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['pictures'] = json_encode($pictures);
        }
    }

    public function getPicturesAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

    public function getTagsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTagsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tags'] = implode(',', $value);
        }
    }

    public function floors()
    {
      return  $this->belongsTo(Floor::class,'floor_id');
    }

    public function district()
    {
        return  $this->belongsTo(District::class);
    }

    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }
}
