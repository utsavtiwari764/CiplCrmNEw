<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onlinequestion extends Model
{
    protected $fillable = [
        'question',
        'option',
        'answer',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'marks',
        'question_type',
        'jobcategory_id',
        'status'
     ];
    public function category(){
        return $this->belongsTo(JobCategory::class, 'jobcategory_id');
    }
    public function onlinexam(){
        return $this->belongsToMany('App\Onlinexam')->withTimestamps();
    }
public  function onlinexamjobapplicationresult(){
        return $this->hasMany(Onlinexamresult::class,'onlinequestion_id');
    }

    use HasFactory; 
}
