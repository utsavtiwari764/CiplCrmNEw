<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobassign extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function leadassigned(){
        return $this->belongsTo(User::class,'user_id');
    }

   public function belongsassigned(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function transfer(){
        return $this->belongsTo(User::class,'transfer');
    }
}
