<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EmailSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\StoreTeam;
use App\Http\Requests\UpdateTeam;
use App\Notifications\NewUser;
use App\Role;
use App\RoleUser;
use App\User; 
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Tests\HttpCache\StoreTest;
use Illuminate\Support\Facades\Validator;
class TeamController extends Controller
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
     
        $role=Role::all();
        if(!empty($role)){
            return $this->successJson('Role List',200,$role);
        }else{
            return $this->successJson('not found Details',200);
        }
     
   }
   public function Store(Request $request){
    if(!auth()->user()->cans('add_team')){
        return $this->errorJson('Not authenticated to perform this request',403);
    }else{
         
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'image' => 'image|max:2048',
            'mobile' => 'required|numeric',
            'role_id'=>'required'
        ]);
        if ($validator->fails()) {
             return response()->json(['error'=>$validator->errors()], 422);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->calling_code = '+91';
        $user->mobile = $request->mobile;
        if ($request->hasFile('image')) {
            $user->image = Files::uploadLocalOrS3($request->image,'profile');
        }
        $user->save();


        //attach role
        $user->roles()->attach($request->role_id);
       $user->notify(new NewUser($request->password));

        return $this->successJson('Team Created Successfully',200,$user);
    
    }

   }



   public function get() {
    
        $users = User::with(['roles.permissions.permission'])->get();
        if($users){
            return $this->successJson('Team List',200,$users);
        }else{
            return $this->errorJson('not found Details',404);
        }
     
   }

  public function filter($name){
    $users = User::where('name', 'LIKE', "%$name%")->get();
    if($users){
        return $this->successJson('Team List',200,$users);
    }else{
        return $this->errorJson('not found Details',404);
    }
   }
 public function changeRole(Request $request){
        //attach role

        $user = User::find($request->teamId);

        $RoleUser=RoleUser::where('user_id', $request->teamId)->delete();


        $user->roles()->attach($request->roleId);

         if($user){
        return $this->successJson('Update ',200,$user);
        }else{
            return $this->errorJson('not found Details',404);
        }
    }

public function destroy($id)
    {
         if(!auth()->user()->cans('delete_team')){
        return $this->errorJson('Not authenticated to perform this request',403);
    }else{

        User::destroy($id); 
         return $this->successJson('Team Deleted Successfully',200);
    }
    }


 public function update(UpdateTeam $request, $id){
       

        if(!auth()->user()->cans('edit_team')){
        return $this->errorJson('Not authenticated to perform this request',403);
       }else{

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if($request->password != ''){
            $user->password = Hash::make($request->password);
        }

       
        $user->mobile       = $request->mobile;
       

        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'profile');
            $user->image = Files::uploadLocalOrS3($request->image,'profile');
        }else{
           
          // $user->image = null;
       }
        $user->save();

        //attach role
        RoleUser::where('user_id', $id)->delete();
        $user->roles()->attach($request->role_id);

        if($user){
              return $this->successJson('Team Updated Successfully',200,$user);
        }
    }
   }


 public function changepassword(Request $request, $id){
        $user = User::find($id);
        $user->name = $request->name;
         
        if($request->password != ''){
            $user->password = Hash::make($request->password);
        }

        if($request->mobile!= '' || $request->mobile==null){
            $user->mobile=$request->mobile;
        }
        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'profile');
            $user->image = Files::uploadLocalOrS3($request->image,'profile');
        }else{
           
          // $user->image = null;
       }
        $user->save();
        if($user){
              return $this->successJson('Profile Updated Successfully',200,$user);
        }
    }


}
