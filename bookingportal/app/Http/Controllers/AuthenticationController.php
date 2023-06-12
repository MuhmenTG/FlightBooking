<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\Authentication\IAuthenticationService;
use App\Services\AuthenticationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    protected $IAuthenticationService;

    public function __construct(IAuthenticationService $IAuthenticationService)
    {
        $this->IAuthenticationService = $IAuthenticationService;
    }

    public function login(Request $request)
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

        $response = $this->IAuthenticationService->authenticate($email, $password);

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
