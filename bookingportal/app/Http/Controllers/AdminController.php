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

    protected$IbackOfficeService;

    public function __construct(IBackOfficeService $IbackOfficeService)
    {
        $this->IbackOfficeService = $IbackOfficeService;
    }

    public function saveAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName'     => 'required|string',
            'lastName'      => 'required|string',
            'email'         => 'required|email',
            'status'        => 'required|string',
            'isAdmin'       => 'required|boolean',
            'isAgent'       => 'required|boolean',
            'agentId'       => 'sometimes|numeric', 
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        try {
            $agentId = $request->input('agentId');
            $userAccount = null;

            if ($agentId) {              
                $userAccount = $this->IbackOfficeService->editAgent(
                    intval($agentId),
                    $request->input('firstName'),
                    $request->input('lastName'),
                    $request->input('email'),
                    $request->input('status'),
                    intval($request->input('isAdmin')),
                    intval($request->input('isAgent'))
                );
            } else {
                $userAccount = $this->IbackOfficeService->createAgent(
                    $request->input('firstName'),
                    $request->input('lastName'),
                    $request->input('email'),
                    $request->input('status'),
                    intval($request->input('isAdmin')),
                    intval($request->input('isAgent'))
                );
            }

            $agentResource = new AgentResource($userAccount);
            return ResponseHelper::jsonResponseMessage($agentResource, Response::HTTP_OK, "Agent");
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    public function getSpecificAgentDetails(int $agentId)
    {
        try {
            $userAccount = $this->IbackOfficeService->getAgentById($agentId);

            if (!$userAccount) {
                return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
            }  
            
            $agentResource = new AgentResource($userAccount);
            return ResponseHelper::jsonResponseMessage($agentResource, Response::HTTP_OK, "Agent");
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(),Response::HTTP_IM_USED);        
        }
    }

    public function deOrReactivateAgentAccount(int $agentId){
        try {
            $userAccount = $this->IbackOfficeService->getAgentById($agentId);

            if (!$userAccount) {
                return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
            }  
            
            $deactivatedAgent = $this->IbackOfficeService->removeAgentAccount($agentId);

            $formatedAgent = new AgentResource($deactivatedAgent);
            return ResponseHelper::jsonResponseMessage($formatedAgent, Response::HTTP_OK, "Agent");
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);        
        }
    }

    public function showListOfTravelAgents()
    {
        $agents = $this->IbackOfficeService->getAllAgents();    

        if (!$agents) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        $formatedAgents = AgentResource::collection($agents);

        return response()->json(['formatedAgents' => $formatedAgents], 200);
    }

    public function saveFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string',
            'answer' => 'required|string',
            'faqId' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        try {
            $faqId = $request->input('faqId');
            $faq = null;

            if ($faqId) {
                $faq = $this->IbackOfficeService->createOrUpdateFaq(
                    $request->input('question'),
                    $request->input('answer'),
                    intval($faqId)
                );
            } else {
                $faq = $this->IbackOfficeService->createOrUpdateFaq(
                    $request->input('question'),
                    $request->input('answer')
                );
            }

            return ResponseHelper::jsonResponseMessage($faq, Response::HTTP_OK, "FAQ");
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    public function getSpecificFaq(int $faqId){
        
        $specificFaq = $this->IbackOfficeService->getFaqById($faqId);
        
        if(!$specificFaq){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
        
        return ResponseHelper::jsonResponseMessage($specificFaq, Response::HTTP_OK);
    }

    public function removeFaq(int $faqId){
        $specificFaq = Faq::ById($faqId)->first();

        if($specificFaq === null){
            return ResponseHelper::jsonResponseMessage('Faq to delete not found', Response::HTTP_NOT_FOUND);    
        }
        if($specificFaq->delete()){
            return ResponseHelper::jsonResponseMessage('Faq successfully deleted', Response::HTTP_OK);
        }
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

