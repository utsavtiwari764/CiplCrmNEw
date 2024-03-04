<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Department;
class DepartmentController extends Controller
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
        //return $this->successJson('Department Details',200);
        
        $categories = Department::all();
        if(!empty($categories)){
            return $this->successJson('Department Details',200,$categories);
        }else{
            return $this->successJson('not found Details',200,$categories);
        }
       
    }
    public function add(Request $request){
       
                if(!auth()->user()->cans('add_department')){
                    return $this->errorJson('Not authenticated to perform this request',403);
                }else{
                    $validator = Validator::make($request->all(), [
                        'name' => 'required|unique:departments',
                    
                    ]);
                    if ($validator->fails()) { 
                        return response()->json(['error'=>$validator->errors()], 422);
                    }
                    $category=Department::create(['name' => $request->name]);
                    if(!empty($category)){
                        return $this->successJson('Department Added Successfully',200,$category);
                    }else{
                        return $this->successJson('something else wrong',200);
                    }
                }
       
    }
    
    public function update(Request $request,$id){
        if(!auth()->user()->cans('edit_department')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            if(!empty($id)){
                $qualification = Department::find($id);
                
                if(empty($qualification)){
                    return $this->errorJson('not found Details',404);
                }else{

                    $validator = Validator::make($request->all(), [
                        'name' => ['required',Rule::unique('departments')->ignore($id)],
                         
                    ]);
                    if ($validator->fails()) {
                       return response()->json(['error'=>$validator->errors()], 422);
                    }  
                   $qualification->name=$request->name;
                   $qualification->save();
                    return $this->successJson('Department Updated Successfully',200,$qualification);
                }
                
            }else{
                return $this->successJson('not found Details',200);
            }
        }
    }

    public function destroy($id)
    {
        if(!auth()->user()->cans('delete_department')){

            return $this->errorJson('Not authenticated to perform this request',403);
        }else{

            Department::destroy($id);
            return $this->successJson('Department Delete Successfully',200);
        }
    }
}
