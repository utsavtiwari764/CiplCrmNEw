<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salarycreation extends Model
{
    use HasFactory;
    protected $guarded = [];

 public function jobapplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function complayer(){
        return $this->hasMany(Complayer::class, 'salarycreation_id');
    }

}
 