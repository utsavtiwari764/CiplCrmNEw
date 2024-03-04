<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobrecruitment extends Model
{
    use HasFactory;
     protected $guarded = [];
    
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id')->with('department');
    }
   

   public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }
  public function department()
    {
        return $this->belongsTo(Department::class, 'job_id');
    }

  public function jd() 
    {
        return $this->belongsTo(Jdstore::class, 'jd_id');
    }
  public function anycertificationold()
    {
        return $this->belongsTo(Certification::class, 'anycertification_id');
    }

    public function anycertification()
    {
        return $this->hasMany(Jobcertification::class, 'jobrecruitment_id')->with('certification');
    }

    public function categorywithskill()
    {
        return $this->belongsTo(JobCategory::class, 'category_id')->with('jobskill');
    }
     public function qualification(){
        return $this->hasMany(Jobqualification::class, 'jobrecruitment_id')->with('qualificationdetails');
    }

    public function subqualifications(){
        return $this->hasMany(JobSubqualification::class, 'jobrecruitment_id')->with('subqualification');
    }
    public function replacements(){
        return $this->hasMany(Replacementemployee::class, 'jobrecruitment_id');
    }
  public function skills(){
        return $this->hasMany(JobSkill::class, 'jobrecruitment_id')->with('skill');
    }
 public function assignBy(){
        return $this->belongsTo(User::class, 'assign_id');
    }

    public function assignedetails(){
        return $this->hasMany(Jobassign::class, 'assign_id');
    }
   public function assigned(){
   return $this->hasMany(Jobassign::class, 'jobrecruitment_id');

   }

 public function jobapplication(){
        return $this->hasMany(JobApplication::class, 'jobrecruitment_id');
    }
}
