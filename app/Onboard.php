<?php

namespace App;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Onboard extends Model
{

    protected $table = 'on_board_details';
    protected $dates = ['joining_date', 'accept_last_date'];
    protected $guarded = ['id'];

   public function getJoiningDateAttribute($value)
    {  
        return \Carbon\Carbon::parse($value)->format('d/m/Y');        
    }

    public function files()
    {
        return $this->hasMany(OnboardFiles::class, 'on_board_detail_id');
    }

    public function applications()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }
      public function template()
    {
        return $this->belongsTo(Offerletter::class, 'template_id');
    }
      public function salary()
    {
        return $this->hasOne(Salarycreation::class, 'job_application_id','job_application_id');
    }

    public function designation(){
        return $this->belongsTo(Designation::class);
    }

    public function reportto(){
        return $this->belongsTo(User::class, 'reports_to_id');
    }
    public function getExt($name){

    }

    public function  currency(){
         return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function onboardQuestion(){
        return $this->belongsToMany(JobOfferQuestion::class, 'job_onboard_questions', 'job_offer_id', 'question_id');
    }
}
