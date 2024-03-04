<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Reply;
 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Subqualification;

class SubqualificationController extends Controller
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
       
            $qualification = Subqualification::with('qualification')->get();
            if(!empty($qualification)){
                return $this->successJson('Subqualification Details',200,$qualification);
            }else{
                return $this->successJson('not found Details',404);
            }
        
    }

    public function add(Request $request){
        if(!auth()->user()->cans('add_qualification')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
               'qualification_id'=>'required',
                'name' => Rule::unique('subqualifications')->where(fn ($query) => $query->where('name', ucfirst($request->name))->where('qualification_id',$request->qualification_id))
          
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 422);
            }
        $ins=Subqualification::create(['name' => $request->name,'qualification_id'=>$request->qualification_id]);
        if(!empty($ins)){
            return $this->successJson('Specialization Added Successfully',200,$ins);
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
                $qualification = Subqualification::find($id);
                
                if(empty($qualification)){
                    return $this->errorJson('not found Details',404);
                }else{

                    $validator = Validator::make($request->all(), [
                        'name' => 'required',
                         'qualification_id'=>'required',
                    ]);
                    if ($validator->fails()) {
                        return $this->errorJson( $validator->messages(),422);
                    }  
                   $qualification->name=$request->name;
                   $qualification->qualification_id=$request->qualification_id;
                   $qualification->save();
                    return $this->successJson('Specialization Updated Successfully',200,$qualification);
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
        Subqualification::destroy($id);
        return $this->successJson('Subqualification Delete Successfully',200);
    }
   }

   public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'qualification_id' => 'required',
             
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
        }  
        $skill = Subqualification::where('qualification_id',$request->qualification_id)->get(['id','name']);
        if(empty($skill)){
            return $this->errorJson('oops something went wrong please try again',404);
        }else{
            
            return $this->successJson('Specialization details',200,$skill);
        }
    }
 public function search1(Request $request){
        $validator = Validator::make($request->all(), [
            'qualification_id' => 'required',
             
        ]);
        if ($validator->fails()) {
            return $this->errorJson( $validator->messages(),422);
        }  
        $skill = Subqualification::where('qualification_id',$request->qualification_id)->get(['id','name']);
        if(empty($skill)){
            return $this->errorJson('oops something went wrong please try again',404);
        }else{
            
            return $this->successJson('Specialization details',200,$skill);
        }
    }

}
