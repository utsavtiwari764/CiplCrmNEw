<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use DB;
use App\Onlinequestion_Onlineexam;
use App\JobApplication;
use App\OnlineexamJobApplication;
use App\Mail\MyExamMail; 
use Illuminate\Support\Facades\Validator;
class ExamjobapplicationController extends Controller
{
    public function exmaassign(Request $request){
        $validator = Validator::make($request->all(), [
            'onlinexam_id' => 'required',
            'jobapplication_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
        } 
        if($request->jobapplication_id){        
        $application=JobApplication::find($request->jobapplication_id);
        if($application->email) {
            $check=OnlineexamJobApplication::where(['jobapplication_id'=>$request->jobapplication_id,'status'=>1])->count();
            if(empty($check)){
                $myEmail = $application->email;
            $save=OnlineexamJobApplication::create([
                                                    'jobapplication_id'=>$request->jobapplication_id,
                                                    'onlinexam_id'=>$request->onlinexam_id,
                                                    'status'=>1
                                                    ]);

            $details = [
                        'title' => 'Dear ',
                        'url' => 'http://localhost/project/hrm/public/admin/settings/smtp-settings',
                    ];
            Mail::to($myEmail)->send(new MyExamMail($details));
             return $this->successJson('Assign exam  and send mail successfully',200,$save);
            }else{
                return $this->successJson('Exam Already Assgined',200);
            }
            
           }else{
            return $this->successJson('Email Id not Founds',404);
           }
        } 
    }

    //Function Get List of Appliction
    public function exmaassignRemove(Request $request){
        $validator = Validator::make($request->all(), [
            'onlinexam_id' => 'required',
            'jobapplication_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
        } 
        if($request->jobapplication_id){        
            $application=OnlineexamJobApplication::where(['jobapplication_id'=>$request->jobapplication_id,'status'=>1])->delete();
            if(!empty($application)){
                return $this->successJson('JobApllication remove  Successfully',200);
            }else{
                return $this->errorJson('Questions not founds',404);
            }
        }
    }


    public function onlinexamlistbyapplication($id,$exam_id){
        try {
          
              $resource = OnlineexamJobApplication::findorfail($id); 
              
              $dt = Carbon::now()->format('Y-m-d');
              $currenttime =date('h:i A');
             // $onlineexam=Onlinexam::with('onlinequestion')->where('id',$exam_id)->whereDate('scheduled_date','=',$dt)->whereTime('scheduled_time', '<=', $currenttime)->get();
              $onlineexam=Onlinexam::with('onlinequestion')->where('id',$exam_id)->get();
              
              if($onlineexam->count()===0){
                  return $this->errorJson('exam not found',404);
              }
              
              $exam_attempt=$onlineexam[0]->attempt;
              $attempt=$resource->in_attempt;
              if($exam_attempt>=$attempt){
                  $resource->in_attempt=$attempt+1;
                  $resource->save();
                  return $this->successJson('Exam submit  Successfully',200,$onlineexam);
              }else{
                  return $this->errorJson('Not authenticated to perform this request attempt',403);
              }
             
          } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
              return $this->errorJson('404 not found',404);
          }
  }
}
