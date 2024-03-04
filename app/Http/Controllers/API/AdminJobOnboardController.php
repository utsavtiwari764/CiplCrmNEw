<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\User;
use App\Onboard;
use App\Currency;
use App\Department;
use App\Designation;
use App\Helper\Files;
use App\Helper\Reply;
use App\OnboardFiles;
use App\JobApplication;
use Illuminate\Support\Str; 
use App\Notifications\JobOffer;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Onboard\StoreRequest;
use App\Http\Requests\Onboard\UpdateStatus;
use App\JobOfferQuestion;
use App\JobOnboardQuestion;
use App\OnboardAnswer;
use App\Notifications\JobOfferAccepted;
use App\Notifications\JobOfferRejected;
use Illuminate\Support\Facades\Notification;

class AdminJobOnboardController extends Controller
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


 public function store(Request $request,$id)
    { 
        $application = JobApplication::findOrFail($id);
        // Save On Board Details

        //$check=Onboard::where(['job_application_id'=>$id,'hired_status'=>'offered'])->count();
           $check = Onboard::where('job_application_id', $id)->where(function ($query) {
     	   $query->where('hired_status', 'offered')
              ->orWhere('hired_status', 'accepted');
         })->count(); 
        if(empty($check)){
            $onBoard = new Onboard();
            $onBoard->job_application_id = $application->id;
            // $onBoard->department_id      = $request->department;
            // $onBoard->designation_id     = $request->designation;
            // $onBoard->currency_id        = $request-> currency_id; 
            // $onBoard->salary_offered     = $request->salary;
             $onBoard->joining_date       = $request->joining_date;
            // $onBoard->reports_to_id      = $request->report_to;
             $onBoard->hired_status    = 'offered';
            // $onBoard->accept_last_date   = $request->last_date;
            $onBoard->offer_code         = Str::random(18);
            $onBoard->message         = $request->message;
            $onBoard->template_id         = $request->template_id;
            $onBoard->save();
            // $names = $request->name;
            // $files = $request->file;
           
            if ($request->send_email=='yes') {
                 $this->sendOffer($application->id);    
              
            } 
    
            if(!empty($onBoard)){
 

                return $this->successJson('Offer Letter Sent',200,$onBoard);
            }else{
                return $this->successJson('Not Found Details',200);
            }
        }else{
           return $this->errorJson('Offer letter already sent',409);
        }
        
      
    }


    public function sendOffer($userID)
    {
       
        $jobApplication = JobApplication::select('id', 'job_id', 'full_name', 'email','jobrecruitment_id')
        ->with(['job:id','recruitment','onboard:id,offer_code,job_application_id,hired_status'])
        ->where('id', $userID)->first();
      
        if ($jobApplication->onboard->hired_status !== 'offered') {
            $jobApplication->onboard->hired_status = 'offered';

            $jobApplication->onboard->save();
        }
        // Send Email Or Sms to applicant.Request
       return  $jobApplication->notify(new JobOffer($jobApplication));

        
    }

      public function view($id){
         $jobApplications = Onboard::with(['applications','applications.recruitment','template','salary','files','applications.job', 'department', 'designation', 'onboardQuestion'])
        ->where('offer_code', $id)->where('hired_status','!=','accepted')->where('hired_status','!=','rejected')
        ->first();

        if(!empty($jobApplications)){
            return $this->successJson('Offer  Details',200,$jobApplications);
        }else{
            return $this->successJson('Page Not Found Details',404);
        }
    }

 public function saveOffer(Request $request)
        {
           try{
            // dd($request->all());
            $offer = Onboard::where('offer_code', $request->offer_code)->first();
            $jobApplication = JobApplication::findOrFail($offer->applications->id);
            //return $this->successJson('Offer  Details',200,$jobApplication);
            if($request->type == 'accept'){
                
               
               
                $offer->hired_status = 'accepted';
                $jobApplication->status_id='5';
                $jobApplication->save();
            }
            else{
                $offer->reject_reason = $request->reason;
                $offer->hired_status  = 'rejected';
		 $jobApplication->status_id='6';
                $jobApplication->save();
            }
    
            $offer->save();
   
            // All admins data for send mail.
            $admins = User::allAdmins();
            //$admins = User::findOrFail(1);

           if ($admins) {
            if($request->type == 'accept'){
                // Send Email Or SMS to admin on accept.
                Notification::send($admins, new JobOfferAccepted($jobApplication));
            }else{ 
                // Send Email Or SMS to admin on reject.
                //Notification::send($admins, new JobOfferRejected($jobApplication));
            }
          }
            return $this->successJson('Thank you for your response',200,$jobApplication);
           }catch(\Throwable $th) {
                 
                return $this->errorJson('something else wrong',403,$th->getMessage());
         }

      }

///admin onboad list

public function onboadList(){
    $jobApplications = Onboard::select('on_board_details.id','on_board_details.message','on_board_details.cancel_reason','on_board_details.reject_reason', 'job_applications.id as application_id', 'job_applications.full_name', 'jobs.erf_id', 'jobrecruitments.location as location', 'on_board_details.joining_date', 'on_board_details.accept_last_date', 'on_board_details.hired_status')
            ->join('job_applications', 'job_applications.id', 'on_board_details.job_application_id')
            ->leftJoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('jobrecruitments', 'jobrecruitments.id', 'job_applications.jobrecruitment_id')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')->get();
            return $this->successJson('onboard list',200,$jobApplications);
}
public function updateStatus(Request $request, $id)
{
    $onboard = Onboard::findOrFail($id);
    $onboard->cancel_reason = $request->cancel_reason;
    $onboard->hired_status = 'canceled';
    $onboard->save();   

    return $this->successJson('Update Successfully',200,$onboard);
}
}
