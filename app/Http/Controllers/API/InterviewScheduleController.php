<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ApplicationStatus;
use App\Helper\Reply;

use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\InterviewSchedule\UpdateRequest;
use App\InterviewSchedule;
use App\InterviewScheduleEmployee;
use App\JobApplication;
use App\Notifications\CandidateNotify;
use App\Notifications\CandidateReminder;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\EmployeeResponse;
use App\Notifications\ScheduleInterview;
use App\Notifications\ScheduleInterviewStatus;
use App\Notifications\ScheduleStatusCandidate;
use App\Traits\ZoomSettings;
use MacsiDigital\Zoom\Facades\Zoom;
use App\ScheduleComments;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Mail\CandidateScheduleInterviewMail;
use Mail;
use App\EmailSetting;
use App\SmtpSetting; 
use App\Notifications\MyFirstNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Notifications\InterviewRatingNotification;
class InterviewScheduleController extends Controller
{
    protected function errorJson($message, $code, $data = [])
    {
        return response()->json([
            'code' => $code,
            'message' =>  $message,
            'data' => $data,
            'status'=>0, 
        ], $code);
    }
    protected function successJson($message, $code, $data = [])
    {
        return response()->json([
            'code' => $code,
            'message' =>  $message,
            'data' => $data,
            'status'=>1,
        ], 200);
    } 

    
        public function view(Request $request)
    {
        
        if(!auth()->user()->cans('add_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $data['candidates'] = JobApplication::all();
        $data['users'] = User::all();
        
        return $this->successJson('Details',200,$data);
        }
    }


    public function store(Request $request)
    {
        if(!auth()->user()->cans('add_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{

         $validator = Validator::make($request->all(), [ 
                  "candidates"    => "required",
                "employees.0"      => "required",
                "scheduleDate"    => "required",
                "scheduleTime"    => "required",            
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 422);            
            }
         try{
           \DB::beginTransaction();
        $dateTime =  $request->scheduleDate . ' ' . $request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);
        
            if($request->interview_type == 'online'){
            $data = $request->all();
         
            //$data['meeting_name'] = $request->meeting_title;
            $data['start_date_time'] = $dateTime;
            $data['meetingurl']=$request->meetingurl;
           // $meeting = $meeting->create($data);
           $data['interview_type']=$request->status;
            $meetings = $data;
 
        } 
        else{
             
           $data['start_date_time'] = $dateTime;
            $data['meetingurl']='null';
           $data['interview_type']=$request->status;
            $meetings = $data;

        }  
            // store Schedule
            $interviewSchedule = new InterviewSchedule();
            $interviewSchedule->job_application_id = $request->candidates;
            $interviewSchedule->schedule_date = $dateTime;
            //$interviewSchedule->interview_type = ($request->has('interview_type')) ? $request->interview_type : 'offline';
             $interviewSchedule->interview_type = $request->interview_type;
            $interviewSchedule->meeting_id = ($request->meeting_id!= '') ? $request->meeting_id: null;
            $interviewSchedule->meetingurl = ($request->meetingurl!= '') ? $request->meetingurl: null;
            $interviewSchedule->user_accept_status='accept';
            $interviewSchedule->status = $request->status;
            $interviewSchedule->save();

            // Update Schedule Status
            $jobApplication = $interviewSchedule->jobApplication;
            if($request->status=='interview round 1'){
            $jobApplication->status_id = 3;
           }else if($request->status=='interview round 2'){
              $jobApplication->status_id = 4;

          }
         
            
            $jobApplication->save();

            if ($request->comment) {
                $scheduleComment = [
                    'interview_schedule_id' => $interviewSchedule->id,
                    'user_id' => auth()->user()->id,
                    'comment' => $request->comment
                ];

                $interviewSchedule->comments()->create($scheduleComment);
            }
            \DB::commit();

              if(!empty($request->employees)) {
                $interviewSchedule->employees()->attach($request->employees);
                  
                   
                // Mail to employee for inform interview schedule
                Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication, $interviewSchedule,$meetings));
            }

            // mail to candidate for inform interview schedule
            Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule,$meetings));
            
            return $this->successJson('Interview Schedule Created Successfully',200,$jobApplication);
            
         } catch(\Throwable $th) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$th->getMessage());
         }
       }
    }


    public function storeurl(StoreRequest $request)
    {
        if(!auth()->user()->cans('add_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $dateTime =  $request->scheduleDate . ' ' . $request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);
        
            if($request->interview_type == 'online'){
            $data = $request->all();
         
            $data['meeting_name'] = $request->meeting_title;
            $data['start_date_time'] = $dateTime;
            $data['meetingurl']=$request->meetingurl;
           // $meeting = $meeting->create($data);
          
            $meetings = $data;
        } 
        else{
            $meetings = '';
        }  
            // store Schedule
            $interviewSchedule = new InterviewSchedule();
            $interviewSchedule->job_application_id = $request->candidates;
            $interviewSchedule->schedule_date = $dateTime;
            $interviewSchedule->interview_type = ($request->has('interview_type')) ? $request->interview_type : 'offline';
            // $interviewSchedule->interview_type = $request->interview_type;
            $interviewSchedule->meeting_id = ($request->meeting_id!= '') ? $request->meeting_id: null;
            $interviewSchedule->meetingurl = ($request->meetingurl!= '') ? $request->meetingurl: null;
            $interviewSchedule->user_accept_status='accept';

            $interviewSchedule->save();

            // Update Schedule Status
            $jobApplication = $interviewSchedule->jobApplication;
            // $jobApplication->status_id = 5;
            // $jobApplication->save();

            if($request->status=='interview round 1'){
                $jobApplication->status_id = 3;
               }else if($request->status=='interview round 2'){
                  $jobApplication->status_id = 4;
    
              }else if($request->status=='salary Negotiation'){
               $jobApplication->status_id = 10;
               }

            if ($request->comment) {
                $scheduleComment = [
                    'interview_schedule_id' => $interviewSchedule->id,
                    'user_id' => auth()->user()->id,
                    'comment' => $request->comment
                ];

                $interviewSchedule->comments()->create($scheduleComment);
            }

            if (!empty($request->employees)) {
                $interviewSchedule->employees()->attach($request->employees);
                Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication,$interviewSchedule,$meetings));

                // Mail to employee for inform interview schedule
                //Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication,$meetings));
            }

            // mail to candidate for inform interview schedule
           Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule,$meetings));
           
  
 

            return $this->successJson('Interview Schedule Created Successfully',200,$jobApplication);
       }
    }
public function data(Request $request)
    {
        if(!auth()->user()->cans('view_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $shedule = InterviewSchedule::select('interview_schedules.id','interview_schedules.interview_type', 'interview_schedule_employees.user_id as employee_id','job_applications.id as candidate_id','job_applications.full_name','users.name as interviewer_name', 'interview_schedules.status','interview_schedules.schedule_date','interview_schedules.meetingurl')
            ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->leftjoin('interview_schedule_employees', 'interview_schedule_employees.interview_schedule_id', 'interview_schedules.id')
 
            ->leftJoin('users', 'interview_schedule_employees.user_id', 'users.id')

            ->whereNull('job_applications.deleted_at');
            
    
            $shedule = $shedule->orderBy('id', 'DESC')->get();
        if($shedule){
            return $this->successJson('Details',200,$shedule);
        }else{
            return $this->successJson('data not found',404);
        }
        
        }
       
    }

    public function update(UpdateRequest $request, $id)
    {
        if(!auth()->user()->cans('add_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }
        else{
            
            try{
            \DB::beginTransaction();
                $dateTime =  $request->scheduleDate . ' ' . $request->scheduleTime;
                $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);

                // Update interview Schedule
                $interviewSchedule = InterviewSchedule::select('id', 'job_application_id','interview_type', 'schedule_date', 'status')
                    ->with([
                        'jobApplication:id,full_name,email,job_id,status_id',
                        'employees',
                        'comments'
                    ])
                    ->where('id', $id)->first();
                $interviewSchedule->schedule_date = $dateTime;
                if(!is_null($request->interview_type)){
                     $interviewSchedule->interview_type = $request->interview_type;

                    $interviewSchedule->meetingurl  = $request->meetingurl;
			

                }else{
                    $interviewSchedule->interview_type =  $interviewSchedule->interview_type;
            
                }
            
                if($request->interview_type == 'offline' ){
                    $interviewSchedule->meeting_id = null;
                    $interviewSchedule->meetingurl = null;
                    $meetings ='';
                }
                $interviewSchedule->save();

                if ($request->comment) {
                    $scheduleComment = [
                        'comment' => $request->comment
                    ];

                    $interviewSchedule->comments()->updateOrCreate([
                        'interview_schedule_id' => $interviewSchedule->id,
                        'user_id' => $this->user->id
                    ], $scheduleComment);
                }

                $jobApplication = $interviewSchedule->jobApplication;
                    //zoom meeting update
            
                if($request->interview_type == 'online'){
                
                    $data = $request->all();
                    //$data['meeting_name'] = $request->meeting_title;
                    $data['start_date_time'] = $dateTime;
                    $data['meetingurl']=$request->meetingurl;
			
                    $meetings = $data;
                    $interviewSchedule->save();
                }
                if (!empty($request->employee)) {
                    $interviewSchedule->employees()->sync($request->employee);
                    if(!($request->interview_type)){
                        $meeting = '';
                    }
                    // Mail to employee for inform interview schedule



                    Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication,$interviewSchedule,$meetings));

                }
                \DB::commit();
                    return $this->successJson('Interview Schedule Updated Successfully',200,$interviewSchedule);
            }
            catch (\Throwable $th) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$th->getMessage());
            }
        } 
    }

    public function destroy($id)
    {
       if(!auth()->user()->cans('delete_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }
        else{
       InterviewSchedule::destroy($id);
        return $this->successJson('Interview Schedule  Deleted Successfully',200);
        }
    }


    public function show($id)
    {
        if(!auth()->user()->cans('view_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }
        else{
        $schedule = InterviewSchedule::with(['jobApplication', 'user'])->find($id);
        $schedule['statuslist'] = ApplicationStatus::select('id', 'status')->get();
        return $this->successJson('Interview schedule list',200,$schedule);
        }
    }


   
    public function changeStatus(Request $request)
    {        
        if(!auth()->user()->cans('add_schedule')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }
        else{
        $this->commonChangeStatusFunction($request->id, $request);

        //return Reply::success(__('messages.interviewScheduleStatus'));
        return $this->successJson('Update Successfull !',200);
        }
    }

    public function commonChangeStatusFunction($id, $request)
    {
        // store Schedule
        $interviewSchedule = InterviewSchedule::select('id', 'job_application_id', 'status')
            ->with([
                'jobApplication:id,full_name,email,job_id,status_id',
                'employees'
            ])
            ->where('id', $id)->first();
        $interviewSchedule->status = $request->status;
        $interviewSchedule->save();

        $application = $interviewSchedule->jobApplication;
        $status = ApplicationStatus::select('id', 'status');

        if (in_array($request->status, ['rejected', 'canceled'])) {
            $applicationStatus = $status->status('rejected');
        }
        if ($request->status === 'hired') {
            $applicationStatus = $status->status('hired');
        }
        if ($request->status === 'pending') {
            $applicationStatus = $status->status('interview round 1');
        }
        if ($request->status === 'interview round 1') {
            $applicationStatus = $status->status('interview round 1');
        }
        if ($request->status === 'salary Negotiation') {
            $applicationStatus = $status->status('salary Negotiation');
        }

        if ($request->status === 'interview round 2') {
            $applicationStatus = $status->status('interview round 2');
        }



        $application->status_id = $applicationStatus->id;

        $application->save();

        $employees = $interviewSchedule->employees;
        $admins = User::allAdmins();

        $users = $employees->merge($admins);

        if ($users && $request->mailToCandidate ==  'yes') {
            // Mail to employee for inform interview schedule
            Notification::send($users, new ScheduleInterviewStatus($application));
        }

        if ($request->mailToCandidate ==  'yes') {
            // mail to candidate for inform interview schedule status
            Notification::send($application, new ScheduleStatusCandidate($application, $interviewSchedule));
        }

        return;
    }
    public function sendratinglink(Request $request,$id,$employeeid){
       
         
        $shedule = InterviewSchedule::find($id);
       
       $users = User::find($employeeid);
       $candidates = JobApplication::find($shedule->job_application_id);
       $interviewDate=$shedule->schedule_date->format('d-m-Y H:i:s');
       $interviewLink='https://cipcrm.org.in/'.$request->url_name.'/'.Crypt::encryptString($id);
       $round=$request->round;

       
       Notification::send($users, new InterviewRatingNotification($interviewDate, $candidates->full_name, $interviewLink,$round));
       
        return $this->successJson('Link Sent Successfully',200);
    }

public function getratinglink($id){
        $id=Crypt::decryptString($id);  

        $shedule = InterviewSchedule::find($id);
    $jobApplications = JobApplication::select('job_applications.id', 'job_applications.job_id','job_applications.relevent_exp','job_applications.total_exp','job_applications.qualification_id' ,'job_applications.jobrecruitment_id', 'status_id', 'full_name','email','phone','address', 'skills')
        ->with([ 
        'job',
        'recruitment',
        'salarydetails',
        'qualification',
        'subqualifications',
        'status:id,status',
        'interviews', 
        'totalrating',                    
        ])->where('id',$shedule->job_application_id)->get();
    if($jobApplications->isNotEmpty()){
        return $this->successJson('Job Applications Details',200,$jobApplications);
    }else{
        return $this->successJson('not found Details',404);
    }
}
}
