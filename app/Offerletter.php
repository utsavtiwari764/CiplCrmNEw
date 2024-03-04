<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offerletter extends Model
{
    use HasFactory;
    protected $guarded = [];
protected $appends = ['signature_url'];

 public function getSignatureUrlAttribute()
    {
        if(is_null($this->signature)){
            return false;
        }
        return asset($this->signature);
    }

}
