<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Job;
use App\User;
use App\Skill;
use App\Question;
use Carbon\Carbon;
use App\JobLocation;
use App\ZoomMeeting;
use App\ZoomSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\JobApplication;
use App\ApplicationStatus;
use App\InterviewSchedule;
use App\ApplicationSetting;
use Illuminate\Support\Arr;
use App\Traits\ZoomSettings;
use Illuminate\Http\Request;
use App\JobApplicationAnswer;
use Illuminate\Support\Facades\DB;
use MacsiDigital\Zoom\Facades\Zoom;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JobApplicationExport;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ScheduleInterview;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreJobApplication;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Http\Requests\UpdateJobApplication;
use App\Notifications\CandidateStatusChange;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CandidateScheduleInterview;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\JobApplicationsubqualification;
use App\Notifications\RatingNotification;
use App\Exports\ExportJobApplication; 
use App\Imports\ImportJobApplication; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Mail;
use App\Mail\DocumentUploadMail;
use App\Mail\RatingMail;

use App\Mail\PccUploadMail;
use App\Document;
use App\Ratingdetail;
use Illuminate\Validation\Rule;
use App\Notifications\HiredNotification;
use App\Notifications\HoldNotification;
use App\Notifications\RejectNotification;
use App\Notifications\SelectedApplication;
use App\Notifications\CandidateStatusDocument;
use App\Notifications\AdminStatusDocument;
class JobApplicationController extends Controller
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
   
    public function store(StoreJobApplication $request)
    {
        if(!auth()->user()->cans('add_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
           
            try {
               \DB::beginTransaction();
            $jobApplication = new JobApplication();
            $jobApplication->full_name = $request->full_name;
            $jobApplication->lastname =$request->lastname;
	    $jobApplication->fatherfirst =$request->fatherfirst;
            $jobApplication->fatherlast =$request->fatherlast;
            $jobApplication->job_id = $request->job_id;
            $jobApplication->status_id = 1; //applied status id
            $jobApplication->jobrecruitment_id = $request->jobrecruitment_id;
            $jobApplication->crated_by=auth()->user()->id;
            $jobApplication->email = $request->email;
            $jobApplication->phone = $request->phone;
            $jobApplication->address = $request->address;
            $jobApplication->column_priority = 0;
            $jobApplication->relevent_exp=$request->relevent_exp;
	    $jobApplication->skills=$request->skills;
            $jobApplication->total_exp=$request->total_exp;
            $jobApplication->qualification_id=$request->qualification_id;
            $jobApplication->added_by=auth()->user()->id;
            if ($request->has('gender')) {
                $jobApplication->gender = $request->gender;
            }
            if ($request->has('dob')) {
                $jobApplication->dob = $request->dob;
            }
	   $jobApplication->save();
	    if(!is_null($request->subqualification_id)){
                JobApplicationsubqualification::where('jobapplication_id', $jobApplication->id)->delete();
                foreach ($request->subqualification_id as $value) {
                    $jobapplicationqualification = new JobApplicationsubqualification();
                    $jobapplicationqualification->jobapplication_id = $jobApplication->id;
                    $jobapplicationqualification->status='1';
                    $jobapplicationqualification->subqualifications_id = $value;
                  
                    $jobapplicationqualification->save();
                }
             }
            if ($request->hasFile('resume')){
                $hashname = Files::uploadLocalOrS3($request->resume, 'documents/'.$jobApplication->id, null, null, false);
                    $jobApplication->documents()->create([
                    'name' => 'Resume',
                    'hashname' => $hashname
                ]);
            }

             if ($request->hasFile('document')){
                $hashname = Files::uploadLocalOrS3($request->document, 'documents/'.$jobApplication->id, null, null, false);
                    $jobApplication->documents()->create([
                    'name' => 'Document',
                    'hashname' => $hashname
                ]);
            }

              \DB::commit();
            if(!empty($jobApplication)){
                 $messagedata="Dear $request->full_name, Congratulations!!! your CV has been shortlisted for job opening at Corporate Infotech Private Limited.  If you want to proceed with further process please reply YES or reply NO to stop the process.Thank you.Team CIPL";

                Files::smssend($request->phone,$messagedata);

                return $this->successJson('Job Application Added Successfully',200,$jobApplication);
            }else{
                return $this->successJson('not found Details',200);
            }
          }catch (\Exception $th) {
            \DB::rollBack();
            return $this->errorJson('something else wrong',403,$th->getMessage());
        }
     }
    }

 public function get(Request $request ,$id=null){
     
     
        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.job_id','job_applications.cancel_reason','job_applications.relevent_exp','job_applications.total_exp','job_applications.qualification_id' ,'job_applications.jobrecruitment_id', 'status_id', 'full_name','email','phone','address', 'skills','added_by')
        ->with([
            'job',
            'recruitment',
            'salarydetails',
            'qualification',
            'subqualifications',
            'status:id,status',
             'addedby',
             'interviews', 
              'totalrating', 
             
            ]);
            if (@$id != '') {
                $jobApplications = $jobApplications->where('job_id', $id);
            }
            
            if (@$request->project_manager !=''){            
                $jobApplications = $jobApplications->whereHas('recruitment', function ($query) use ($request)
                {
                $query->Where('project_manager', $request->project_manager);
                });               
            }
            if (@$request->location !=''){            
                $jobApplications = $jobApplications->whereHas('recruitment', function ($query) use ($request)
                {
                $query->Where('location', $request->location);
              }); 
            }
            $jobApplications = $jobApplications->get();
            if(!empty($jobApplications)){
                return $this->successJson('Job Applications Details',200,$jobApplications);
            }else{
                return $this->successJson('not found Details',404);
            }
         
    }

    public function getlist(){
    $jobApplications = JobApplication::select('job_applications.id', 'job_applications.job_id','job_applications.relevent_exp','job_applications.total_exp','job_applications.qualification_id' ,'job_applications.jobrecruitment_id', 'status_id', 'full_name','email','phone','address', 'skills')
        ->with([ 
            'job',
            'recruitment',
            'salarydetails',
            'qualification',
            'subqualifications',
            'status:id,status',
            'interview', 
            ])->whereIn('status_id', ['3','4'])->get();
       if($jobApplications->isNotEmpty()){
                return $this->successJson('Job Applications Details',200,$jobApplications);
            }else{
                return $this->successJson('not found Details',404);
            }

     

    }
    
      public function getlistbyid(){
            $jobApplications = JobApplication::select('job_applications.id', 'job_applications.job_id','job_applications.rating','job_applications.relevent_exp','job_applications.total_exp','job_applications.qualification_id' ,'job_applications.jobrecruitment_id', 'status_id', 'full_name','email','phone','address', 'skills')
                ->with([ 
                    'job',
                    'recruitment',
                    'salarydetails',
                    'qualification',
                    'subqualifications',
                    'status:id,status',
                    'interviews', 
                    'totalrating',                    
                    ])->whereNotNull('job_applications.rating')->get();
            if($jobApplications->isNotEmpty()){
                return $this->successJson('Job Applications Details',200,$jobApplications);
            }else{
                return $this->successJson('not found Details',404);
            }
     }
   public function edit($id)
    {
        if(!auth()->user()->cans('edit_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
                $data['statuses'] = ApplicationStatus::all();
                $data['application'] = JobApplication::find($id);
                if($data){
                    return $this->successJson('Job Application Details',200,$data);
                }
               return $this->errorJson('Job Application id not found',404);
             
        }
    }
  
    public function update(UpdateJobApplication $request, $id)
    {
        if(!auth()->user()->cans('edit_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
           try {
            \DB::beginTransaction();
            $mailSetting = ApplicationSetting::select('id', 'mail_setting')->first()->mail_setting;
            $jobApplication = JobApplication::with(['documents'])->findOrFail($id);
            
            $jobApplication->full_name = $request->full_name;
            $jobApplication->job_id = $request->job_id;
            $jobApplication->status_id = $request->status_id; //applied status id
            $jobApplication->email = $request->email;
            $jobApplication->phone = $request->phone;
            $jobApplication->address = $request->address;
            $jobApplication->column_priority = 0;
            $jobApplication->relevent_exp=$request->relevent_exp;
            $jobApplication->total_exp=$request->total_exp;
            $jobApplication->qualification_id=$request->qualification_id;
           // $jobApplication->subqualification_id=$request->subqualification_id;
            if ($request->has('gender')) {
                $jobApplication->gender = $request->gender;
            }
            if ($request->has('dob')) {
                $jobApplication->dob = $request->dob;
            }
            $isStatusDirty = $jobApplication->isDirty('status_id');
            $jobApplication->save();
           
            if($request->subqualification_id[0] !="undefined"){
                JobApplicationsubqualification::where('jobapplication_id', $jobApplication->id)->delete();
                foreach ($request->subqualification_id as $value) {
                    $jobapplicationqualification = new JobApplicationsubqualification();
                    $jobapplicationqualification->jobapplication_id = $jobApplication->id;
                    $jobapplicationqualification->status='1';
                    $jobapplicationqualification->subqualifications_id = $value;
                  
                    $jobapplicationqualification->save();
                }
             }
            if ($request->hasFile('resume')) {
                $hashname = Files::uploadLocalOrS3($request->resume, 'documents/'.$jobApplication->id, null, null, false);
                    $jobApplication->documents()->create([
                    'name' => 'Resume',
                    'hashname' => $hashname
                ]);
            }
            \DB::commit(); 
             if ($request->status_id) {
            Notification::send($jobApplication, new CandidateStatusChange($jobApplication));
             }
            if(!empty($jobApplication)){
                return $this->successJson('Job Application Updated Successfully',200,$jobApplication);
            }else{
                return $this->successJson('Not Found Details',200);
            }
        }catch (\Throwable $th) {
                \DB::rollBack();
                return $this->errorJson('Something Else Wrong',403,$th->getMessage());
            }
           
        }
    }
    public function destroy($id)
    {
        if(!auth()->user()->cans('edit_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
           
            try {
                \DB::beginTransaction();
                
                $jobApplication = JobApplication::findOrFail($id);
                 JobApplicationsubqualification::where('jobapplication_id', $id)->delete();
                   
                if ($jobApplication->photo) {
                    Storage::delete('candidate-photos/'.$jobApplication->photo);
                }

                $jobApplication->forceDelete();
                \DB::commit();
                return $this->successJson('jobApplication Delete Successfully',200);
            }
            catch (\Throwable $th) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$th);
            }
        }
    }

    public function ratingSave(Request $request, $id)
    {
         
		 $validator = Validator::make($request->all(), [
                 
                'interviewround' => Rule::unique('ratingdetails')->where(fn ($query) => $query->where(['interviewround'=>$request->interviewround,'job_application_id'=>$id]))
             ]); 
             //$validator->setMessage('unique', 'The Feedback has already been taken');
 	 	$validator->setCustomMessages([
       	 	'interviewround.unique' => 'The Feedback has already been taken',
  		  ]);

             if ($validator->fails()) {
                 return $this->errorJson( $validator->messages(),422);
             }
            try {
                \DB::beginTransaction();
                
                    $application = JobApplication::withTrashed()->findOrFail($id);
                   
                    $application->rating = $request->tottal_rating;
                    
                    $application->save();
                    
                    $ratingdetail=new Ratingdetail();
                    $ratingdetail->job_application_id=$id;
                    $ratingdetail->overall_personality=$request->overall_personality;
                    $ratingdetail->mobility=$request->mobility;
                    $ratingdetail->self_concept=$request->self_concept;
                    $ratingdetail->openness_to_feedback=$request->openness_to_feedback;
                    $ratingdetail->drive=$request->drive;
                    $ratingdetail->leadership_potential=$request->leadership_potential;
                    $ratingdetail->personal_efficacy=$request->personal_efficacy;
                    $ratingdetail->maturity_understanding=$request->maturity_understanding;
                    $ratingdetail->comprehensibility_eloquence=$request->comprehensibility_eloquence;
                    $ratingdetail->knowledge_of_subject_job_product=$request->knowledge_of_subject_job_product;
                    $ratingdetail->poise_mannerism=$request->poise_mannerism;
                    $ratingdetail->tottal_rating=$request->tottal_rating;
                    $ratingdetail->interviewround=$request->interviewround;
		    $ratingdetail->comments=$request->comments;
                    $ratingdetail->save();
                    \DB::commit(); 
                     $admins = User::allAdmins();
                      
                 \Mail::to('murali.menon@cipl.org.in')->send(new RatingMail($ratingdetail));
                    //Notification::send($admins, new RatingNotification($application,$ratingdetail));
                    return $this->successJson('Thank you for valuable feedback',200);
            }
            catch (\Throwable $th) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$th->getMessage());
            }
         
        
    }

   public function show($id)
    {
      $application = JobApplication::with(['schedule','notes','onboard', 'status', 'schedule.employee', 'schedule.comments.user'])->find($id);
      return $this->successJson('jobApplication with rating',200,$application);
    }

    public function archiveJobApplication(Request $request, JobApplication $application)
    {
        if(!auth()->user()->cans('delete_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
                $application->delete();
                return $this->successJson('Application Archived Successfully',200); 
            
       }
    }

 public function unarchiveJobApplication(Request $request, $application_id)
    {
        if(!auth()->user()->cans('delete_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
                $application = JobApplication::select('id', 'deleted_at')->withTrashed()->where('id', $application_id)->first();
                $application->restore(); 
                return $this->successJson('Application Unarchived Successfully',200); 
            }
    }


    public function exportJobApplication(Request $request){
        return Excel::download(new ExportJobApplication, 'jobapplication'.time().'.xlsx');
    }

    public function JobApplicationimport(Request $request){
       
        $validator = Validator::make($request->all(), [           
            'import_file' => 'required|mimes:csv,xlsx',
            'job_id'=>'required',
            'jobrecruitment_id'=>'required',    
       ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
    
           }
        try{
           \DB::beginTransaction();
           session()->forget('jobrecruitment_id');
           session()->forget('job_id');
           session(['job_id'=>$request->job_id , 'jobrecruitment_id'=>$request->jobrecruitment_id]);
           Excel::import(new ImportJobApplication,$request->file('import_file'));
           \DB::commit();
           return $this->successJson('Job Application Added Successfully',200);
        }catch ( \Exception $e){
        \DB::rollBack();
        return $this->errorJson('oops something went wrong please try again',404,$e->getMessage());
        }            
       
    }
 

  public function statusJobApplication(Request $request, $application_id)
        {
            if(!auth()->user()->cans('view_job_applications')){
                return $this->errorJson('Not authenticated to perform this request',403);
            }else{
                    $application = JobApplication::find($application_id);
                  //return $this->successJson('Application successfull update',200,$request->all()); 
                    if($application){
                        $application->status_id=$request->status;
                       
                        $application->save();
			 if($request->status=='14'){
                             
                    		Notification::send($application, new HoldNotification($application));
                	}
			  if($request->status=='9'){
                   
                    Notification::send($application, new RejectNotification($application));
                    $application->delete(); 
                }
                        return $this->successJson('Status Updated Successfully',200); 
                    }else{
                        return $this->successJson('Job Application Not Founds',404); 
                    }
 
                    
                }
        }


    public function documentuploadlink($id){
        $application = JobApplication::find($id);
      // return $this->successJson('Application successfull update',200,$application->email); 
        if($application){
            try{
                \DB::beginTransaction();
                $application->status_id=16;
                $application->save();
                $url='https://cipcrm.org.in/documentsupload/'.Crypt::encryptString($id); 
                $details = [
                            'title' => 'Documents Upload link',
                            'url' => $url,
                             'position'=>@$application->recruitment->degination,
                            ]; 
                
                $application->save();
                \DB::commit();
                Mail::to($application->email)->send(new DocumentUploadMail($details));
                return $this->successJson('Link Sent Successfully For Documents Uploading',200);
            }
            catch ( \Exception $e){
                \DB::rollBack();
                return $this->errorJson('oops something went wrong please try again',404,$e->getMessage());
                } 
        }else{
            return $this->errorJson('Page Not Found',404);
        }
    }

//==================Document upload for PCC=================//

    public function PCClink($id){
        $application = JobApplication::findOrFail($id);
        //return $this->successJson('Application successfull update',200,$application->email); 
         if($application){
             try{
                 \DB::beginTransaction();
                 $application->status_id=18;
                 $application->save();
                 $url='https://cipcrm.org.in/pccupload/'.Crypt::encryptString($id); 
                 $details = [
                             'title' => 'Pcc Upload link',
                             'url' => $url
                             ]; 
                 
                 $application->save();
                 \DB::commit();
                 Mail::to($application->email)->send(new PccUploadMail($details));
                 return $this->successJson('Send link successfully for Pcc upload',200,$application);
             }
             catch ( \Exception $e){
                 \DB::rollBack();
                 return $this->errorJson('oops something went wrong please try again',404,$e->getMessage());
                 } 
         }else{
             return $this->errorJson('page no founds',404);
         }
    }


   public function uploadpcc(Request $request,$id){
        $id=Crypt::decryptString($id);        
        $application = JobApplication::with(['documents'])->findOrFail($id);
        if($application){
            try {
               \DB::beginTransaction();
               if($request->hasFile('pcc')) {
                Document::where(['name'=>'pcc','documentable_id'=>$id])->delete();
                $hashname = Files::uploadLocalOrS3($request->pcc, 'documents/'.$application->id, null, null, false);
                    $application->documents()->create([
                    'name' => 'pcc',
                    'hashname' => $hashname
                ]);
            }
                $application->status_id=19;
                $application->save();
               \DB::commit();
               return $this->successJson('Pcc uploaded successfully',200,$application);
            } catch (\Throwable $e) {
               \DB::rollBack();
               return $this->errorJson('oops something went wrong please try again',403,$e->getMessage());
       }
           //return $this->successJson('Application successfull update',200,$application); 
       }else{
           return $this->errorJson('page no founds',404);
       }
    }
public function getlistbyidfilter($id){
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
                         ])->where('id',$id)->get();
                 if($jobApplications->isNotEmpty()){
                     return $this->successJson('Job Applications Details',200,$jobApplications);
                 }else{
                     return $this->successJson('not found Details',404);
                 }
     
         
        }
function uploadDocument(Request $request,$id)
    {
 
        $id=Crypt::decryptString($id);  
      
        $mailSetting = ApplicationSetting::select('id', 'mail_setting')->first()->mail_setting;
        $applications = JobApplication::with(['documents'])->where('status_id','16')->find($id);
        
  
        if(!empty($applications)){
             try {
                $application = JobApplication::with(['documents'])->find($id);

           
                \DB::beginTransaction();
                if($request->hasFile('aadharfront')) {
                    Document::where(['name'=>'aadharfront','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->aadharfront, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'aadharfront',
                        'hashname' => $hashname
                    ]);
                }
                
                if($request->hasFile('aadharback')) {
                    Document::where(['name'=>'aadharback','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->aadharback, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'aadharback',
                        'hashname' => $hashname
                    ]);
                }
                if($request->hasFile('passport')) {
                    Document::where(['name'=>'passport','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->passport, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'passport',
                        'hashname' => $hashname
                    ]);
                }

                if($request->hasFile('pancard')) {
                    Document::where(['name'=>'pancard','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->pancard, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'pancard',
                        'hashname' => $hashname
                    ]);
                }

                if($request->hasFile('voterid')) {
                    Document::where(['name'=>'voterid','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->voterid, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'voterid',
                        'hashname' => $hashname
                    ]);
                }
                
                if($request->hasFile('offerletter')) {
                    Document::where(['name'=>'offerletter','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->offerletter, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'offerletter',
                        'hashname' => $hashname
                    ]);
                }

                if($request->hasFile('appointmentletter')) {
                    Document::where(['name'=>'appointmentletter','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->appointmentletter, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'appointmentletter',
                        'hashname' => $hashname
                    ]);
                }

                if($request->hasFile('salaryslip')) {
                    Document::where(['name'=>'salaryslip','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->salaryslip, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'salaryslip',
                        'hashname' => $hashname
                    ]);
                }

                if($request->hasFile('bankstatement')) {
                    Document::where(['name'=>'bankstatement','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->bankstatement, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'bankstatement',
                        'hashname' => $hashname
                    ]);
                }
                if($request->hasFile('other')) {
                    Document::where(['name'=>'other','documentable_id'=>$id])->delete();
                    $hashname = Files::uploadLocalOrS3($request->other, 'documents/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'other',
                        'hashname' => $hashname
                    ]);
                }


                
                 $application->status_id=17;
                 $application->save();
                 
                \DB::commit();
                   $admins = User::allAdmins();
                  Notification::send($application, new CandidateStatusDocument($application));
                 Notification::send($admins, new AdminStatusDocument($application)); 
                return $this->successJson('Documents Uploaded Successfully',200,$application);

             } catch (\Throwable $e) {
                \DB::rollBack();
                return $this->errorJson('oops something went wrong please try again',403,$e->getMessage());
        }
            //return $this->successJson('Application successfull update',200,$application); 
        }else{
            return $this->errorJson('Documents already uploaded !',404);
        }
    }

   public function hired(Request $request,$id){

        $application = JobApplication::with([ 
                            'job',
                            'recruitment',
                            'salarydetails',
                            'qualification',
                            'subqualifications',
                            'status:id,status',
                            'interviews', 
                            'totalrating',                    
                            ])->find($id);
        if($application){
           $application->status_id=$request->status;
           $application->save();
  	Notification::send($application, new HiredNotification($application));
	return $this->successJson('Email Sent Successfully',200,$application); 
        }else{
            return $this->successJson('Application not founds',404); 
        } 
        
    }

     public function uploadresult(Request $request,$id){
        $application = JobApplication::findOrFail($id);
        if($application){
            try{
                 $validator = Validator::make($request->all(), [
                 
                'status_id' =>'required',
                'file'=>'required' 
              ]); 
             if ($validator->fails()) {
                 return $this->errorJson( $validator->messages(),422);
             }


                \DB::beginTransaction();
                $application->status_id=$request->status_id;
                $application->save();
                if ($request->hasFile('file')){
                        Document::where(['name'=>'result','documentable_id'=>$id])->delete();

                    $hashname = Files::uploadLocalOrS3($request->file, 'result/'.$application->id, null, null, false);
                        $application->documents()->create([
                        'name' => 'result',
                        'hashname' => $hashname
                    ]);
                }
  	\DB::commit();
                return $this->successJson('Result Uploaded Successfully',200,$application);
              
            }catch (\Throwable $e) {
                \DB::rollBack();
                return $this->errorJson('oops something went wrong please try again',403,$e->getMessage());
            }
        }else{
            return $this->errorJson('page no founds',404);
        }
    }  

    public function reject(Request $request, JobApplication $application)
    {
        if($application){
                $validator = Validator::make($request->all(), [           
                    'status' => 'required',             
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 422);    
            }
          $application->status_id=$request->status;
          $application->cancel_reason=$request->cancel_reason; 
 
          $application->save();       
     
            if($request->status=='9' || $request->status=='23' || $request->status=='24'){
                    Notification::send($application, new RejectNotification($application));
                    $application->delete();  
                    return $this->successJson('Application Archived Successfully',200); 
            }
        }   
    }

 public function selectionupdate(Request $request, JobApplication $application)
    {
        if($application){
                $validator = Validator::make($request->all(), [           
                    'status' => 'required',             
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 422);    
            }
            
          $application->status_id=$request->status;
          $application->save();       
          if($request->status=='5' || $request->status=='21' || $request->status=='22'){
               Notification::send($application, new SelectedApplication($application));
                   return $this->successJson('Application Status Update Successfully',200); 
            }
        }   
    }

//rating round 1

public function ratinginterviewone($type=null){
// return $this->successJson('Rating Details',200);

  //$rating=Ratingdetail::with(['jobapplication'])->where(['interviewround'=>$type])->get();
$rating=Ratingdetail::select('job_applications.id as jobapplication_id','job_applications.full_name','ratingdetails.*')
                ->leftjoin('job_applications', 'job_applications.id', 'ratingdetails.job_application_id')                 
                ->where('ratingdetails.interviewround',$type)
                ->whereNull('job_applications.deleted_at')->get();


  if($rating){
   return $this->successJson('Rating Details',200,$rating);
  }else{
   return $this->errorJson('Data no founds',404);

  }
}


public function ratinginterviewoneold($type=null){
 
$rating=Ratingdetail::select('job_applications.id as jobapplication_id','job_applications.full_name','jobrecruitments.degination','jobrecruitments.location','ratingdetails.*')
                ->leftjoin('job_applications', 'job_applications.id', 'ratingdetails.job_application_id') 
                  ->leftjoin('jobrecruitments', 'jobrecruitments.id', 'job_applications.jobrecruitment_id')                 
                ->where('ratingdetails.interviewround',$type)
                ->whereNull('job_applications.deleted_at')
                ->orderBy('ratingdetails.updated_at', 'desc')
               ->get();


  if($rating){
   return $this->successJson('Rating Details',200,$rating);
  }else{
   return $this->errorJson('Data no founds',404);

  }
}


//end
}
