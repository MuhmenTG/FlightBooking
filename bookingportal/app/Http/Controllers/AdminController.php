<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserRole;
use App\Services\BackOfficeService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    //

    public function createAgent(Request $request){
   
        $validator = Validator::make($request->all(), [
            'firstName'               => 'required|string',
            'lastName'                => 'required|string',
            'email'                   => 'required|string',
            'status'                  => 'required|int',
            'isAdmin'                 => 'nullable|int',
            'isAgent'                 => 'nullable|int'

        ]);


        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }
    
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $status = $request->input('status');
        $isAdmin = $request->input('isAdmin');
        $isAgent = $request->input('isAgent');

        $newAgent = BackOfficeService::createOrEditAgent(
            $firstName,
            $lastName,
            $email,
            $status,
            intval($isAdmin),
            intval($isAgent)
        );

        if($newAgent){
            return ResponseHelper::jsonResponseMessage($newAgent, Response::HTTP_OK);
        }
        
        return ResponseHelper::jsonResponseMessage("User already registered", Response::HTTP_IM_USED);

    }

    public function getSpecificAgentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        
        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $userId = intval($request->input('userId'));

        $agent = BackOfficeService::getSpecificAgentDetails($userId);

        if($agent){
            return ResponseHelper::jsonResponseMessage($agent, Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }

    public function setAgentAccountToDeactive(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $userId = $request->input('userId');
        
        $deactivatedEmployee = BackOfficeService::deactivateEmployee($userId);
        if(!$deactivatedEmployee){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($deactivatedEmployee, Response::HTTP_OK);
    }

    public function showListOfAgent(){

        $agents = Useraccount::all();
        return [
            "agents" => $agents
        ];
    }

    public function setUserEnquiryStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }
    
        $id = $request->input('id');
    
        return ResponseHelper::jsonResponseMessage(['message' => 'User enquiry could not be marked'], Response::HTTP_BAD_REQUEST);    
    }

    public function createNewFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question'  => 'required|string',
            'answer'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $question = $request->input('question');
        $answer = $request->input('answer');

        $result = BackOfficeService::createOrUpdateFaq($question, $answer);
        
        if ($result) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_CREATED_SUCCESS, Response::HTTP_OK);
        }
        
        return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_CREATION_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function getSpecificFaq(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }
        
        $id = intval($request->input('id'));

        $specificFaq = Faq::byId($id)->first();

        if(!$specificFaq){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
        
        return ResponseHelper::jsonResponseMessage($specificFaq, Response::HTTP_OK);
    }

    public function removeFaq(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $id = intval($request->input('id'));

        $specificFaq = Faq::byId($id)->first();
        if(!$specificFaq){
            return ResponseHelper::jsonResponseMessage('Faq to delete not found', Response::HTTP_NOT_FOUND);    
        }
        $specificFaq->delete();

        if($specificFaq){
            ResponseHelper::jsonResponseMessage('Faq successfully deleted', Response::HTTP_OK);
        }
    }

    public function createOrEditUserRole(Request $request){
        $validator = Validator::make($request->all(), [
            'id'                => 'nullable|integer',
            'roleName'          => 'required|string',
            'roleCode'          => 'required|string',
            'roleDescription'   => 'required|string',

        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $id = $request->input('id');
        $roleName = $request->input('roleName');
        $roleCode = $request->input('roleCode');
        $roleDescription = $request->input('roleDescription');

        $userRole = $id ? UserRole::byId($id) : new UserRole();
        $userRole->setRoleName($roleName);
        $userRole->setRoleCode($roleCode);
        $userRole->setRoleDescription($roleDescription);
       
        if ($userRole->save()) {
            return ResponseHelper::jsonResponseMessage('New user role successfully created', Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage('Failed to create new user role', Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    public function removeUserRole(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }
    
        $id = intval($request->input('id'));
    
        $userRole = UserRole::byId($id)->first();
        if (!$userRole) {
            return ResponseHelper::jsonResponseMessage('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        if ($userRole->delete()) {
            return ResponseHelper::jsonResponseMessage('User enquiry deleted successfully', Response::HTTP_OK);
        }
    
        return ResponseHelper::jsonResponseMessage('UserEnquiry could not be deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function showSpecificOrAllUserRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $id = intval($request->input('id'));

        if ($id) {
            $userRole = UserRole::byId($id);

            if (!$userRole) {
                return ResponseHelper::jsonResponseMessage(['error' => 'User role not found'], Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::jsonResponseMessage($userRole, 200);
        }

        $userRoles = UserRole::all();

        if ($userRoles->isEmpty()) {
            return ResponseHelper::jsonResponseMessage(['error' => 'No user roles found'], Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($userRoles, 200);
    }

    public function resetAgentPassword(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $userId = $request->input('userId');
        $password = "systemAgentUser";

        $user = UserAccount::ById($userId)->first();
        $user->setPassword(Hash::make($password));
        $user->getFirstTimeLoggedIn(0);
        $user->save();
    }
}

