<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jdstore;
use DB;
use Illuminate\Validation\Rule;
class JdController extends Controller
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
    public function index($id=null){
        if($id){
           $jd=Jdstore::where('id',$id)->get();
          }else{
          $jd=Jdstore::get();
         }
         if($jd){
            return $this->successJson('Job Description List',200,$jd);
        }else{
            return $this->errorJson('Data not founds',404);
        }
    }

     public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'designation' => 'required|unique:jdstores',
            'job_type' => 'required',
            'job_description' => 'required',
            'responsibilities' => 'required',
            'requirements' => 'required',
            
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }
        try{
            \DB::beginTransaction();
            $jd=new Jdstore;
            $jd->designation=$request->designation;
            $jd->job_type=$request->job_type;
            $jd->job_description=$request->job_description;
            $jd->responsibilities=$request->responsibilities;
            $jd->requirements=$request->requirements;
            $jd->save();
                          
            \DB::commit();
              return $this->successJson('Job Description Saved Successfully',200,$jd);
        }
        catch (\Exception $e) {
            \DB::rollBack();
            return $this->errorJson('oops something went wrong please try again',500,$e->getMessage());
        }
     }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'designation' => ['required',Rule::unique('jdstores')->ignore($id)],
            'job_type' => 'required',
            'job_description' => 'required',
            'responsibilities' => 'required',
            'requirements' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }
        try{
            \DB::beginTransaction();
            $jd=Jdstore::find($id);

            if(empty($jd)){
                return $this->errorJson('not found Details',404);
            }else{

                $jd->designation=$request->designation;
                $jd->job_type=$request->job_type;
                $jd->job_description=$request->job_description;
                $jd->responsibilities=$request->responsibilities;
                $jd->requirements=$request->requirements;
                $jd->save();                          
                \DB::commit();
                return $this->successJson('Job Description Updated Successfully',200,$jd);
            }
        }
        catch (\Exception $e) {
            \DB::rollBack();
            return $this->errorJson('oops something went wrong please try again',500,$e->getMessage());
        }
    }

    public function delete($id){
        Jdstore::destroy($id);
        return $this->successJson('Job Description Deleted Successfully',200);
    }
}
