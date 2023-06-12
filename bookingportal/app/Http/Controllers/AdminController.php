<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\AgentResource;
use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserRole;
use App\Services\BackOffice\IBackOfficeService;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    //

    protected $IbackOfficeService;

    public function __construct(IBackOfficeService $IbackOfficeService)
    {
        $this->IbackOfficeService = $IbackOfficeService;
    }

    public function createAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName'     => 'required|string',
            'lastName'      => 'required|string',
            'email'         => 'required|email',
            'status'        => 'required|string',
            'isAdmin'       => 'required|boolean',
            'isAgent'       => 'required|boolean',
        ]);


        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        try {
            $userAccount = $this->IbackOfficeService->createAgent(
                $request->input('firstName'),
                $request->input('lastName'),
                $request->input('email'),
                $request->input('status'),
                intval($request->input('isAdmin')),
                intval($request->input('isAgent'))
            );

            $agentResource = new AgentResource($userAccount);
            return $agentResource;
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_IM_USED);        
        }
    }

    public function editAgent(int $agentId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName'     => 'nullable|string',
            'lastName'      => 'nullable|string',
            'password'      => 'nullable|string',
            'email'         => 'nullable|email',
            'status'        => 'nullable|string',
            'isAdmin'       => 'nullable|boolean',
            'isAgent'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        echo     $request->input('firstName');exit;

        try {
            $userAccount = $this->IbackOfficeService->editAgent(
                $agentId,
                $request->input('firstName'),
                $request->input('password'),
                $request->input('lastName'),
                $request->input('email'),
                $request->input('status'),
                intval($request->input('isAdmin')),
                intval($request->input('isAgent')),
            );

            $agentResource = new AgentResource($userAccount);
            return $agentResource;
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(),Response::HTTP_IM_USED);        
        }
    }

    public function getpecificAgentDetails(int $agentId)
    {
        try {
            $userAccount = $this->backOfficeService->getAgentById($agentId);

            if (!$userAccount) {
                return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
            }  
            
            $agentResource = new AgentResource($userAccount);
            return $agentResource;
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(),Response::HTTP_IM_USED);        
        }
    }

    public function setAgentAccountToDeactive(int $agentId){
        try {
            $userAccount = $this->backOfficeService->getAgentById($agentId);

            if (!$userAccount) {
                return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
            }  
            
            $deactivatedAgent = $this->backOfficeService->removeAgentAccount($agentId);

            $agentResource = new AgentResource($deactivatedAgent);
            return $agentResource;
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(),Response::HTTP_IM_USED);        
        }
    }

    public function showListOfTravlAgent()
    {
        $agents = $this->backOfficeService->getAllAgents();
    
        if ($agents === false) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return $agents;
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

    public function editFaq(int $faqId, Request $request)
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


    public function getSpecificFaq(int $faqId){

        $specificFaq = Faq::byId($faqId)->first();

        if(!$specificFaq){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
        
        return ResponseHelper::jsonResponseMessage($specificFaq, Response::HTTP_OK);
    }

    public function removeFaq(int $faqId){
        $specificFaq = Faq::byId($faqId)->first();
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

