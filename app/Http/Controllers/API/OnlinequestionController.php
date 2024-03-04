<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Onlinequestion;
use App\JobCategory;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Onlinequestion\StoreOnlinequestion;
use Illuminate\Validation\Rule;
class OnlinequestionController extends Controller
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
        
        $questions = Onlinequestion::with(['category'])->orderBy('id', 'DESC')->get(['id','question','option_a','option_b','option_c','option_d','answer','marks','question_type','jobcategory_id','status']);
        if(!empty($questions)){
            return $this->successJson('Question Details',200,$questions);
        }else{
            return $this->successJson('Data  not founds',404);
        }
       
    }

    public function Store(Request $request){
        if(!auth()->user()->cans('add_question')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            $validator = Validator::make($request->all(), [
                'question_type' => 'required',
                'marks'         =>'required',
                'jobcategory_id' => 'required',
                'question' => 'required|unique:onlinequestions',
               ]);
            if ($validator->fails()) {

                
                     return response()->json(['error'=>$validator->errors()], 422);
            }
           
            $onlinequestion =new  Onlinequestion();
            if($request->question_type!='description'){ 
                $validator = Validator::make($request->all(), [
                    'option_a' => 'required',
                    'option_b'         =>'required',
                    'option_c' => 'required',
                    'option_d' => 'required',
                    'answer'  =>'required'
                ]);
                if ($validator->fails()) {
                     return response()->json(['error'=>$validator->errors()], 422);
                }             
                $onlinequestion->answer = $request->answer;
                $onlinequestion->option_a=$request->option_a;
                $onlinequestion->option_b=$request->option_b;
                $onlinequestion->option_c=$request->option_c;
                $onlinequestion->option_d=$request->option_d;
            }         
            $onlinequestion->question = $request->question;
            $onlinequestion->marks = $request->marks;
           if($request->question_type!='description'){ 

           $onlinequestion->question_type = 'optional';
          }else{
           $onlinequestion->question_type = 'description';
           }

            
            $onlinequestion->jobcategory_id = $request->jobcategory_id;           
            if(empty($request->status)){
                $onlinonlinequestionexam->status="enable";
            }else{
               
                $onlinequestion->status=$request->status;
            }
            $onlinequestion->save();
      
        if(!empty($onlinequestion)){
            return $this->successJson('Question Added Successfully',200,$onlinequestion);
        }else{
            return $this->errorJson('something else wrong',400);
        }
       }
    }
    public function edit(Request $request,$id=null){
        if(!auth()->user()->cans('update_question')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $question = Onlinequestion::with(['category'])->find($id);
        if(!empty($question)){
            return $this->successJson('Question Details',200,$question);
        }else{
            return $this->errorJson('not found Details',404);
        }
        }
    }
    public function update(Request $request,$id){
        if(!auth()->user()->cans('update_question')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
            if(!empty($id)){
                $onlinequestion = Onlinequestion::find($id);
                
                if(empty($onlinequestion)){
                    return $this->errorJson('not found Details',404);
                }else{

                    $validator = Validator::make($request->all(), [
                        'question' => ['required',Rule::unique('onlinequestions')->ignore($id)],
                        'question_type' => 'required',
                        'marks'         =>'required',
                        'jobcategory_id' => 'required',
                    ]);
                    if ($validator->fails()) {
                        return $this->errorJson( $validator->messages(),422);
                    }  
                    if($request->question_type!='description'){
                        
                        $validator = Validator::make($request->all(), [
                            'option_a' => 'required',
                            'option_b' =>'required',
                            'option_c' => 'required',
                            'option_d' => 'required',
                            'answer'   =>'required'
                        ]);
                        if ($validator->fails()) {
                            return $this->errorJson( $validator->messages(),422);
                        }    
                        $onlinequestion->answer = $request->answer;
                        $onlinequestion->option_a=$request->option_a;
                        $onlinequestion->option_b=$request->option_b;
                        $onlinequestion->option_c=$request->option_c;
                        $onlinequestion->option_d=$request->option_d;
                    }    
                    $onlinequestion->marks = $request->marks;
                    $onlinequestion->question = $request->question;                
                    $onlinequestion->jobcategory_id = $request->jobcategory_id;
                    $onlinequestion->question_type = $request->question_type;
                    if(empty($request->status)){
                        $onlinonlinequestionexam->status="enable";
                    }else{
                    
                        $onlinequestion->status=$request->status;
                    }
                    $onlinequestion->save();
                    return $this->successJson('Question Updated Successfully',200,$onlinequestion);
                }
                
            }else{
                return $this->successJson('not found Details',200);
            }
        }
    }

     public function destroy($id){
        if(!auth()->user()->cans('delete_question')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{    
            Onlinequestion::destroy($id);
            return $this->successJson('Question Delete Successfully',200);
        }
     }
}
