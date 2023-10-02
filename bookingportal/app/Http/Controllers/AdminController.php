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

    /**
    * Create a new agent based on the provided request data.
    *
    * @param AdminCreateAgentRequest $request The request containing agent creation data.
    *
    * @return Agent The newly created agent.
    */
    public function createAgent(AdminCreateAgentRequest $request)
    {
        $request->validated();

        try {
            return new AgentResource(
                $this->IbackOfficeService->createAgent(
                    $request->get('firstName'),
                    $request->get('lastName'),
                    $request->get('email'),
                    $request->get('status'),
                    (int) $request->get('isAdmin'),
                    (int) $request->get('isAgent')
                )
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    /**
    * Edit an existing agent using the provided request data.
    *
    * @param AdminCreateAgentRequest $request The request containing agent edit data.
    *
    * @return Agent The edited agent.
    */
    public function editAgent(AdminCreateAgentRequest $request)
    {
        $request->validated();

        try {
            return new AgentResource(
                $this->IbackOfficeService->editAgent(
                    intval($request->get('id')),
                    $request->get('firstName'),
                    $request->get('lastName'),
                    $request->get('email'),
                    $request->get('status'),
                    intval($request->get('isAdmin')),
                    intval($request->get('isAgent'))
                )
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    /**
    * Retrieve detailed information about a specific agent based on their unique ID.
    *
    * @param int $agentId The unique ID of the agent.
    *
    * @return AgentDetails|null The detailed information about the agent, or null if not found.
    */
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

    /**
    * Deactivate or reactivate the account of a specific agent based on their unique ID.
    *
    * @param int $agentId The unique ID of the agent.
    *
    * @return JsonResponse The JSON response indicating success or failure of the operation.
    * @throws \Exception If an error occurs during the operation.
    */
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

    /**
    * Retrieve a list of travel agents.
    *
    * @return JsonResponse The JSON response containing the list of travel agents or an error message.
    */
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
        $request->validated();

        try {
            return new FaqResource(
                $this->IbackOfficeService->createOrUpdateFaq(
                    $request->get('question'),
                    $request->get('answer'),
                )
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }


    public function editFaq(CreateOrUpdateFaqRequest $request)
    {
        $request->validated();

        try {
            return new FaqResource(
                $this->IbackOfficeService->createOrUpdateFaq(
                    $request->get('question'),
                    $request->get('answer'),
                    // Changed 'faqId' to 'id'
                    intval($request->get('id'))
                )
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_ALREADY_REPORTED);
        }
    }

    public function getSpecificFaq(int $faqId)
    {

        $specificFaq = new FaqResource($this->IbackOfficeService->getFaqById($faqId));

        if (!$specificFaq) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($specificFaq, Response::HTTP_OK, "FAQ");
    }

    public function removeFaq(int $faqId)
    {
        $specificFaq = $this->IbackOfficeService->getFaqById($faqId);

        if ($specificFaq === null) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        if ($specificFaq->delete()) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_DELETED_SUCCESSFULLY, Response::HTTP_OK);
        }
    }

    public function resetAgentPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $userId = $request->input('id');
        $password = "systemAgentUser";

        $user = UserAccount::ById($userId)->first();
        $user->setPassword(Hash::make($password));
        $user->getFirstTimeLoggedIn(0);
        $user->save();
    }
}