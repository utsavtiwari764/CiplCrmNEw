<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineexamJobApplicationResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'onlinequestion_id',
        'onlineexamjobapp_id',
        'answer_by_jobapplication',
        'true_answer',
        'status',
        'marks'
];
    protected $table = 'onlineexam_job_application_results';

}
