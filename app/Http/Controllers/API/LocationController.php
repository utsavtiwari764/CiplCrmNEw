<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\User;
use App\JobLocation;
class LocationController extends Controller
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
         if(!auth()->user()->cans('view_locations')){
                return $this->errorJson('Not authenticated to perform this request',403);
            }else{
       
        $locations = JobLocation::all();
        
             
            if(!empty($locations )){
                return $this->successJson('Location Details',200,$locations);
            }else{
                return $this->errorJson('not found Details',404);
            }
        } 
      
    }


}
