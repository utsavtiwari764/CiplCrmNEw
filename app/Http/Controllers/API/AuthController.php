<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
Log::info('Login Data:', $loginData);

class AuthController extends Controller
{
  

    public function login(Request $request)
    {
        // Validate request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]); 
    
        // Retrieve email and password from the request
        $email = $request->input('email');
        $password = $request->input('password');
    
        // Attempt to authenticate the user
        if (!auth()->attempt(['email' => $email, 'password' => $password])) {
            // Return error response if authentication fails
            return response()->json([
                'code' => 401,
                'message' => 'Invalid email or password.',
                'status' => 0,
            ], 401);
        }
    
        


        // If authentication succeeds, generate access token
        $user = $request->user();    
        $accessToken = $user->createToken('CIPL')->accessToken;
    
        // Retrieve user data with roles and permissions
        $userData = User::with('roles.permissions.permission')->find(auth()->user()->id);
        $userPermissions = $userData->roles->flatMap->permissions->pluck('permission.name')->toArray();
        $userData['userPermissions'] = $userPermissions;
    
        // Return success response with access token and user data
        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'code' => 200,
            'data' => $userData,
            'message' => 'Login successful!!',
            'status' => 1,
        ]);
    }
           
}
    ?>
    

