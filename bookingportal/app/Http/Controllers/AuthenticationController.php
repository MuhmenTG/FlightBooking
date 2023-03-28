<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
     
    public function loginUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }
        
        $email = $request->input('email');
        $password = $request->input('password');
        $user = UserAccount::ByEmail($email)->first();

        if($user && Hash::check($password, $user->getPassword())){
            
            $token = $user->createToken('my-token')->plainTextToken;
            echo $token;exit;
            return response([
                'token' =>  $token,
                'user'  =>  $user,
                'message' => 'Login Success',
                'status'  => true
            ], 200);
        }
        
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status'=>'failed'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'Succesfully logout'
        ], 200);
  
    }
}
