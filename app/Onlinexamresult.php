<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onlinexamresult extends Model
{
    use HasFactory;
    protected $guarded = [];
 public  function onlinexamjobapplication(){
        return $this->belongsTo(OnlinexamJobApplication::class,'onlinexamjobsapp_id');
    }
}
