<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\JobApplication;
class AdminApplicationArchiveController extends Controller
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

    public function data(Request $request)
    {
        
        if(!auth()->user()->cans('view_job_applications')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
       
            $jobApplications = JobApplication::with([
            'job',
            'qualification',
            'subqualifications',
            'status:id,status',
        ])->onlyTrashed();

            $jobApplications = $jobApplications->get();
            if($jobApplications){
        return $this->successJson('Candidate database list Successfully',200,$jobApplications);
        }else{
        return $this->errorJson('Candidate database not founds',404);
        }
        
        }
    }

}
