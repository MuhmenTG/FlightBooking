<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

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
        
        
        if(!$user || !Hash::check($password, $user->getPassword())){
            return response([
                'msg' => 'Credentials incorrect'
            ], Response::HTTP_UNAUTHORIZED);
        }
       

        $token = $user->createToken('apiToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, Response::HTTP_ACCEPTED);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'Succesfully logout'
        ], 200);
  
    }
}
