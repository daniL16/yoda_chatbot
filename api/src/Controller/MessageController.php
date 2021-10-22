<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use App\Service\InbentaSwapiApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageController
{

    public const NOT_FOUND_ATTEMPTS = 2;

    public function __construct(
        private ChatBotApiService $chatBotApiClient,
        private InbentaSwapiApiService $swapiApiClient
    )
    {
    }

    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return new JsonResponse(['error' => sprintf('Invalid JSON format: %s', json_last_error_msg())], 400);
        }
        if (!isset($data['message'])) {
            return new JsonResponse(['error' => 'message is required'], 400);
        }

        return new JsonResponse($this->getResponseMessage($data['message'], $data['sessionToken'] ?? ''), 200);
    }

    private function getResponseMessage(string $message, string $conversationToken): string{
        if(str_contains($message, 'force')){
            $responseMessage = json_encode($this->swapiApiClient->getFilms());
            $response = ['session_token' => $conversationToken, 'response_message' => $responseMessage];
        }
        else{
            $response = $this->chatBotApiClient->sendMessage($message, $conversationToken);
            if(str_contains($response['response_message'],'couldn\'t find')
                || str_contains($response['response_message'], 'Please search again')
            ){
                $response['response_message'] = $this->postProcessNotFound();
            }
        }

        return json_encode($response);
    }

    /**
     * @return string
     */
    private function postProcessNotFound(): string{
        return json_encode($this->swapiApiClient->getPeople());
    }
}
