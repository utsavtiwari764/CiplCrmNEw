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
use Illuminate\Support\Facades\DB; 
use App\Notifications\NewJobOpening; 
use Illuminate\Support\Facades\Notification; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Mail\Approvedlink;
use Carbon\Carbon;
use App\JobSubqualification;
use Mail;
use Auth;
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
        
         
        if(!auth()->user()->cans('view_jobs')){
                return $this->errorJson('Not authenticated to perform this request',403);
            }else{
       
                
             $categories = Job::with(['category','skills','user','department','qualification','subqualifications','replacements'])->where('id', '>', '0')->orderBy('id', 'DESC')->get();
            if(!empty($categories)){
                return $this->successJson('Job Details',200,$categories);
            }else{
                return $this->successJson('not found Details',200);
            }
        } 
      
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
             'start_date' => 'required|date',
             'end_date' => 'required|date',
             'pid' => 'required',
          
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
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
            // Start Transaction
  //return $this->successJson('Erf Form Generated Successfully',200,$request->all());

            \DB::beginTransaction();
        $job = new Job();
           
            $job->slug = null;
            $job->job_description = $request->job_description;
            $job->billable_type = $request->billable_type;
            $job->total_positions = $request->total_positions;
            $job->pid = $request->pid;
            $job->location = $request->location;
            $job->category_id = $request->category_id;
            $job->qualification_id=$request->qualification_id;
            $job->start_date = $request->start_date;
            $job->end_date = $request->end_date;
           // $job->job_type_id = $request->job_type_id;
            $job->department_id = $request->department_id;
            $job->degination=$request->degination;
            $job->level = $request->level;
            $job->project_manager=$request->project_manager;
            $job->reporting_team=$request->reporting_team;
            $job->project_name=$request->project_name;
            $job->position_budgeted=$request->position_budgeted;
            $job->work_experience=$request->work_experience;
            $job->relevent_exp=$request->relevent_exp;
            $job->responsibility=$request->responsibility;
            $job->prerequisite=$request->prerequisite;
           
            $job->anycertification=$request->anycertification;
            $job->recruitment_type=$request->recruitment_type;
            $job->recruitment=$request->recruitment;
            $job->user_id=auth()->user()->id;
            $job->approved=$request->approved;
            $job->erf_id=$prefix.$new_prefix;
            $link=$prefix.$new_prefix;
            $myEmail = 'akhilesh1.epic@gmail.com';
             $link=$prefix.$new_prefix;
            $myEmail = 'akhilesh1.epic@gmail.com';
            $url='https://cipcrm.org.in/erfapproval/'.$link; 
            $details = [
               'title' => 'Mail from CIPL CRM Approval',
               'url' => $url
           ];
            if($request->approved=='yes'){
                $job->status='0';
                Mail::to($myEmail)->send(new Approvedlink($details));

            }elseif($request->recruitment_type=='inhouse'){
                $job->status='0';
                
        	Mail::to($myEmail)->send(new Approvedlink($details));

            }else{
                $job->status='1';
            }
            if ($request->hasFile('scopeofwork')) {
                $path = public_path('Erf/scopeofwork/');
                if (!File::isDirectory($path)) {

                    File::makeDirectory($path, 0777, true, true);
                }
                $image = $request->file('scopeofwork');
                $name = time().'.'.$image->getClientOriginalExtension();
                
                $image->move($path, $name);
                $job->scopeofwork='public/Erf/scopeofwork/'.$name;
            }
            $jobData = $job->save();

            if ($request->skill_id !='Other') {
                JobSkill::where('job_id', $job->id)->delete();

                foreach ($request->skill_id as $skill) {
                    $jobSkill = new JobSkill();
                    $jobSkill->skill_id = $skill;
                    $jobSkill->job_id = $job->id;
                    $jobSkill->save();
                }
            }else{
                $job->other_skill=$request->other_skill;
            }
	    if(!is_null($request->additionalqualification)){
                foreach ($request->additionalqualification as $qskill) {
                    $qualification = new JobSubqualification();
                    $qualification->subqualification_id = $qskill;
                    $qualification->job_id = $job->id;
                    $qualification->save();
                }
            }
           
            
            \DB::commit();
            return $this->successJson('Erf Form Generated Successfully',200,$job);
            
        } catch (\Throwable $th) {
            \DB::rollBack();
            return $this->errorJson('something else wrong',403,$th);
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

}
 