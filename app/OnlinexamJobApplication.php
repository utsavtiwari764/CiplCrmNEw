<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlinexamJobApplication extends Model
{
    use HasFactory;
    protected $fillable = [
        'jobapplication_id',
        'onlinexam_id',
        'status',
        'in_attempt',
       'scheduled_time',
       'scheduled_date',
        
     ];
    protected $table = 'onlinexam_job_applications';
 public function onlineexam()
    {
        return $this->belongsTo(Onlinexam::class,'onlinexam_id')->with('onlinequestion');
    }


public function onlineexamdeatils()
    {
        return $this->belongsTo(Onlinexam::class,'onlinexam_id')->with('onlinequestion');
    }


}
