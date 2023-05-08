<?php

declare(strict_types=1);
namespace App\Http\Controllers;

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
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
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
            return response()->json($newAgent, Response::HTTP_OK);
        }
        
        return response()->json("User already registered", Response::HTTP_IM_USED);

    }

    public function getSpecificAgentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $userId = intval($request->input('userId'));

        $agent = BackOfficeService::getSpecificAgentDetails($userId);

        if($agent){
            return response()->json($agent, Response::HTTP_OK);
        }

        return response()->json("Agent could not be found", Response::HTTP_NOT_FOUND);
    }

    public function setAgentAccountToDeactive(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->input('userId');
        
        $user = UserAccount::ById($userId)->first();

        $user->setStatus(0);
        $user->getDeactivatedAt(time());
        return $user->save();
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
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $id = $request->input('id');
    
        return response()->json(['message' => 'User enquiry could not be marked'], Response::HTTP_BAD_REQUEST);    
    }

    

    public function createNewFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question'  => 'required|string',
            'answer'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $question = $request->input('question');
        $answer = $request->input('answer');

        $result = BackOfficeService::createOrUpdateFaq($question, $answer);

        if ($result) {
            return response()->json('New FAQ successfully created', Response::HTTP_OK);
        }

        return response()->json('Failed to create new FAQ', Response::HTTP_INTERNAL_SERVER_ERROR);
    }



    public function getSpecificFaq(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        
        $id = intval($request->input('id'));

        $specificFaq = Faq::byId($id)->first();

        if(!$specificFaq){
            response()->json("Faq not found", Response::HTTP_NOT_FOUND);
        }
        
        response()->json($specificFaq, Response::HTTP_OK);
    }

    public function removeFaq(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }

        $id = intval($request->input('id'));

        $specificFaq = Faq::byId($id)->first();
        if(!$specificFaq){
            return response()->json('Faq to delete not found', Response::HTTP_NOT_FOUND);    
        }
        $specificFaq->delete();

        if($specificFaq){
            response()->json('Faq successfully deleted', Response::HTTP_OK);
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
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
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
            return response()->json('New user role successfully created', Response::HTTP_OK);
        }

        return response()->json('Failed to create new user role', Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    public function removeUserRole(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $id = intval($request->input('id'));
    
        $userRole = UserRole::byId($id)->first();
        if (!$userRole) {
            return response()->json('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        if ($userRole->delete()) {
            return response()->json('User enquiry deleted successfully', Response::HTTP_OK);
        }
    
        return response()->json('UserEnquiry could not be deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function showSpecificOrAllUserRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $id = intval($request->input('id'));

        if ($id) {
            $userRole = UserRole::byId($id);

            if (!$userRole) {
                return response()->json(['error' => 'User role not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($userRole, 200);
        }

        $userRoles = UserRole::all();

        if ($userRoles->isEmpty()) {
            return response()->json(['error' => 'No user roles found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($userRoles, 200);
    }

    public function resetAgentPassword(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->input('userId');
        $password = "systemAgentUser";

        $user = UserAccount::ById($userId)->first();
        $user->setPassword(Hash::make($password));
        $user->getFirstTimeLoggedIn(0);
        $user->save();
    }
}

