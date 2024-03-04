<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Job;
use App\Skill;
use App\Company;
use App\JobType;
use App\JobSkill;
use App\Question;
use App\JobCategory;
use App\JobLocation;
use App\Helper\Reply;
use App\JobApplication;
use App\WorkExperience;
use App\ApplicationStatus;
use Illuminate\Support\Str; 
use App\Events\JobAlertEvent;
use App\Http\Requests\StoreJob;
use App\Http\Requests\UpdateJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use App\Notifications\NewJobOpening; 
use Illuminate\Support\Facades\Notification; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator; 
use App\Mail\Approvedlink;
use App\Mail\SalaryverifyMail;
use App\Replacementemployee;
use App\JobSubqualification;
use App\Jobrecruitment;
use Mail;
use Auth;
use App\Salarycreation;
use App\Complayer;
use App\Jobcertification;
use App\Jobqualification;
use Illuminate\Support\Facades\Crypt;
class ErfgeneraterController extends Controller
{
    protected function successJson($message, $code, $data = [])
    {
        return response()->json([
            'code' => $code,
            'message' =>  $message,
            'data' => $data,
            'status'=>1,
        ], 200);
    }
    protected function errorJson($message, $code, $data = [])
    {
        return response()->json([
            'code' => $code,
            'message' =>  $message,
            'data' => $data,
            'status'=>0,
        ], $code);
    }

    public function index()
    {
       
       
	  $categories = Job::with(['department','jobrecruitment','jobassigned'])->where('id', '>', '0')->orderBy('id', 'DESC')->get();
            if(!empty($categories)){
                return $this->successJson('Job Details',200,$categories);
            }else{
                return $this->successJson('not found Details',200);
            }
         
      
    }
    
     public function view($id)
    {
       
            $categories = Job::with(['skills','user','department','qualification','jobassign','subqualifications','replacements'])->where('id',$id)->get();
            if(!empty($categories)){
                return $this->successJson('ERF Details',200,$categories);
            }else{
                return $this->successJson('not found Details',200);
            }
        
      
    } 

    public function store(Request $request)
    {

      if(!auth()->user()->cans('add_jobs')){
                return $this->errorJson('Not authenticated to perform this request',403);
            }else{

        $validator = Validator::make($request->all(), [
           
             //'pid' => 'required',
             'pid' => 'required|unique:jobs,pid',
          
        ]);
        if ($validator->fails()) {
           return response()->json(['error'=>$validator->errors()], 422);
        }
        
        try {
            $jobs = Job::latest()->first();
            $prefix="C".date("y").'I';
            if ($jobs != null) {
                $preid =  $jobs->erf_id;
                $preidg = explode($prefix,$preid)[1]; 

                $counter = str_pad((int)$preidg+1, 4 ,"0",STR_PAD_LEFT);
                $new_prefix = $counter;

            }
            else{

                $new_prefix = '0001';
            }
          
            \DB::beginTransaction();
            $job = new Job();
            $job->billable_type = $request->billable_type;
            $job->pid = $request->pid;
          
            $job->recruitment=$request->recruitment;
            $job->department_id = $request->department_id;
            $job->recruitment_type=$request->recruitment_type;
            $job->billable_type=$request->billable_type;
            $job->project_name=$request->project_name;
            $job->erf_id=$prefix.$new_prefix;
            $job->user_id=auth()->user()->id;          
             $job->approved=$request->approved;
            if($request->approved=='yes'){
                $job->status='0';
               // Mail::to($myEmail)->send(new Approvedlink($details));

            }elseif($request->recruitment_type=='inhouse'){
                $job->status='0';
                
        	//Mail::to($myEmail)->send(new Approvedlink($details));

            }else{
                $job->status='1';
            }
            $jobData = $job->save();
    
            \DB::commit();
            return $this->successJson('ERF Form Generated Successfully',200,$job);
            
        } catch (\Exception $th) {
            \DB::rollBack();
            return $this->errorJson('something else wrong',403,$th->getMessage());
        }
       }
    }

   public function mail(){
      $data = array('name'=>"Our Code World");
        // Path or name to the blade template to be rendered
        $template_path = 'hello';

        Mail::send(['text'=> $template_path ], $data, function($message) {
            // Set the receiver and subject of the mail.
            $message->to('akhilesh1.epic@gmail.com.com', 'Receiver Name')->subject('Laravel First Mail');
            // Set the sender
            $message->from('noida.cipl1@gmail.com','Our Code World');
        });

        return "Basic email sent, check your inbox.";

   }


   function Leads(Request $request,$id){
    
    $job=Job::findOrFail($id);
    if($job){
        $validator = Validator::make($request->all(), [           
            'category_id'         => 'required',           
            'location' =>'required',  
           "degination"=> "required",
            "level"=> "required",
 	   "skills"=> "array",
           "level"=> "required",
          "position_budgeted"=>'required',
	 'total_positions'=>'required',
           'reporting_team'=>'required',
          'project_manager'=>'required',
         'additionalqualification'=>'array',
           "skill_id.0"      => "required",
          'total_experience'=>'required',
          'relevent_exp'=>'required',
          'responsibility'=>'required',
          'qualification.0'=>'required',
          'additionalqualification.0'=>'required',
          'start_date' => 'required|date',
             'end_date' => 'required|date',

       ]);
       if ($validator->fails()) {
           return response()->json(['error'=>$validator->errors()], 422);
       }
       try {
       \DB::beginTransaction();
       $requipment=new Jobrecruitment();
       $requipment->job_id = $job->id;
       $requipment->category_id = $request->category_id;
       $requipment->qualification_id = $request->qualification_id;
       $requipment->anycertification_id = $request->anycertification_id;
       $requipment->degination = $request->degination;
       $requipment->level = $request->level;
       $requipment->location=$request->location;
          $requipment->start_date = $request->start_date;
            $requipment->end_date = $request->end_date;
       $requipment->project_manager = $request->project_manager;
       $requipment->reporting_team = $request->reporting_team;
       $requipment->position_budgeted = $request->position_budgeted;
       $requipment->relevent_exp = $request->relevent_exp;  
       $requipment->responsibility=$request->responsibility;
       $requipment->prerequisite = $request->prerequisite;
       $requipment->total_experience=$request->total_experience;
      $requipment->total_positions=$request->total_positions;
       $requipment->created_by_id=auth()->user()->id;
       $requipment->jd_id = $request->jd_id;
        if($request->hasFile('scopeofwork')) {
            $path = public_path('Erf/scopeofwork/');
            if(!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image = $request->file('scopeofwork');
            $name = time().'.'.$image->getClientOriginalExtension();            
            $image->move($path, $name);
            $requipment->scopeofwork='public/Erf/scopeofwork/'.$name;
        }
        $requipment->save();
        if ($request->skill_id !='Other') {
            JobSkill::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();

            foreach ($request->skill_id as $skill) {
                $jobSkill = new JobSkill();
                $jobSkill->skill_id = $skill;
                $jobSkill->jobrecruitment_id=$requipment->id;
                $jobSkill->job_id = $job->id;
                $jobSkill->save();
            }
        }else{
            $requipment->other_skill=$request->other_skill;
        }
          if(!is_null($request->qualification)){
            Jobqualification::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->qualification as $qskill) {
                $qualification = new Jobqualification();
                $qualification->qualification_id = $qskill;
                $qualification->job_id = $job->id;
                $qualification->jobrecruitment_id=$requipment->id;
                $qualification->save();
            }
        }

        if(!is_null($request->additionalqualification)){
            JobSubqualification::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->additionalqualification as $qskill) {
                $qualification = new JobSubqualification();
                $qualification->subqualification_id = $qskill;
                $qualification->job_id = $job->id;
                $qualification->jobrecruitment_id=$requipment->id;
                $qualification->save();
            }
        }
        
     if(!is_null($request->anycertification)){
            Jobcertification::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->anycertification as $qskill) {
                $certification = new Jobcertification();
                $certification->anycertification_id = $qskill;
                $certification->job_id = $job->id;
                $certification->jobrecruitment_id=$requipment->id;
                $certification->save();
            } 
        }

         if($request->employee[0]['emp_name']!='null'){
 
            Replacementemployee::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->employee as $key=>$value) {
                $replacement = new Replacementemployee();
                $replacement->emp_name = $value['emp_name'];
                $replacement->emp_code=$value['emp_code'];
                $replacement->resign_date=Carbon::parse($value['resign_date'])->format('Y-m-d');
                $replacement->last_working_date=Carbon::parse($value['last_working_date'])->format('Y-m-d');
                $replacement->job_id = $job->id;
                $replacement->jobrecruitment_id=$requipment->id;
                $replacement->save();
            }
         }
          \DB::commit();
           return $this->successJson('ERF Form Generated Successfully',200,$requipment);
       }
        catch (\Exception $e) {
        \DB::rollBack();
        return $this->errorJson('something else wrong',403,$e->getMessage());
       }
    }
   }



   function Leadsupdate(Request $request,$id,$leadid){
    
    $job=Job::findOrFail($id);
    if($job){
        $validator = Validator::make($request->all(), [           
         'category_id'         => 'required',           
            'location' =>'required',  
           "degination"=> "required",
            "level"=> "required",
 	   "skills"=> "array",
           "level"=> "required",
          "position_budgeted"=>'required',
	 'total_positions'=>'required',
           'reporting_team'=>'required',
          'project_manager'=>'required',
         'additionalqualification'=>'array',
           "skill_id.0"      => "required",
          'total_experience'=>'required',
          'relevent_exp'=>'required',
          'responsibility'=>'required',
          'qualification.0'=>'required',
          'additionalqualification.0'=>'required',
          'start_date' => 'required|date',
             'end_date' => 'required|date',

       ]);
       if ($validator->fails()) {
           return response()->json(['error'=>$validator->errors()], 422);
       }
       try {
       \DB::beginTransaction();
       $requipment=Jobrecruitment::findOrFail($leadid);
       $requipment->job_id = $job->id;
       $requipment->category_id = $request->category_id;
       $requipment->qualification_id = $request->qualification_id;
       $requipment->anycertification_id = $request->anycertification_id;
       $requipment->degination = $request->degination;
       $requipment->level = $request->level;
       $requipment->location=$request->location;
          $requipment->start_date = $request->start_date;
            $requipment->end_date = $request->end_date;
       $requipment->project_manager = $request->project_manager;
       $requipment->reporting_team = $request->reporting_team;
       $requipment->position_budgeted = $request->position_budgeted;
       $requipment->relevent_exp = $request->relevent_exp;  
       $requipment->responsibility=$request->responsibility;
       $requipment->prerequisite = $request->prerequisite;
       $requipment->total_experience=$request->total_experience;
      $requipment->total_positions=$request->total_positions;
       $requipment->created_by_id=auth()->user()->id;
       $requipment->jd_id = $request->jd_id;
        if($request->hasFile('scopeofwork')) {
            $path = public_path('Erf/scopeofwork/');
            if(!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image = $request->file('scopeofwork');
            $name = time().'.'.$image->getClientOriginalExtension();            
            $image->move($path, $name);
            $requipment->scopeofwork='public/Erf/scopeofwork/'.$name;
        }
        $requipment->save();
        if ($request->skill_id !='Other') {
            JobSkill::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();

            foreach ($request->skill_id as $skill) {
                $jobSkill = new JobSkill();
                $jobSkill->skill_id = $skill;
                $jobSkill->jobrecruitment_id=$requipment->id;
                $jobSkill->job_id = $job->id;
                $jobSkill->save();
            }
        }else{
            $requipment->other_skill=$request->other_skill;
        }
          if(!is_null($request->qualification)){
            Jobqualification::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->qualification as $qskill) {
                $qualification = new Jobqualification();
                $qualification->qualification_id = $qskill;
                $qualification->job_id = $job->id;
                $qualification->jobrecruitment_id=$requipment->id;
                $qualification->save();
            }
        }

        if(!is_null($request->additionalqualification)){
            JobSubqualification::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->additionalqualification as $qskill) {
                $qualification = new JobSubqualification();
                $qualification->subqualification_id = $qskill;
                $qualification->job_id = $job->id;
                $qualification->jobrecruitment_id=$requipment->id;
                $qualification->save();
            }
        }
        
     if(!is_null($request->anycertification)){
            Jobcertification::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->anycertification as $qskill) {
                $certification = new Jobcertification();
                $certification->anycertification_id = $qskill;
                $certification->job_id = $job->id;
                $certification->jobrecruitment_id=$requipment->id;
                $certification->save();
            } 
        }

         if($request->employee[0]['emp_name']!='null'){
 
            Replacementemployee::where(['job_id'=>$job->id,'jobrecruitment_id'=>$requipment->id])->delete();
            foreach ($request->employee as $key=>$value) {
                $replacement = new Replacementemployee();
                $replacement->emp_name = $value['emp_name'];
                $replacement->emp_code=$value['emp_code'];
                $replacement->resign_date=Carbon::parse($value['resign_date'])->format('Y-m-d');
                $replacement->last_working_date=Carbon::parse($value['last_working_date'])->format('Y-m-d');
                $replacement->job_id = $job->id;
                $replacement->jobrecruitment_id=$requipment->id;
                $replacement->save();
            }
         }
          \DB::commit();
           return $this->successJson('Erf Form Generated Successfully',200,$requipment);
       }
        catch (\Exception $e) {
        \DB::rollBack();
        return $this->errorJson('something else wrong',403,$e->getMessage());
       }
    }
   }



 public function leaddata(Request $request,$id=null){
    if(!empty($id)){
        $data=Jobrecruitment::with(['job','department','jd','category','skills','qualification','subqualifications','replacements','anycertification','assignBy','assigned'])->where('job_id',$id)->get();
       
    }else{
       $data=Jobrecruitment::with(['job','department','jd','category','skills','qualification','subqualifications','replacements','anycertification','assignBy','assigned'])->get();
       
    }

      return $this->successJson('ERF list Successfully',200,$data);
 }

public function approveallinkold($id){
        $jobdetails = Job::find($id);
       
            if(!empty($jobdetails)){
                $myEmail = 'roshni.bansod@cipl.org.in';
               // $url='https://cipcrm.org.in/erfapproval/'.$jobdetails->erf_id; 
              $url='https://cipcrm.org.in/erfapproval/'.$jobdetails->erf_id; 

                $details = [
                            'title' => 'New ERF for approval',
                            'url' => $url
                            ]; 

                $jobdetails->status='2';
                $jobdetails->save();
                //print_r($jobdetails);die();
                Mail::to($myEmail)->send(new Approvedlink($details));
            
                return $this->successJson('Send Mail Successfully',200,$jobdetails);
            }else{
                return $this->successJson('not found Details',200);
            }
}

public function approveallink($id){
        $jobdetails = Job::find($id);
        $detailsposistion = Jobrecruitment::with(['job','jd','department','category','skills','qualification','subqualifications','replacements','anycertification','assignBy','assigned'])->where('job_id',$id)->get();
              
            if(!empty($jobdetails)){
               // $myEmail = 'akhilesh1.epic@gmail.com';
                 $myEmail = 'vinodkumar@cipl.org.in';
               // $url='https://cipcrm.org.in/erfapproval/'.$jobdetails->erf_id; 
              $url='https://cipcrm.org.in/erfapproval/'.$jobdetails->erf_id; 

                $details = [
                            'title' => 'New ERF for approval',
                            'detailsposistion'=>$detailsposistion->toArray(),
                            'jobdetails'=>$jobdetails,
                            'url' => $url
                            ]; 

                $jobdetails->status='2';
                $jobdetails->save();
                //print_r($jobdetails);die();
                Mail::to($myEmail)->send(new Approvedlink($details));
            
                return $this->successJson('Mail Sent Successfully',200,$jobdetails);
            }else{
                return $this->successJson('not found Details',200);
            }
}

   function Salarycreated(Request $request ,$id){

        $job=JobApplication::with(['interviewround2'])->findOrFail($id);
        // return $this->successJson('Salary Created Successfully',200,$job);
        if($job){
          $validator = Validator::make($request->all(), [           
            'ctc'         => 'required',           
            

       ]);
       if ($validator->fails()) {
           return response()->json(['error'=>$validator->errors()], 422);
       }

        try {
       \DB::beginTransaction();
        
        $requipment=new Salarycreation;
        $requipment->job_application_id  =$id;
        $requipment->employeeCode = $request->employeeCode;
        $requipment->location = $request->location;
        $requipment->salaryType = $request->salaryType;
        $requipment->name = $request->name;
        $requipment->state = $request->state;
        $requipment->designation=$request->designation;
        $requipment->effectiveDate = $request->effectiveDate;
        $requipment->dateOfJoining = $request->dateOfJoining;
        $requipment->ctc = $request->ctc;
        $requipment->basicMonthly = $request->basicMonthly;
        $requipment->basicAnnual=$request->basicAnnual;
        $requipment->hrmMonthly = $request->hrmMonthly;
        $requipment->hrmAnnual = $request->hrmAnnual;
        $requipment->specialMonthly = $request->specialMonthly;
        $requipment->spciealAnnual=$request->spciealAnnual;
        $requipment->pfMonthly = $request->pfMonthly;
        $requipment->pTaxMonthly = $request->pTaxMonthly;
        $requipment->totalDeductionAnnually = $request->totalDeductionAnnually;
        $requipment->pfEMonthly=$request->pfEMonthly;
        $requipment->pfEAnnually = $request->pfEAnnually;
        $requipment->pfAAnnually = $request->pfAAnnually;
        $requipment->eStateInsuranceMonthly = $request->eStateInsuranceMonthly;
        $requipment->eStateInsuranceAnnually=$request->eStateInsuranceAnnually;
        $requipment->gratuityMonthly = $request->gratuityMonthly;
        $requipment->gratuityAnnually = $request->gratuityAnnually;
        $requipment->ltaMonthly = $request->ltaMonthly;
        $requipment->ltaAnnually=$request->ltaAnnually;
        $requipment->insuranceMonthly = $request->insuranceMonthly;
        $requipment->insuranceAnnually = $request->insuranceAnnually;
        $requipment->fixedCtcMonthly = $request->fixedCtcMonthly;
        $requipment->fixedCtcAnnually=$request->fixedCtcAnnually;
        $requipment->totalCTC = $request->totalCTC;
        $requipment->netTakeHome = $request->netTakeHome;
        $requipment->grossAmount = $request->grossAmount;
        $requipment->pTaxAnnually =$request->pTaxAnnually;
        $requipment->totalDeductionMonthly =$request->totalDeductionMonthly;
        $requipment->pfAMonthly =$request->pfAMonthly; 
        $requipment->retension=$request->retension;
        $requipment->retensionBonus=$request->retensionBonus;
        $requipment->save();
         if(!is_null($request->complayers)){
            foreach ($request->complayers as $key=>$value) {
                if($value['email']){

                $complayer = new Complayer();
                $complayer->email = $value['email'];
                $complayer->job_application_id = $id;
                $complayer->salarycreation_id=$requipment->id;
                $complayer->save();
                $url='https://cipcrm.org.in/salaryverification/'.Crypt::encryptString($complayer->id); 
                $details = [
                            'title' => 'Salary verification link',
                            'url' => $url,	
			     
                            ]; 
                Mail::to($value['email'])->send(new SalaryverifyMail($details,$job));
                $complayer->save();
              }
            }
        }    
     
          $requipment->status ='0'; 
        $requipment->save();          
         
         $job->status_id=10;
        $job->save();
           \DB::commit();
            return $this->successJson('Salary Created Successfully',200,$requipment);
        }
         catch (\Exception $e) {
         \DB::rollBack();
         return $this->errorJson('something else wrong',403,$e->getMessage());
        }
    }else{
        return $this->errorJson('Job Application not founds',404);
    }
      
    }

    function Salaryget($id){
 

          $jobApplications = Salarycreation::with('jobapplication','complayer')->where('job_application_id',$id)->get();          
            if(!empty($jobApplications)){
                return $this->successJson('Job Applications Details',200,$jobApplications);
            }else{
                return $this->successJson('not found Details',404);
            }
     }

   function Salarygeturl(Request $request,$id){
          $id = Crypt::decryptString($id);  
 
          
            $jobApplications = Complayer::with(['salarycreation','jobapplication'])->where('id',$id)->get();
              
                if(!empty($jobApplications)){
                    return $this->successJson('Job Applications Details',200,$jobApplications);
                }else{
                    return $this->successJson('not found Details',404);
                }
    }

function SalaryUpdate(Request $request,$id){
        $requipment=Salarycreation::find($id);
        if($requipment){
            try {
                \DB::beginTransaction();
                 $requipment->job_application_id  = $requipment->job_application_id;
                $requipment->employeeCode = $request->employeeCode;
                $requipment->location = $request->location;
                $requipment->salaryType = $request->salaryType;
                $requipment->name = $request->name;
                $requipment->state = $request->state;
                $requipment->designation=$request->designation;
                $requipment->effectiveDate = $request->effectiveDate;
                $requipment->dateOfJoining = $request->dateOfJoining;
                $requipment->ctc = $request->ctc;
                $requipment->basicMonthly = $request->basicMonthly;
                $requipment->basicAnnual=$request->basicAnnual;
                $requipment->hrmMonthly = $request->hrmMonthly;
                $requipment->hrmAnnual = $request->hrmAnnual;
                $requipment->specialMonthly = $request->specialMonthly;
                $requipment->spciealAnnual=$request->spciealAnnual;
                $requipment->pfMonthly = $request->pfMonthly;
                $requipment->pTaxMonthly = $request->pTaxMonthly;
                $requipment->totalDeductionAnnually = $request->totalDeductionAnnually;
                $requipment->pfEMonthly=$request->pfEMonthly;
                $requipment->pfEAnnually = $request->pfEAnnually;
        
                $requipment->pfAAnnually = $request->pfAAnnually;
                $requipment->eStateInsuranceMonthly = $request->eStateInsuranceMonthly;
                $requipment->eStateInsuranceAnnually=$request->eStateInsuranceAnnually;
                $requipment->gratuityMonthly = $request->gratuityMonthly;
                $requipment->gratuityAnnually = $request->gratuityAnnually;
                $requipment->ltaMonthly = $request->ltaMonthly;
                $requipment->ltaAnnually=$request->ltaAnnually;
                $requipment->insuranceMonthly = $request->insuranceMonthly;
        
                $requipment->insuranceAnnually = $request->insuranceAnnually;
                $requipment->fixedCtcMonthly = $request->fixedCtcMonthly;
                $requipment->fixedCtcAnnually=$request->fixedCtcAnnually;
                $requipment->totalCTC = $request->totalCTC;
                $requipment->netTakeHome = $request->netTakeHome;
                $requipment->grossAmount = $request->grossAmount;
                 $requipment->pTaxAnnually =$request->pTaxAnnually;
                $requipment->totalDeductionMonthly =$request->totalDeductionMonthly;
                $requipment->pfAMonthly =$request->pfAMonthly; 
                $requipment->retension=$request->retension;
                $requipment->retensionBonus=$request->retensionBonus;
                $requipment->save();
                
                $requipment->status ='1'; 
                
                $requipment->save();
                $job=JobApplication::findOrFail($requipment->job_application_id);
                if($request->status_id){
                 $job->status_id=$request->status_id;
                 $job->cancel_reason=$request->cancel_reason;
		}else{
                 $job->status_id=15;
                  //$job->cancel_reason=$request->cancel_reason;
		}
               
                $job->save();
                 \DB::commit();
                    return $this->successJson('Updated Successful',200,$requipment);
                }
                 catch (\Exception $e) {
                 \DB::rollBack();
                 return $this->errorJson('something else wrong',403,$e->getMessage());
                }

        }else{
            return $this->errorJson('page not found',404);
        }
       
    }

    public function updatejobStatus(Request $request,$id){
        $jobdetails = Job::find($id);

        $jobdetails->erfstatus=$request->status;
        $jobdetails->save();
        return $this->successJson('Update Successful',200,$jobdetails);
    }
  
 public function leaddatasearchold(Request $request){
        
            $jobApplications=Jobrecruitment::with(['job','category','skills','qualification','subqualifications','replacements','anycertification','assignBy','assigned','jobapplication']);
            if ($request->pid != '' || $request->pid !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('pid', $request->pid);
            }); 
            }
            if ($request->recruitment_type != '' || $request->recruitment_type !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('recruitment_type', $request->recruitment_type);
            }); 
            }
            if ($request->billable_type != '' || $request->billable_type !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('billable_type', $request->billable_type);
            }); 
            }
          if ($request->qualification_id!= '' || $request->qualification_id !=null) {
            $jobApplications = $jobApplications->whereHas('qualification', function ($query) use ($request)
            {
                    $query->Where('qualification_id', $request->qualification_id);
            }); 
            }

            if ($request->project_name != '' || $request->project_name !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('project_name', $request->project_name);
            }); 
            }
            if ($request->location != '' || $request->location !=null) {
                $jobApplications = $jobApplications->Where('location', $request->location);
            }

            if ($request->department_id != '' || $request->department_id !=null) {
                $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
                {
                        $query->Where('department_id', $request->department_id);
                }); 
            }
            if ($request->category_id != '' || $request->category_id !=null) {


                $jobApplications = $jobApplications->Where('category_id', $request->category_id);
            }

            

            if ((!is_null($request->min_budgeted) && !is_null($request->max_budgeted))) {
                // fetch all between min & max values
                $jobApplications = $jobApplications->whereBetween('position_budgeted',[$request->min_budgeted, $request->max_budgeted]);
            }
            // if just min_value is available (is not null)
            elseif (! is_null($request->min_budgeted)) {
                // fetch all greater than or equal to min_value
                $jobApplications = $jobApplications->where('position_budgeted', '>=', $request->min_budgeted);
            }
            // if just max_value is available (is not null)
            elseif (! is_null($request->max_budgeted)) {
                // fetch all lesser than or equal to max_value
                $jobApplications = $jobApplications->where('position_budgeted', '<=', $request->max_budgeted);
            } 
           elseif($request->project_manager != '' || $request->project_manager !=null) {
                   $jobApplications = $jobApplications->where('project_manager', 'like', '%' .$request->project_manager. '%');

            }
            
            if ($request->start_date != '' || $request->start_date !=null) {
                $jobApplications = $jobApplications->whereDate('start_date','>=',$request->start_date);
            }

            if ($request->end_date != '' || $request->end_date !=null) {
                 $jobApplications = $jobApplications->whereDate('end_date','<=',$request->end_date);

                
            }
            if ($request->user_id != '' || $request->user_id !=null) {
                $jobApplications = $jobApplications->whereHas('assigned', function ($query) use ($request)
                {
                         $query->where('user_id', '<=', $request->user_id);
                      

                }); 
            }
            if (! is_null($request->status)) {
                // fetch all lesser than or equal to max_value
                $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
                {
                         $query->where('erfstatus',  $request->status);
                      

                }); 
                 
            }

           // $date = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
            $jobApplications=$jobApplications->get();
            
          return $this->successJson('ERF Form list Successfully',200,$jobApplications);
        }



 public function leaddatasearch(Request $request){
        
            $jobApplications=Jobrecruitment::with(['job','category','skills','qualification','subqualifications','replacements','anycertification','assignBy','assigned','jobapplication']);
            if ($request->pid != '' || $request->pid !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('pid', $request->pid);
            }); 
            }
            if ($request->recruitment_type != '' || $request->recruitment_type !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('recruitment_type', $request->recruitment_type);
            }); 
            }
            if ($request->billable_type != '' || $request->billable_type !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('billable_type', $request->billable_type);
            }); 
            }
          if ($request->qualification_id!= '' || $request->qualification_id !=null) {
            $jobApplications = $jobApplications->whereHas('qualification', function ($query) use ($request)
            {
                    $query->Where('qualification_id', $request->qualification_id);
            }); 
            }

            if ($request->project_name != '' || $request->project_name !=null) {
            $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
            {
                    $query->Where('project_name', 'like', '%' .$request->project_name. '%');
            }); 
            }
            if ($request->location != '' || $request->location !=null) {
                $jobApplications = $jobApplications->Where('location', $request->location);
            }

            if ($request->department_id != '' || $request->department_id !=null) {
                $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
                {
                        $query->Where('department_id', $request->department_id);
                }); 
            }
            if ($request->category_id != '' || $request->category_id !=null) {


                $jobApplications = $jobApplications->Where('category_id', $request->category_id);
            }

            

           if ($request->max_budgeted !='' || $request->max_budgeted !=null) {
                
                $jobApplications = $jobApplications->where('position_budgeted', '<=', $request->max_budgeted);
            }
           elseif($request->project_manager != '' || $request->project_manager !=null) {
                $jobApplications = $jobApplications->where('project_manager', 'like', '%' .$request->project_manager. '%');
            }
            
            if ($request->start_date != '' || $request->start_date !=null) {
           $jobApplications = $jobApplications->whereDate('start_date','>=',$request->start_date);
                   }

            if ($request->end_date != '' || $request->end_date !=null) {
               $jobApplications = $jobApplications->whereDate('end_date','<=',$request->end_date);

            }
            if ($request->user_id != '' || $request->user_id !=null) {
                $jobApplications = $jobApplications->whereHas('assigned', function ($query) use ($request)
                {
                         $query->where('user_id', '<=', $request->user_id);
                      

                }); 
            }
            if (! is_null($request->status)) {
                // fetch all lesser than or equal to max_value
                $jobApplications = $jobApplications->whereHas('job', function ($query) use ($request)
                {
                         $query->where('erfstatus',  $request->status);
                      

                }); 
                 
            }

           // $date = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
            $jobApplications=$jobApplications->get();
            
          return $this->successJson('ERF Form list Successfully',200,$jobApplications);
        }


}
  