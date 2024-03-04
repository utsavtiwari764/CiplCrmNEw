<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
     protected $guarded = [];
 public function department(){
   return $this->belongsTo(Department::class, 'department_id');

}

}
