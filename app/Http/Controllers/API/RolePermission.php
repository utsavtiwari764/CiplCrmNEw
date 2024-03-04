<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Module;
use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRole;
use App\Http\Requests\StoreUserRole;
class RolePermission extends Controller
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
    public function get(){
        $roles = Role::where('id', '>', 1)->get();
        $totalPermissions = Permission::count();
        $modules = Module::with(['permissions'])->get();
        $data=['roles'=>$roles,'totalPermissions'=>$totalPermissions,'modules'=>$modules];
        if(empty($data)){
            return $this->errorJson('data not founds',404);
        }else{
            
            return $this->successJson('data found',200,$data);
        }
    }  
 public function permisioncheck(Request $request){

          $roles = DB::table('permission_role')->where(['permission_id'=>$request->permission_id,'role_id'=>$request->role_id])->count();
      
       
        if(empty($roles)){
            return $this->errorJson('data not founds',404,false);
        }else{
            
            return $this->successJson('data found',200,true);
        }
            
    } 
    public function assignAllPermission(Request $request)
    {
        $roleId = $request->roleId;
        $permissions = Permission::all();

        $role = Role::findOrFail($roleId);
        $role->perms()->sync([]);
        $role->attachPermissions($permissions);
        if($role){
            return $this->successJson('add permission',200,$role);
        }else{
            
            return $this->errorJson('Not data found',404);
        }
    }
    public function removeAllPermission(Request $request)
    {
        $roleId = $request->roleId;

        $role = Role::findOrFail($roleId);
        $role->perms()->sync([]);

        if($role){
            return $this->successJson('removed permission',200,$role);
        }else{
            
            return $this->errorJson('Not data found',404);
        }
    }


    public function store(Request $request)
    {
        $roleId = $request->roleId;
        $permissionId = $request->permissionId;
         //return $this->successJson('assignPermission',200,$request->assignPermission);

        if ($request->assignPermission == 'yes') {
            $permissionRole=PermissionRole::firstOrCreate([
                'permission_id' => $permissionId,
                'role_id' => $roleId
            ]);
            return $this->successJson('add permission',200,$permissionRole);
        } else {
            $permissionRole=PermissionRole::where('role_id', $roleId)->where('permission_id', $permissionId)->delete();
            return $this->successJson('removed permission',200,);
        }
       
         
    }


    public function storeRole(StoreRole $request)
    {
        $roleUser = new Role();
        $roleUser->name = $request->name;
        $roleUser->display_name = ucwords($request->name);
        $roleUser->save();
        if($roleUser){
            return $this->successJson('create Role Successfully',200,$roleUser);
        }else{
            return $this->errorJson('something else wrong',500);
        }
    }
 public function update(StoreRole $request, $id)
    {
        $roleUser = Role::findOrFail($id);
        $roleUser->name = $request->name;
        $roleUser->display_name = ucwords($request->name);
        $roleUser->save();
        if($roleUser){
            return $this->successJson('update Role Successfully',200,$roleUser);
        }else{
            return $this->errorJson('something else wrong',500);
        }
    }

}
