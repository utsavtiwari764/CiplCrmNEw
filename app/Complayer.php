<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complayer extends Model
{
    use HasFactory; 
    protected $guarded = [];
 public function salarycreation()
    {
        return $this->belongsTo(Salarycreation::class, 'salarycreation_id');
    }
    public function jobapplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id')->with('job');
    }
}
