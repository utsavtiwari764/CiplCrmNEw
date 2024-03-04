<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jdstore extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function jobequipments()
    {
        return $this->hasMany(Jobrequipment::class, 'jb_id');
    }
}
