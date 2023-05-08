<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $response = AuthenticationService::authenticate($email, $password);

        if (!$response) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::CREDENTIALS_WRONG, Response::HTTP_FORBIDDEN);
        }

        return ResponseHelper::jsonResponseMessage($response, Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ResponseHelper::jsonResponseMessage(ResponseHelper::LOGOUT_SUCCESS, Response::HTTP_OK);
    }
}
