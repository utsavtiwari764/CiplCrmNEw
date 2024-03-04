<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Onlinequestion;
use App\JobCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Qualification;
class QualificationController extends Controller
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

    public function index(){
        
            $qualification = Qualification::all();
            if(!empty($qualification)){
                return $this->successJson('Qualification Details',200,$qualification);
            }else{
                return $this->successJson('not found Details',200,$qualification);
            }
       
    }

  public function index1(){
        
            $qualification = Qualification::all();
            if(!empty($qualification)){
                return $this->successJson('Qualification Details',200,$qualification);
            }else{
                return $this->successJson('not found Details',200,$qualification);
            }
       
    }


    public function add(Request $request){
        if(!auth()->user()->cans('add_qualification')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:qualifications',
               
            ]);
            if ($validator->fails()) {
                 return response()->json(['error'=>$validator->errors()], 422);
            }
        $ins=Qualification::create(['name' => $request->name]);
        if(!empty($ins)){
            return $this->successJson('Qualification Added Successfully',200,$ins);
        }else{
            return $this->successJson('something else wrong',200);
        }
       }
    }

    public function update(Request $request,$id){
        if(!auth()->user()->cans('edit_qualification')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            if(!empty($id)){
                $qualification = Qualification::find($id);
                
                if(empty($qualification)){
                    return $this->errorJson('not found Details',404);
                }else{

                    $validator = Validator::make($request->all(), [
                        'name' => ['required',Rule::unique('qualifications')->ignore($id)],
                         
                    ]);
                    if ($validator->fails()) {
                         return response()->json(['error'=>$validator->errors()], 422);
                    }  
                   $qualification->name=$request->name;
                   $qualification->save();
                    return $this->successJson('Qualification Updated Successfully',200,$qualification);
                }
                
            }else{
                return $this->successJson('not found Details',200);
            }
        }
    }

    public function destroy($id)
    {
     if(!auth()->user()->cans('delete_qualification')){
        return $this->errorJson('Not authenticated to perform this request',403);
    }else{
        Qualification::destroy($id);
        return $this->successJson('Qualification Delete Successfully',200);
    }
   }
}
