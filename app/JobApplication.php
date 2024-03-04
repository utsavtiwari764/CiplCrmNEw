<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class JobApplication extends Model
{
    use Notifiable, SoftDeletes, HasFactory;

    protected $dates = ['dob', 'created_at'];

    protected $casts = [
        'skills' => 'array'
    ];

   // protected $appends = ['resume_url','pcc_url'];
 protected $appends = ['resume_url','document_url','pcc_url','aadharfront_url','aadharback_url','passport_url','pancard_url','voterid_url','offerletter_url','appointmentletter_url','salaryslip_url','bankstatement_url','result_url','other_url'];
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
 public function addedby(){
        return $this->belongsTo(User::class, 'added_by');
    }
      public function getOtherUrlAttribute()
    {
        if ($this->documents()->where('name', 'other')->first()) {
       
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'other')->first()->hashname);
        }
        return false;
    }

    public function getResultUrlAttribute()
    {
        if ($this->documents()->where('name', 'result')->first()) {
       
            return asset_url_local_s3('result/' . $this->id . '/' . $this->documents()->where('name', 'result')->first()->hashname);
        }
        return false;
    }
    public function resumeDocument()
    {
        return $this->morphOne(Document::class, 'documentable')->where('name', 'Resume');
    }
    public function salarydetails()
    {
        return $this->hasOne(Salarycreation::class, 'job_application_id');
    }
    public function qualification()
    {
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }
     public function subqualifications(){
        return $this->hasMany(JobApplicationsubqualification::class, 'jobapplication_id')->with('subqualification');
    }
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id')->with('department','jobassigned');
    }
    
  


    public function interview(){
       return $this->hasOne(InterviewSchedule::class, 'job_application_id');

    }

  public function interviewround2(){
       return $this->hasOne(InterviewSchedule::class, 'job_application_id')->where('status','interview round 2');

    }

public function interviews(){
        return $this->hasMany(InterviewSchedule::class, 'job_application_id');
 
    }
    public function totalrating()
    {
        return $this->hasMany(Ratingdetail::class,'job_application_id');
    }
    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }
    public function recruitment()
    {
        return $this->belongsTo(Jobrecruitment::class, 'jobrecruitment_id');
    }
    public function status()
    {
        return $this->belongsTo(ApplicationStatus::class, 'status_id');
    }

    public function schedule()
    {
        return $this->hasOne(InterviewSchedule::class)->latest();
    }

    public function onboard()
    {
        return $this->hasOne(Onboard::class)->where('hired_status','!=','rejected');
    }

    public function getResumeUrlAttribute()
    {
        if ($this->documents()->where('name', 'Resume')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'Resume')->first()->hashname);
        }
        return false;
    }
  public function getDocumentUrlAttribute()
    {
        if ($this->documents()->where('name', 'Document')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'Document')->first()->hashname);
        }
        return false;
    }

    public function getPccUrlAttribute()
    {
        if ($this->documents()->where('name', 'pcc')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'pcc')->first()->hashname);
        }
        return false;
    }
    
public function getAadharfrontUrlAttribute()
    {
        if ($this->documents()->where('name', 'aadharfront')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'aadharfront')->first()->hashname);
        }
        return false;
    }
    public function getAadharbackUrlAttribute()
    {
        if ($this->documents()->where('name', 'aadharback')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'aadharback')->first()->hashname);
        }
        return false;
    }
    public function getPassportUrlAttribute()
    {
        if ($this->documents()->where('name', 'passport')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'passport')->first()->hashname);
        }
        return false;
    }
    public function getPancardUrlAttribute()
    {
        if ($this->documents()->where('name', 'pancard')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'pancard')->first()->hashname);
        }
        return false;
    }
    public function getVoteridUrlAttribute()
    {
        if ($this->documents()->where('name', 'voterid')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'voterid')->first()->hashname);
        }
        return false;
    }
    public function getOfferletterUrlAttribute()
    {
        if ($this->documents()->where('name', 'offerletter')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'offerletter')->first()->hashname);
        }
        return false;
    }
    public function getAppointmentletterUrlAttribute()
    {
        if ($this->documents()->where('name', 'appointmentletter')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'appointmentletter')->first()->hashname);
        }
        return false;
    }
    
    public function getSalaryslipUrlAttribute()
    {
        if ($this->documents()->where('name', 'salaryslip')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'salaryslip')->first()->hashname);
        }
        return false;
    }
    public function getBankstatementUrlAttribute()
    {
        if ($this->documents()->where('name', 'bankstatement')->first()) {
            return asset_url_local_s3('documents/' . $this->id . '/' . $this->documents()->where('name', 'bankstatement')->first()->hashname);
        }
        return false;
    }

    public function notes()
    {
        return $this->hasMany(ApplicantNote::class, 'job_application_id')->orderBy('id', 'desc');
    }

    public function getPhotoUrlAttribute()
    {
        if (is_null($this->photo)) {
            return asset('avatar.png');
        }
        return asset_url_local_s3('candidate-photos/' . $this->photo);
    }

    public function answer()
    {
        return $this->belongsTo(JobApplicationAnswer::class, 'status_id');
    }
}
