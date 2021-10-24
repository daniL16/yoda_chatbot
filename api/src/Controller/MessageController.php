<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use App\Service\InbentaSwapiApiService;
use GuzzleHttp\Exception\GuzzleException;
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
        $errors = [];
        if(!$this->validateRequest($request, $errors)){
            return new JsonResponse(['errors' => $errors], 400);
        }

        try {
            return new JsonResponse($this->getResponseMessage($data['message'], $data['sessionToken'] ?? '', (int) $data['notFountAttempts']), 200);
        } catch (GuzzleException $exception) {
            return new JsonResponse(['errors' => $exception->getMessage()],500);
        }
    }

    /**
     * @param Request $request
     * @param array $errors
     * @return bool
     */
    private function validateRequest(Request $request, array &$errors) : bool{
         $data = json_decode($request->getContent(), true);
         $valid = true;
        if (JSON_ERROR_NONE !== json_last_error()) {
            $errors[] =  sprintf('Invalid JSON format: %s', json_last_error_msg());
            $valid = false;
        }
        if (!isset($data['message'])) {
            $errors[] = 'Message is required';
            $valid = false;
        }
        return $valid;
    }

    /**
     * @param string $message
     * @param string $conversationToken
     * @param int $previousNotFound
     * @return string
     * @throws GuzzleException
     */
    private function getResponseMessage(string $message, string $conversationToken, int $previousNotFound = 0): string{
        if(str_contains($message, 'force')){
            $films = json_encode($this->swapiApiClient->getFilms());
            $films = str_replace('"','', str_replace('[','', $films));
            $responseMessage = 'I haven\'t found any results, but here is a list of some Star Wars films: '. $films;
            $response = ['session_token' => $conversationToken, 'response_message' => $responseMessage];
        }
        else{
            $response = $this->chatBotApiClient->sendMessage($message, $conversationToken);
            if(str_contains($response['response_message'],'couldn\'t find')
                || str_contains($response['response_message'], 'Please search again')
            ){
                $response['not_found_message'] = true;
                if($previousNotFound >= self::NOT_FOUND_ATTEMPTS){
                    $response['response_message'] = $this->postProcessNotFound();
                }
            }
        }

        return json_encode($response);
    }

    /**
     * @return string
     */
    private function postProcessNotFound(): string{
        $characters = json_encode($this->swapiApiClient->getPeople());
        $characters = str_replace('"','', str_replace('[','', $characters));
        return 'I haven\'t found any results, but here is a list of some Star Wars characters: '. $characters;
    }
}
