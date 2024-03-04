<?php

namespace App;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use Sluggable;

    protected $dates = ['end_date', 'start_date'];

    protected $casts = [
        'required_columns' => 'array',
        'meta_details' => 'array',
        'section_visibility' => 'array',
        'position_budgeted'=>'integer'
    ];

    // protected $appends = [
    //     'active'
    // ];
    
    public function applications()
    {

        return $this->belongsToMany(JobApplication::class);
    }

    public function jobrecruitment()
    {
        return $this->hasMany(Jobrecruitment::class, 'job_id');
    }
    public function jobrecruitmentold()
    {
        return $this->hasMany(Jobrecruitment::class, 'job_id');
    }

      public function anycertification()
    {
        return $this->belongsTo(Certification::class, 'anycertification_id');
    }
    public function categorywithskill()
    {
        return $this->belongsTo(JobCategory::class, 'category_id')->with('jobskill');
    }

     

    public function skills(){
        return $this->hasMany(JobSkill::class, 'job_id')->with('skill');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'location.location']
            ]
        ];
    }

    public static function activeJobs(){
        return Job::where('status', 'active')
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->get();
    }

    public static function activeJobsCount(){
        return Job::where('status', 'active')
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->count();
    }

    public function getActiveAttribute()
    {
        return $this->status === 'active' && $this->start_date <= Carbon::now()->format('Y-m-d') && $this->end_date >= Carbon::now()->format('Y-m-d');
    }

    public function questions(){
        return $this->belongsToMany(Question::class, 'job_questions');
    }

    public function workExperience(){
        return $this->belongsTo(WorkExperience::class, 'work_experience_id');
    }

    public function jobType(){
        return $this->belongsTo(JobType::class, 'job_type_id');
    }
    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function qualification(){
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }

    public function subqualifications(){
        return $this->hasMany(JobSubqualification::class, 'job_id')->with('subqualification');
    }
    public function replacements(){
        return $this->hasMany(Replacementemployee::class, 'job_id');
    }
    /* public function jobassign(){
        return $this->belongsTo(User::class, 'assign_id');
    }
   */
  
     public function jobassign(){
         return $this->hasMany(Jobassign::class, 'job_id');
    }
   
  public function jobassigned(){
        return $this->hasMany(Jobassign::class, 'job_id')->with('belongsassigned');
    }

   /* public function getStartDateAttribute($value)
    {  
        return \Carbon\Carbon::parse($value)->format('d/m/Y');        
    }
    public function getEndDateAttribute($value)
    {  
        return \Carbon\Carbon::parse($value)->format('d/m/Y');        
    }
 */
    public function getPositionBudgetedAttribute($value)
    {  
        return (int)($value);        
    }
    
}
