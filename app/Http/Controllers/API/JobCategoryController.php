<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Http\Requests\StoreJobCategory;
use App\JobCategory;
use App\Skill;
use Illuminate\Support\Facades\Validator;

class JobCategoryController extends Controller
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
        
        $categories = JobCategory::all();
        if(!empty($categories)){
            return $this->successJson('Job Category Details',200,$categories);
        }else{
            return $this->successJson('List not founds',404);
        }
       

    }

 public function index1(){
        
        $categories = JobCategory::all();
        if(!empty($categories)){
            return $this->successJson('Job Category Details',200,$categories);
        }else{
            return $this->successJson('List not founds',404);
        }
       

    }

    public function addold(StoreJobCategory $request){
        if(!auth()->user()->cans('add_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $category=JobCategory::create(['name' => $request->name]);
        if(!empty($category)){
            return $this->successJson('Job Category Added Successfully',200,$category);
        }else{
            return $this->successJson('something else wrong',500);
        }
       }
    }

   public function add(Request $request){
        if(!auth()->user()->cans('add_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
          $validator = Validator::make($request->all(), [
                'name' => 'required|unique:job_categories,name',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 422);

            }
        $category=JobCategory::create(['name' => $request->name]);
        if(!empty($category)){
            return $this->successJson('Job Category Added Successfully',200,$category);
        }else{
            return $this->successJson('something else wrong',500);
        }
       }
    }

    public function edit(StoreJobCategory $request,$id=null){
        if(!auth()->user()->cans('edit_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $category = JobCategory::find($id);
        if(!empty($category)){
            return $this->successJson('Details',200,$category);
        }else{
            return $this->successJson('not found Details',200);
        }
        }
    }
    public function update(StoreJobCategory $request,$id=null){
        if(!auth()->user()->cans('edit_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        if(!empty($id)){
            $category = JobCategory::find($id);
            if($category){
                $category->name = $request->name;
                $category->save();
                return $this->successJson('Job Category Updated Successfully',200,$category);
            }else{
                return $this->errorJson('oops something went wrong please try again',500);
                 
            }
           
        }else{
            return $this->successJson('Data not founds',404);
        }
        }
    }
    public function destroy($id)
    {
     if(!auth()->user()->cans('delete_category')){

        return $this->errorJson('Not authenticated to perform this request',403);
    }else{

        JobCategory::destroy($id);
        return $this->successJson('Job Category Deleted Successfully',200);
    }
   }
}
