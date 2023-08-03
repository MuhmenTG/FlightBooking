<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\AdminCreateAgentRequest;
use App\Http\Requests\CreateOrUpdateFaqRequest;
use App\Http\Resources\AgentResource;
use App\Http\Resources\FaqResource;
use App\Models\UserAccount;
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

    public function createAgent(AdminCreateAgentRequest $request)
    {
        try {
            return new AgentResource($this->IbackOfficeService->createAgent(
                $request->get('firstName'),
                $request->get('lastName'),
                $request->get('email'),
                $request->get('status'),
                (int)$request->get('isAdmin'), 
                (int)$request->get('isAgent')
            ));
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    public function editAgent(AdminCreateAgentRequest $request)
    {
        try {
            return new AgentResource($this->IbackOfficeService->editAgent(
                intval($request->input('agentId')),
                $request->input('firstName'),
                $request->input('lastName'),
                $request->input('email'),
                $request->input('status'),
                intval($request->input('isAdmin')),
                intval($request->input('isAgent'))
            ));
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    public function getSpecificAgentDetails(int $agentId)
    {
        try {
            $userAccount = $this->IbackOfficeService->getAgentById($agentId);

            return $userAccount
                ? new AgentResource($userAccount)
                : ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND, "Agent");
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_IM_USED);
        }
    }

    public function deOrReactivateAgentAccount(int $agentId)
    {
        try {
            $deactivatedAgent = $this->IbackOfficeService->removeAgentAccount($agentId);

            return $deactivatedAgent
                ? ResponseHelper::jsonResponseMessage(new AgentResource($deactivatedAgent), Response::HTTP_OK, "Agent")
                : ResponseHelper::jsonResponseMessage(ResponseHelper::AGENT_NOT_FOUND, Response::HTTP_NOT_FOUND, "Agent");
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function showListOfTravelAgents()
    {
        $agents = $this->IbackOfficeService->getAllAgents();
        $responseMessage = $agents
            ? ['formatedAgents' => AgentResource::collection($agents)]
            : ResponseHelper::AGENT_NOT_FOUND;
    
        return ResponseHelper::jsonResponseMessage($responseMessage, Response::HTTP_OK);
    } 

    public function createFaq(CreateOrUpdateFaqRequest $request)
    {
        try {
            return new FaqResource($this->IbackOfficeService->createOrUpdateFaq(
                $request->input('question'),
                $request->input('answer'),
            ));

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    
    public function editFaq(CreateOrUpdateFaqRequest $request)
    {
        try {
            return new FaqResource($this->IbackOfficeService->createOrUpdateFaq(
                $request->input('question'),
                $request->input('answer'),
                intval($request->input('faqId'))
            ));

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }
    
    public function getSpecificFaq(int $faqId){
        
        $specificFaq = new FaqResource($this->IbackOfficeService->getFaqById($faqId));
        
        if(!$specificFaq){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
        
        return ResponseHelper::jsonResponseMessage($specificFaq, Response::HTTP_OK, "FAQ");
    }

    public function removeFaq(int $faqId){
        $specificFaq = $this->IbackOfficeService->getFaqById($faqId);

        if ($specificFaq === null) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);    
        }
        
        if ($specificFaq->delete()) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_DELETED_SUCCESSFULLY, Response::HTTP_OK);
        }
    }

    public function resetAgentPassword(Request $request){
        
        $validator = Validator::make($request->all(), [
            'agentId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $userId = $request->input('agentId');
        $password = "systemAgentUser";

        $user = UserAccount::ById($userId)->first();
        $user->setPassword(Hash::make($password));
        $user->getFirstTimeLoggedIn(0);
        $user->save();
    }
}

