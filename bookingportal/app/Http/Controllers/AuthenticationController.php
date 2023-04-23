<?php

namespace App\Http\Controllers;

use App\Services\AuthenticationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
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

        $response = AuthenticationService::authenticate($email, $password);

        if (!$response) {
            return response([
                'msg' => 'Credentials incorrect'
            ], Response::HTTP_UNAUTHORIZED);
        }

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
