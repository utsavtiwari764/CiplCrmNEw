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
use App\Helper\Files;
use App\Mail\Approvedlink;
use Mail;
use Illuminate\Support\Facades\Validator;class ErfgeneraterController extends Controller
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
       
        // $this->totalJobs = Job::count();
        // $this->locations = JobLocation::all();
        // $this->activeJobs = Job::where('status', 'active')->count();
        // $this->inactiveJobs = Job::where('status', 'inactive')->count();
        
            $categories = Job::with(['categorywithskill','location'])->where('id', '>', '0')->get();
            if(!empty($categories)){
                return $this->successJson('Job Details',200,$categories);
            }else{
                return $this->successJson('not found Details',200);
            }
        } 
      
    }

    public function mail()
    {
        $data = array('name'=>"Our Code World");
        // Path or name to the blade template to be rendered
        $template_path = 'email_template';

        Mail::send(['text'=> $template_path ], $data, function($message) {
            // Set the receiver and subject of the mail.
            $message->to('anyemail@emails.com', 'Receiver Name')->subject('Laravel First Mail');
            // Set the sender
            $message->from('mymail@mymailaccount.com','Our Code World');
        });

        return "Basic email sent, check your inbox.";
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'job_requirement' => 'required',
             'start_date' => 'required|date',
             'end_date' => 'required|date',
             'pid' => 'required',
             'scopeofwork' => 'mimes:jpeg,png,jpg,pdf|max:2048',
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
            \DB::beginTransaction();
            $job = new Job();
        
            $job->slug = null;
            $job->job_description = $request->job_description;
            $job->job_requirement = $request->job_requirement;
            $job->total_positions = $request->total_positions;
            $job->pid = $request->pid;
            $job->location_id = $request->location_id;
            $job->category_id = $request->category_id;
            $job->start_date = $request->start_date;
            $job->end_date = $request->end_date;
           // $job->job_type_id = $request->job_type_id;
            $job->departments = $request->departments;
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
            $job->qualification=$request->qualification;
            $job->additionalqualification=$request->additionalqualification;
            
            $job->anycertification=$request->anycertification;
            $job->recruitment_type=$request->recruitment_type;
            $job->recruitment=$request->recruitment;
	    $job->approved=$request->approved;
            $job->erf_id=$prefix.$new_prefix;
            if($request->approved=='yes'){
                $job->status='0';
		   $myEmail = 'akhilesheng2012@gmail.com';
   
        	$details = [
            		'title' => 'Mail Demo from CIPL',
            		'url' => 'http://cipcrm.org.in/'
        		];
  
        		Mail::to($myEmail)->send(new Approvedlink($details));
            }elseif($request->recruitment_type=='inhouse'){
                $job->status='0';

            }else{
                $job->status='1';
            }
            if ($request->hasFile('scopeofwork')) {
                       $job->scopeofwork = Files::uploadLocalOrS3($request->scopeofwork,'profile');
                
            }
            $jobData = $job->save();

            if (!is_null($request->skill_id)) {
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
            
            \DB::commit();
            return $this->successJson('Erf Form Generated Successfully',200,$job);
            
        } catch (Exception  $th) {
            \DB::rollBack();
            return $this->errorJson('something else wrong',403,$th);
        }
    }

}
 