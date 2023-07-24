<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\Authentication\IAuthenticationService;
use App\Services\AuthenticationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthenticationController
 *
 * This controller handles user authentication-related operations such as login and logout.
 *
 * @package App\Http\Controllers
 */
class AuthenticationController extends Controller
{
    protected $IAuthenticationService;

    /**
    * Create a new AuthenticationController instance.
    *
    * @param IAuthenticationService $IAuthenticationService The authentication service.
    */
    public function __construct(IAuthenticationService $IAuthenticationService)
    {
        $this->IAuthenticationService = $IAuthenticationService;
    }

    /**
    * Handle user login request.
    *
    * @param Request $request The HTTP request object.
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
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

    /**
    * Handle user logout request.
    *
    * @param Request $request The HTTP request object.
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function logout(Request $request)
    {    
        if ($request->user()) {
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
                return ResponseHelper::jsonResponseMessage(ResponseHelper::LOGOUT_SUCCESS, Response::HTTP_OK);
            }
        }    
        return ResponseHelper::jsonResponseMessage(ResponseHelper::LOGOUT_SUCCESS, Response::HTTP_NOT_FOUND);
        
    }
}
