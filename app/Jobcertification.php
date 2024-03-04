<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobcertification extends Model
{
    use HasFactory;
  protected $guarded = [];
      public function certification(){
        return $this->belongsTo(Certification::class, 'anycertification_id');
    }

}
