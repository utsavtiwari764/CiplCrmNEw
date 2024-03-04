<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function subqualification(){
        return $this->hasMany(Subqualification::class, 'qualification_id');
    }
}
