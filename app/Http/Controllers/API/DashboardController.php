<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EmailSetting;
use App\InterviewSchedule;
use App\Job;
use App\JobApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\SmsSetting;
use App\User;
use Validator;
use Hash;
use Auth;
class DashboardController extends Controller
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
    public function dashboardold(){
       $data['totalOpenings'] = Job::where('status', '=', '1')->count();
        $data['totalApplications'] = JobApplication::count();
        $data['totalHired'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'hired')
            ->count();
        $data['totalRejected'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'rejected')
            ->count();
        $data['newApplications'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'applied')
            ->count();
        $data['onlineexam'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'online exam')           
            ->count();
        $data['condidatestore'] = JobApplication::onlyTrashed()->count();
        $data['interviewround1'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'interview round 1')           
            ->count();
        $data['interviewround2'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'interview round 2')           
            ->count();
        $currentDate = Carbon::now()->format('Y-m-d');

        $data['totalTodayInterview'] = InterviewSchedule::where(DB::raw('DATE(`schedule_date`)'), "$currentDate")
            ->count();
            if(!empty($data)){
                return $this->successJson('Details',200,$data);
            }else{
                return $this->successJson('not found Details',200,$data);
            }
            
    }
 
    public function dashboard(Request $request){
      if($request->rolename=='ERF Generator'){
            $data['totalOpenings'] = Job::where('erfstatus', '!=', '0')->where('user_id',auth()->user()->id)->count();
            $data['totalApplications'] = JobApplication::join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->where('jobs.user_id',auth()->user()->id)
            ->count();
            $data['totalRejected'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'rejected')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->where('jobs.user_id',auth()->user()->id)
            ->count();
           $data['newApplications'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'applied')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->where('jobs.user_id',auth()->user()->id)
            ->count();
        }
        elseif($request->rolename=='Recruiter'){
            $data['totalOpenings'] = Job::join('jobassigns', 'jobassigns.job_id', '=', 'jobs.id')
            ->where('jobassigns.user_id',auth()->user()->id)->where('jobs.erfstatus', '!=', '0')->count();
            $data['totalApplications'] = JobApplication::where('added_by',auth()->user()->id)->count();
            $data['totalRejected'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'rejected')->where('added_by',auth()->user()->id)
            ->count();
          
            $data['condidatestore'] = JobApplication::onlyTrashed()->where('added_by',auth()->user()->id)->count();
            $data['newApplications'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
                ->where('application_status.status', 'applied')
                ->where('added_by',auth()->user()->id)
                ->count();
            $data['onlineexam'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
                ->where('application_status.status', 'Pass') 
                ->where('added_by',auth()->user()->id)          
                ->count();
            //$data['interviewround1']= InterviewSchedule::where('status','interview round 1')->count();
       //$data['interviewround2']= InterviewSchedule::where('status','interview round 2')->count();
           $data['interviewround1'] = InterviewSchedule::select('interview_schedules.id','interview_schedules.status')
                ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')                 
                ->where('interview_schedules.status','interview round 1')
                ->whereNull('job_applications.deleted_at')->count();
    $data['interviewround2'] = InterviewSchedule::select('interview_schedules.id','interview_schedules.status')
                ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')                 
                ->where('interview_schedules.status','interview round 2')
                ->whereNull('job_applications.deleted_at')->count();

        }
       else{

       $data['totalOpenings'] = Job::where('erfstatus', '!=', '0')->count();
        $data['totalApplications'] = JobApplication::count();
        $data['totalHired'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'hired')
            ->count();
        $data['totalRejected'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'rejected')
            ->count();
        $data['newApplications'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'applied')
            ->count();
        $data['onlineexam'] = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
            ->where('application_status.status', 'Pass')           
            ->count();
        
         
        $currentDate = Carbon::now()->format('Y-m-d');
         $data['condidatestore'] = JobApplication::onlyTrashed()->count();
       //$data['interviewround1']= InterviewSchedule::where('status','interview round 1')->count();
       //$data['interviewround2']= InterviewSchedule::where('status','interview round 2')->count();
     $data['interviewround1'] = InterviewSchedule::select('interview_schedules.id','interview_schedules.status')
                ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')                 
                ->where('interview_schedules.status','interview round 1')
                ->whereNull('job_applications.deleted_at')->count();
    $data['interviewround2'] = InterviewSchedule::select('interview_schedules.id','interview_schedules.status')
                ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')                 
                ->where('interview_schedules.status','interview round 2')
                ->whereNull('job_applications.deleted_at')->count();
        $data['totalTodayInterview'] = InterviewSchedule::where(DB::raw('DATE(`schedule_date`)'), "$currentDate")      
            ->count();
      }
            if(!empty($data)){
                return $this->successJson('Details',200,$data);
            }else{
                return $this->successJson('not found Details',200,$data);
            }
            
    }


    public function login(Request $request)
    {
        // Validate user input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'code' => 401,
                'message' => 'Invalid credentials',
                'status' => 0,
            ], 401);
        }
    
        // Retrieve the authenticated user
        $user = $request->user();
    
        // Create a personal access token for the user
        $tokenResult = $user->createToken('CIPL');
        $accessToken = $tokenResult->accessToken;
        // Fetch user's permissions and roles
        $userPermissions = [];
       $userRoles = $user->roles()->with('permissions.permission')->get();
    
        foreach ($userRoles as $role) {
            foreach ($role->permissions as $permission) {
                $userPermissions[] = $permission->permission->name;
            }
        }
    
        // Append user permissions to the user data
        //$user['userPermissions'] = $userPermissions;
    
        // Return the response with access token, user data, and status message
        return response()->json([
            'access_token' => $accessToken,
            //'token_type' => 'Bearer',
            'code' => 200,
            'data' => $user,
            'message' => 'Login successful!',
            'status' => 1,
        ]);
    }
    public function logout(Request $request)
    {
        
        if ($request->user()) {
            $request->user()->token()->revoke();
            return response()->json([
                'code' => 200,
                'status' => 1,
                'message' => 'Logout Successfully.'
            ]);
        }
    }


    // register
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
         ]);
         if($validator->fails())
         {
            $response = ['status'=>false,'message'=>$validator->errors()];
            return response()->json($response);
         }
         $input = $request->all();

         $input['password'] = Hash::make($input['password']);

         $user = User::create($input);

          // Create a personal access token for the user
        // $tokenResult = $user->createToken('CIPL');
        // $accessToken = $tokenResult->accessToken;
       $success['name'] = $user->name;

       $response = ['status'=>true,'data'=>$success,'message'=>'user register successfully'];

       return response()->json($response); 

      
    }
}
