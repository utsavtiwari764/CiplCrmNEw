<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Certification;
use App\Http\Requests\StoreCertification;
use App\Http\Requests\UpdateCertification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
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


    public function unread(Request $request){
        $res = [
            'unread' => auth()->user()->unreadnotifications,
            'read' => auth()->user()->notifications()->whereNotNull('read_at')->get(),
        ];
        
            return $this->successJson('Qualification Details',200,$res);
        
    }

    public function index(){
        
        $categories = Certification::all();
        if(!empty($categories)){
            return $this->successJson('Certification Details',200,$categories);
        }else{
            return $this->successJson('not found Details',200,$categories);
        }
       
    }

public function index1(){
        
        $categories = Certification::all();
        if(!empty($categories)){
            return $this->successJson('Certification Details',200,$categories);
        }else{
            return $this->successJson('not found Details',200,$categories);
        }
      

    }

   
  public function add(Request $request){
        if(!auth()->user()->cans('add_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{

         $validator = Validator::make($request->all(), [
                 'name' => 'required|unique:certifications',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 422);

            }


        $category=Certification::create(['name' => $request->name]);
        if(!empty($category)){
            return $this->successJson('Certification Added Successfully',200,$category);
        }else{
            return $this->successJson('something else wrong',200);
        }
       }
    }

    public function addold(StoreCertification $request){
        if(!auth()->user()->cans('add_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $category=Certification::create(['name' => $request->name]);
        if(!empty($category)){
            return $this->successJson('Certification Added Successfully',200,$category);
        }else{
            return $this->successJson('something else wrong',200);
        }
       }
    }
    public function edit(StoreCertification $request,$id=null){
        if(!auth()->user()->cans('edit_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        $category = Certification::find($id);
        if(!empty($category)){
            return $this->successJson('Details',200,$category);
        }else{
            return $this->successJson('not found Details',200);
        }
        }
    }
    public function update(UpdateCertification $request,$id=null){
        if(!auth()->user()->cans('edit_category')){
            return $this->errorJson('Not authenticated to perform this request',403);
        }else{
        if(!empty($id)){
            $category = Certification::find($id);
            if($category){
                $category->name = $request->name;
                $category->save();
                return $this->successJson('Certification Updated Successfully',200,$category);
            }else{
                return $this->errorJson('Oops something went wrong please try again',404);
                 
            }
           
        }else{
            return $this->successJson('not found Details',200);
        }
        }
    }
    public function destroy($id)
    {
     if(!auth()->user()->cans('delete_category')){

        return $this->errorJson('Not authenticated to perform this request',403);
    }else{

        Certification::destroy($id);
        return $this->successJson('Certification Delete Successfully',200);
    }
   }
}
