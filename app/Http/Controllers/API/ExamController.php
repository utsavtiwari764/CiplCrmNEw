<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Onlinexam;
use App\Onlinequestion;
use App\JobCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use App\Onlinequestion_Onlineexam;
use App\JobApplication;
use App\OnlinexamJobApplication;
use App\Mail\MyExamMail;
use Mail;
use App\EmailSetting;
use App\SmtpSetting;
use App\Onlinexamresult;
class ExamController extends Controller
{
    //

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
    public function index(){
      
      $onlineexam=Onlinexam::orderBy('id', 'desc')->get();   
     
      
      return $this->successJson('Exam Details',200,$onlineexam); 
    }

    public function view($id){
      
        $onlineexam=Onlinexam::with('category')->where('id',$id)->first();   
       // $questions=[];
    
        return $this->successJson('Exam Details',200,$onlineexam); 
      }

    public function Store(Request $request){
        if(!auth()->user()->cans('add_exam')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'time_duration'         =>'required',
               // 'scheduled_time' => 'required|date_format:h:i:s',
               // 'scheduled_date' => 'required|date',
                'attempt'       =>'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 422);
            }
            $onlinexam =new  Onlinexam();
            $onlinexam->name           = $request->name;
            
            $onlinexam->time_duration  = $request->time_duration;
          //  $onlinexam->scheduled_time = date('h:i A',strtotime($request->scheduled_time)); 
           // $onlinexam->scheduled_date = Carbon::parse($request->scheduled_date)->format('Y-m-d');
            $onlinexam->attempt       =$request->attempt;
            $onlinexam->description   =$request->description;
            if(empty($request->status)){
                $onlinexam->status="enable";
            }else{
                $onlinexam->status=$request->status;
            }
            $onlinexam->save();
      
        if(!empty($onlinexam)){
            return $this->successJson('Exam Added Successfully',200,$onlinexam);
        }else{
            return $this->errorJson('something else wrong',403);
        }
       }
    }


    public function destroy($id){
        if(!auth()->user()->cans('delete_exam')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{    
            Onlinexam::destroy($id);
            return $this->successJson('Exam Delete Successfully',200);
        }
    }

    public function edit($id){
        if(!auth()->user()->cans('update_exam')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $exams = Onlinexam::find($id);
        if(!empty($exams)){
            return $this->successJson('Exam Details',200,$exams);
        }else{
            return $this->errorJson('not found Details',404);
        }
        }
    }

    public function update(Request $request,$id){
        if(!auth()->user()->cans('edit_exam')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            if(!empty($id)){
                $onlinexam = Onlinexam::find($id);
                
                if(empty($onlinexam)){
                    return $this->errorJson('not found Details',404);
                }else{

                    $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'time_duration'         =>'required',                      
                        'attempt'       =>'required',
                    ]);
                    if ($validator->fails()) {
                        return $this->errorJson( $validator->messages(),422);
                    }  
                     
                    $onlinexam->name           = $request->name;
                    $onlinexam->time_duration  = $request->time_duration;
                   
                    $onlinexam->attempt       =$request->attempt;
                    $onlinexam->description   =$request->description;
                    if(empty($request->status)){
                        $onlinexam->status="enable";
                    }else{
                      $onlinexam->status="enable";
                    }
                    $onlinexam->save();
                    return $this->successJson('Exam Updated Successfully',200,$onlinexam);
                }
                
            }else{
                return $this->successJson('not found Details',200);
            }
        }
    }


    public function Assignquestion(Request $request){
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required',
            'questions.*'=>'required',
            'questions'=>'required',
            
        ]);
        if ($validator->fails()) {
             return response()->json(['error'=>$validator->errors()], 422);
        } 
 
        $ins='';
        $total_question=count($request->questions);
    
        $onlinexam = Onlinexam::find($request->exam_id);
       // return $this->successJson('Question Successfully Assign',200,$onlinexam );

        $onlinexam->total_question=$total_question;
        $onlinexam->save();
        
       foreach($request->questions as $question){
        $check=Onlinequestion_Onlineexam::where(['onlinequestion_id'=>$question,'onlinexam_id'=>$request->exam_id])->count();
       if($check===0){
            //$marks=work on 
            $onlinequestion=Onlinequestion::find($question);
            $ins=Onlinequestion_Onlineexam::create([
                'onlinequestion_id'=>$question,
                'marks'=>$onlinequestion->marks,
                'onlinexam_id'=>$request->exam_id
            ]);
           
        }
         
        }
        if(!empty($ins)){
            return $this->successJson('Question Successfully Assign',200);
        }else{
            return $this->errorJson('Question already added ',403);
        }
    }

    public function getExamQuestion($exam_id){
        if($exam_id){
            $onlineexam=Onlinexam::with('onlinequestion')->where('id',$exam_id)->get();   
            if(!empty($onlineexam)){
                return $this->successJson('Qestions list',200,$onlineexam);
                }else{
                    return $this->errorJson('Questions not founds',200);
                }
        }else{
            return $this->errorJson('Exam id not empty',404);
        }
    }

    public function RemoveExamQuestion($exam_id,$question_id){
        if($exam_id && $question_id){
            $onlineexam=Onlinequestion_Onlineexam::where(['onlinequestion_id'=>$question_id,'onlinexam_id'=>$exam_id])->delete();   
            if(!empty($onlineexam)){
                return $this->successJson('Exam question remove  Successfully',200);
                }else{
                    return $this->errorJson('Questions not founds',200);
                }
        }else{
            return $this->errorJson('not found Details',404);
        }
    }
    
 //Aplicaton exam assign
    public function exmaassign(Request $request){
        $validator = Validator::make($request->all(), [
            'onlinexam_id' => 'required',
            'jobapplication_id.*'=>'required',
            'scheduled_time' => 'required',
            'scheduled_date' => 'required|date',
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
        } 
        if($request->jobapplication_id){  
            
           
            if(!is_null($request->jobapplication_id)){

                foreach ($request->jobapplication_id as $app_id) {
                 $application=JobApplication::find($app_id);
                    $check=OnlinexamJobApplication::where(['jobapplication_id'=>$app_id,'status'=>1])->count();
 

                    if(empty($check)){
                        try{
                            \DB::beginTransaction();
                            $myEmail = $application->email; 
           
                            $save=OnlinexamJobApplication::create([
                                                                    'jobapplication_id'=>$app_id,
                                                                    'onlinexam_id'=>$request->onlinexam_id,
                                                                    'status'=>1,
                                                                    'scheduled_time'=>date('h:i A',strtotime($request->scheduled_time)),
                                                                    'scheduled_date'=> Carbon::parse($request->scheduled_date)->format('Y-m-d')
                                                                    ]);
                                $rid=\Crypt::encrypt($save->id);
                                $urls='https://cipcrm.org.in/interview/'.$rid;
                            $details = [
                                        'title' => 'Online Interview Test',
                                        'url' => $urls,
					'description'=>$save
                                    ];
                           
                            Mail::to($myEmail)->send(new MyExamMail($details));
                              $application->status_id=2;
                            $application->save();
                            \DB::commit();
                             return $this->successJson('Assign exam  and send mail successfully',200,$save);


                        }catch(\Exception $e){
                            \DB::rollBack();
                            return $this->errorJson('404 not found',500, $e->getMessage());
                        }
                    } 
                     else{
                        return $this->successJson('Test already taken !',409);
                    }
                   
                }
            }else{
                return $this->errorJson('jobapplication id not founds',404);
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
            $application=OnlinexamJobApplication::where(['jobapplication_id'=>$request->jobapplication_id,'status'=>1])->delete();
            if(!empty($application)){
                return $this->successJson('JobApllication remove  Successfully',200);
            }else{
                return $this->errorJson('Questions not founds',404);
            }
        }
    }
    public function onlinexamlistbyapplicationold($id,$exam_id){
        $ids=base64_decode($id);
        $exam_ids=base64_decode($exam_id);
        $dt = Carbon::now()->format('Y-m-d');
        $currenttime =date('h:i A');
        try {
                \DB::beginTransaction();
                       $dt = Carbon::now()->format('Y-m-d');
                       $currenttime =date('h:i A');
                       $onlineexamlist = OnlinexamJobApplication::where('id',$ids)->whereTime('scheduled_time', '<=', $currenttime)->count(); 
                       $onlineexam=Onlinexam::with('onlinequestion')->where('id',$exam_ids)->get();
                       
                       if($onlineexamlist=='0'){
                           return $this->errorJson('exam not found',404);
                       }
                       $resource = OnlinexamJobApplication::findorfail($ids); 
                       $exam_attempt=$onlineexam[0]->attempt;
                       $attempt=$resource->in_attempt;
                       if($exam_attempt>=$attempt){
                           $resource->in_attempt=$attempt+1;
                           $resource->save();
                              $data=['onlineexam'=>$onlineexam,'jobApplication'=>$resource];
                             \DB::commit();
                           return $this->successJson('Exam list',200,$data);
                       }else{
                           return $this->errorJson('Not authenticated to perform this request attempt',403);
                       }
                      
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \DB::rollBack();
            return $this->errorJson('404 not found',404);
        }
    }
    
  public function onlinexamlistbyapplication($id){
        
        try {
                \DB::beginTransaction();
                        $ids=\Crypt::decrypt($id);
                       
                       $dt = Carbon::now()->format('Y-m-d');
                       $currenttime =date('h:i A');
                      // $onlineexamlist = OnlinexamJobApplication::with('onlineexam')->where('id',$ids)->whereTime('scheduled_time', '<=', $currenttime)->get(); 
                       $onlineexamlist = OnlinexamJobApplication::with('onlineexam')->where('id',$ids)->get(); 

                       if($onlineexamlist->count()===0){
                        return $this->errorJson('exam not found',404);
                    }
                    
                    $resource = OnlinexamJobApplication::findorfail($ids); 
                    $exam_attempt=$onlineexamlist[0]->onlineexam->attempt;
                    $attempt=$resource->in_attempt;
                    if($exam_attempt>=$attempt){
                        $resource->in_attempt=$attempt+1;
                        $resource->save();
                          
                          \DB::commit();
                        return $this->successJson('Exam lists',200,$onlineexamlist);
                    }else{
                        return $this->errorJson('Not authenticated to perform this request attempt',403);
                    }

                        
                      
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \DB::rollBack();
            return $this->errorJson('404 Page not found',404);
        }
    }
    
    public function examresult_save(Request $request){
        $validator = Validator::make($request->all(), [
             'onlinequestion_id.*' => 'required',
            'onlinexamjobsapp_id'=>'required',
            'answer_by_jobapplication.*'=>'required',
            
            'marks.*'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
        } 
        else{
            try {
                \DB::beginTransaction();
               Onlinexamresult::where('onlinexamjobsapp_id', $request->onlinexamjobsapp_id)->delete();
                 $onlineexamjobapplication = OnlinexamJobApplication::find($request->onlinexamjobsapp_id); 
                 $onlineexamjobapplication->examstatus='submit';
                 $onlineexamjobapplication->save();
                  $jobapplication=JobApplication::find($onlineexamjobapplication->jobapplication_id);
                 $jobapplication->status_id='20';
                 $jobapplication->save(); 

                if(!is_null($request->examssubmit)){
                  foreach ($request->examssubmit as $key=>$value) {
                    $result = new Onlinexamresult();
            	    $result->onlinequestion_id = $value['onlinequestion_id'];
            	    $result->onlinexamjobsapp_id = $request->onlinexamjobsapp_id;
            	    $result->answer_by_jobapplication=$value['answer_by_jobapplication'];
                    $result->true_answer=$value['true_answer'];
                    $result->marks=$value['marks'];
                    $result->save();
                  }
                 }                
                
                  \DB::commit();
                    return $this->successJson('Exam submit  Successfully',200,$result);
               
            }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             \DB::rollBack();
                return $this->errorJson('404 not found',404);
            }
        }
    }

  public function resultsold($id=null){
        $shedule = OnlinexamJobApplication::with('onlineexamlist')->where('id',$id)->get();
        if($shedule){
            return $this->successJson('Exam result',200,$shedule);
        }
        else{
            return $this->errorJson('404 not found',404);
        }
    } 

   public function results($id=null){
        
        $exam = OnlinexamJobApplication::with('onlineexamdeatils')->where(['jobapplication_id'=>$id])->first();
//$exam = OnlinexamJobApplication::with('onlineexamdeatils')->where(['jobapplication_id'=>$id,'examstatus'=>'submit'])->first();

       /* $shedule = Onlinequestion_Onlineexam::select('onlinequestion_onlinexam.*','onlinexamresults.id as onlineexam_student_result_id','onlinequestions.id as question_id','onlinequestions.question as question_name','onlinequestions.question_type','onlinequestions.option_a','onlinequestions.option_b','onlinequestions.option_c','onlinequestions.option_d','onlinequestions.answer as true_answer','onlinexamresults.answer_by_jobapplication')
                  ->join('onlinexamresults','onlinexamresults.onlinequestion_id','onlinequestion_onlinexam.onlinequestion_id')
                  ->join('onlinequestions', 'onlinequestions.id', 'onlinexamresults.onlinequestion_id')
                  ->join('onlinexam_job_applications','onlinexam_job_applications.id','onlinexamresults.onlinexamjobsapp_id')
                  ->where('onlinexam_job_applications.jobapplication_id',$id)->groupBy('onlinexamresults.id'); */

   $shedule = Onlinequestion_Onlineexam::select('onlinequestion_onlinexam.*','onlinexamresults.id as onlineexam_student_result_id','onlinequestions.id as question_id','onlinequestions.question as question_name','onlinequestions.question_type','onlinequestions.option_a','onlinequestions.option_b','onlinequestions.option_c','onlinequestions.option_d','onlinequestions.answer as true_answer','onlinexamresults.answer_by_jobapplication')
                  ->join('onlinexamresults','onlinexamresults.onlinequestion_id','onlinequestion_onlinexam.onlinequestion_id')
                  ->join('onlinequestions', 'onlinequestions.id', 'onlinexamresults.onlinequestion_id')
                  ->join('onlinexam_job_applications','onlinexam_job_applications.id','onlinexamresults.onlinexamjobsapp_id')
                  ->where('onlinexam_job_applications.jobapplication_id',$id)->groupBy('onlinexamresults.id')->get();

        
      //  $shedule = $shedule->get(); 
 
        $data=[
               'JobApplication'=>$exam,
               'studentresult'=>$shedule
        ];

        if($exam){
            return $this->successJson('Exam result',200,$data);
        }
        else{
            return $this->errorJson('data not found',404);
        }
    } 

  public function resultslist(){
        $data = JobApplication::join('application_status', 'application_status.id', '=', 'job_applications.status_id')
        ->where('application_status.status', 'exam submited')->get();
        if($data){
            return $this->successJson('Exam result',200,$data);
        }
        else{
            return $this->errorJson('404 not found',404);
        }
    }


}
