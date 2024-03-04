<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratingdetail extends Model
{
    use HasFactory; 
    protected $guarded = [];

  
 public function jobapplication()
    {
        return $this->belongsTo(JobApplication::class,'job_application_id');
    }

 

}
