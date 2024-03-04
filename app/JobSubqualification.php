<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSubqualification extends Model
{
    use HasFactory;
    public function subqualificationold(){
        return $this->hasMany(Subqualification::class, 'qualification_id');
    }

 public function subqualification(){
        return $this->belongsTo(Subqualification::class, 'subqualification_id');
    }

    protected $guarded = ['id'];
}
