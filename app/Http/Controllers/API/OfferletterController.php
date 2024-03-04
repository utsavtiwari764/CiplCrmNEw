<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Offerletter;
use DB;
use Illuminate\Support\Facades\File;

use Illuminate\Validation\Rule;
class OfferletterController extends Controller
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
  
    public function index(){
        $jd=Offerletter::get();
        if($jd){
            return $this->successJson('Offer Letter List',200,$jd);
        }else{
            return $this->errorJson('Data not founds',404);
        }
    }
    public function store(Request $request){
        
        
        $validator = Validator::make($request->all(), [
            'templatename' => 'required|unique:offerletters',
            'contents' => 'required',
             
            
            
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }
        try{
            \DB::beginTransaction();
            $offer=new Offerletter;
            if($request->hasFile('signature')) {
            $path = public_path('signature/');
            if(!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image = $request->file('signature');
            $name = time().'.'.$image->getClientOriginalExtension();            
            $image->move($path, $name);
            $offer->signature='public/signature/'.$name;
           }

            $offer->templatename=$request->templatename;
            $offer->contents=$request->contents;            
            $offer->save();                          
            \DB::commit();
            return $this->successJson('Offer Letter Saved Successfully',200,$offer);
        }
        catch (\Exception $e) {
            \DB::rollBack();
            return $this->errorJson('oops something went wrong please try again',500,$e->getMessage());
        }

    }

  public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'templatename' => ['required',Rule::unique('offerletters')->ignore($id)],
            'contents' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);
        }
        try{
            \DB::beginTransaction();
            $jd=Offerletter::find($id);

            if(empty($jd)){
                return $this->errorJson('not found Details',404);
            }else{
                
                $jd->templatename=$request->templatename;
                $jd->contents=$request->contents;
                 if($request->hasFile('signature')) {
            $path = public_path('signature/');
            if(!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image = $request->file('signature');
            $name = time().'.'.$image->getClientOriginalExtension();            
            $image->move($path, $name);
            $jd->signature='public/signature/'.$name;
           }

                $jd->save();                          
                \DB::commit();
                return $this->successJson('Offer Letter Updated Successfully',200,$jd);
            }
        }
        catch (\Exception $e) {
            \DB::rollBack();
            return $this->errorJson('oops something went wrong please try again',500,$e->getMessage());
        }
    }

    public function delete($id){
        Offerletter::destroy($id);
        return $this->successJson('Offerletter Deleted Successfully',200);
    }

}
