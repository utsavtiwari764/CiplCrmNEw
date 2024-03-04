<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Role;
use App\RoleUser;
use App\User; 
use App\Jobassign; 
use Illuminate\Support\Facades\DB; 
use App\Mail\AssignLeadMail;
use Mail;
use App\Job; 
use App\Jobrecruitment;
class LeadMangementController extends Controller
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
 
  public function leadDetails($id){
       $datalist=Jobassign::with(['leadassigned','transfer'])->where('jobrecruitment_id',$id)->get();
       return $this->successJson('Lead Details',200,$datalist);
   }
    public function assign(Request $request){
        $validator = Validator::make($request->all(), [
            'jobassigns'=>'required|array',
             'job_id'=>'required',
           'jobrecruitment_id'=>'required',
           ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }else{
              try {
                \DB::beginTransaction();   
                if(!is_null($request->jobassigns)){
                     $jobrecruitment = Jobrecruitment::where(['job_id'=>$request->job_id,'id'=>$request->jobrecruitment_id])->first();
  

                    $jobrecruitment->status = 1;
                    $jobrecruitment->assign_id = auth()->user()->id;	
                    $jobrecruitment->save();
                     foreach($request->jobassigns as $key=>$value) {  
                   
                        $jobassign=new Jobassign;
                         $jobassign->job_id=$request->job_id; 
                        $jobassign->jobrecruitment_id=$request->jobrecruitment_id;
                        $jobassign->assign_id = auth()->user()->id;	
                        $jobassign->position = $value['no_of_possition'];	
                        $jobassign->status = 'Fresh Lead';
                        $jobassign->user_id=$value['user_id'];	
                        $jobassign->save();
                        $user=User::where('id',$value['user_id'])->first();
                        $myEmail = $user->email;
                        $details = [
                            'title' => 'Lead assign',
                            'url' => 'http://cipcrm.org.in/'
                        ];   
                      // Mail::to($myEmail)->send(new AssignLeadMail($details));
          
                    }
                }
                 \DB::commit();
              // $jobrecruitment = Jobrecruitment::with(['category','skills','qualification','subqualifications','replacements','anycertification','assignedetails'])->where(['job_id'=>$request->job_id,'id'=>$request->Jobrecruitment_id])->first();
                return $this->successJson('Lead Assigned Successfully',200);
            }catch (Exception $e) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$e);
            }

        }
     }


public function transfer(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
           'no_of_possition'=>'required'
           ]);
        if ($validator->fails()) {
             return response()->json(['error'=>$validator->errors()], 422);
        }else{

        $datalist=Jobassign::find($id);
        $totallead=$datalist->position;
        //update lead 
        if($totallead >= $request->no_of_possition){
            $datalist->position=$totallead-$request->no_of_possition;
            $datalist->status = 'Transfer Lead';
            $datalist->transfer=$request->user_id;	
            $datalist->save();
           
        }else{

        }
        $jobassign=new Jobassign;
        $jobassign->job_id=$datalist->job_id; 
        $jobassign->jobrecruitment_id=$datalist->jobrecruitment_id;
        $jobassign->assign_id = auth()->user()->id;	
        $jobassign->position = $request->no_of_possition;	
        $jobassign->status = 'Fresh Lead';
        $jobassign->user_id=$request->user_id;	
        $jobassign->save();
        $user=User::where('id',$request->user_id)->first();
        $myEmail = $user->email;
        $details = [
            'title' => 'Lead assign',
            'url' => 'http://cipcrm.org.in/'
        ];   
      // Mail::to($myEmail)->send(new AssignLeadMail($details));

      
        return $this->successJson('Lead Transfer successfully',200,$datalist);
        }
    }



public function assign1(Request $request){
        $validator = Validator::make($request->all(), [
           'job_id' => 'required',
           'user_id' => 'required',
           'Jobrecruitment_id'     =>'required',
           ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }else{
  //return $this->successJson('Job Assign successfully',200,$request->all());

            try {

                \DB::beginTransaction();
              
             $jobrecruitment = Jobrecruitment::with(['category','skills','qualification','subqualifications','replacements','anycertification','assignBy','assigned'])->where(['job_id'=>$request->job_id,'id'=>$request->Jobrecruitment_id])->first();
                $jobrecruitment->status = 1;
		$jobrecruitment->assign_id = $request->user_id;
                $jobrecruitment->user_id=auth()->user()->id;		
                $jobrecruitment->save();
                $user=User::where('id',$request->user_id)->first();
                $myEmail = $user->email;
                $details = [
                    'title' => 'Lead assign',
                    'url' => 'http://cipcrm.org.in/'
                ];   
              // Mail::to($myEmail)->send(new AssignLeadMail($details));
               \DB::commit();
                return $this->successJson('Job Assign successfully',200,$jobrecruitment);
            }catch (Exception $e) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$e);
            }

        }
     }


    public function assignold(Request $request){
        $validator = Validator::make($request->all(), [
           'job_id' => 'required',
            'user_id' => 'required',
           ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }else{
  //return $this->successJson('Job Assign successfully',200,$request->all());

            try {

                \DB::beginTransaction();
               /* $jobassign = new Jobassign();
                $jobassign->job_id = $request->job_id;
                $jobassign->user_id = $request->user_id;
		
                $jobassign->save(); */
                $job = Job::where(['status'=>'1','id'=>$request->job_id])->first();
                  $job->status = 3;
		$job->assign_id = $request->user_id;		
                  $job->save();

                $user=User::where('id',$request->user_id)->first();
                $myEmail = $user->email;
                $details = [
                    'title' => 'Lead assign',
                    'url' => 'http://cipcrm.org.in/'
                ];   
              // Mail::to($myEmail)->send(new AssignLeadMail($details));
               \DB::commit();
                return $this->successJson('Job Assign successfully',200,$user);
            }catch (Exception $e) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$e);
            }

        }
     }

public function linkget($id){
        
        try {
               
            $erflist = Job::with(['department'])->where(['status'=>'2','erf_id'=>$id])->first();
            $jobrecruitment=Jobrecruitment::with(['category','skills','qualification','subqualifications','replacements','anycertification','jd'])->where('job_id',$erflist->id)->get();
           
               if($erflist){
                $data=['erfgroup'=>$erflist,
                        'jobrecruitment'=>$jobrecruitment,
               ];
                   return $this->successJson('ERf approval link',200,$data);
               }else{
                   return $this->errorJson('Already Approved',403);
               }
               
                }catch (\Throwable $th) {
                    
                    return $this->errorJson('ERF not founds',404,$th);
        }
     
    }  

 public function approved_by(Request $request,$id){
      
            try {
                \DB::beginTransaction();
                $job = Job::where(['status'=>'2','erf_id'=>$id])->first();
                if($job){
                    $job->status = $request->status;
                    $job->note=$request->note;
                    $job->save();
                    \DB::commit();
                    return $this->successJson('Job Approved',200);
                }else{
                    return $this->errorJson('oops something else wrong',403);
                }
                
            }catch (\Throwable $th) {
                \DB::rollBack();
                return $this->errorJson('something else wrong',403,$th);
            }
    }

}
