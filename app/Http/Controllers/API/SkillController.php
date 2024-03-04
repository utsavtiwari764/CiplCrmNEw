<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSkill;
use App\JobCategory;
use App\Skill;
class SkillController extends Controller
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
        
            $skill = Skill::with(['category'])->get();
            if(!empty($skill)){
                return $this->successJson('Skills Details',200,$skill);
            }else{
                return $this->successJson('not found Details',200);
            }
         
        
    }
 
    public function add(StoreSkill $request){
        if(!auth()->user()->cans('add_skills')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
           
        $skill=Skill::create(['name' => $request->name, 'category_id' => $request->category_id]);
            if(!empty($skill)){
                return $this->successJson('Skill Added Successfully',200,$skill);
            }else{
                return $this->successJson('not found Details',200);
            }
        }
    }
    public function edit(Request $request,$id=null){
        if(!auth()->user()->cans('edit_skills')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $skilledit = Skill::with(['category'])->find($id);
            if(!empty($skilledit)){
                return $this->successJson('Details',200,$skilledit);
            }else{
                return $this->successJson('not found Details',200);
            }
       }
    }
    
    public function update(StoreSkill $request,$id=null){
        if(!auth()->user()->cans('edit_skills')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        if(!empty($id)){
            $skill = Skill::find($id);
            if(empty($skill)){
                return $this->errorJson('oops something went wrong please try again',404);
            }else{
                $skill->name = $request->name;
                $skill->category_id=$request->category_id;
                $skill->save();
                return $this->successJson('Skill Updated Successfully',200,$skill);
            }
           
        }else{
            return $this->successJson('not found Details',200);
        }
        }
    }

    public function destroy($id)
    {       
        if(!auth()->user()->cans('delete_skills')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{

        Skill::destroy($id);
        return $this->successJson('Skill Deleted Successfully',200);
        }
    }

   public function search($id){
        $skill = Skill::where('category_id',$id)->get(['id','name']);
        if(empty($skill)){
            return $this->errorJson('oops something went wrong please try again',404);
        }else{
            
            return $this->successJson('Skill details',200,$skill);
        }
    }

   public function search1($id){
        $skill = Skill::where('category_id',$id)->get(['id','name']);
        if(empty($skill)){
            return $this->errorJson('oops something went wrong please try again',404);
        }else{
            
            return $this->successJson('Skill details',200,$skill);
        }
    }

}
