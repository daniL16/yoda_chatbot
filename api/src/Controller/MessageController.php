<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ChatBotApiService;
use App\Service\InbentaSwapiApiService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class MessageController
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
        $errors = [];
        if(!$this->validateRequest($request, $errors)){
            return new JsonResponse(['errors' => $errors], 400);
        }

        $data = json_decode((string)$request->getContent(), true);

        try {
            $failedAttempts = $data['notFoundAttempts'] ?? 0;
            return new JsonResponse($this->getResponseMessage($data['message'], $data['sessionToken'] ?? '', (int)$failedAttempts), 200);
        } catch (GuzzleException $exception) {
            return new JsonResponse(['errors' => $exception->getMessage()],500);
        }
    }

    /**
     * @param Request $request
     * @param array<String> $errors
     * @return bool
     */
    private function validateRequest(Request $request, array &$errors) : bool{
         $data = json_decode((string) $request->getContent(), true);
         $valid = true;
        if (JSON_ERROR_NONE !== json_last_error()) {
            $errors[] =  sprintf('Invalid JSON format: %s', json_last_error_msg());
            $valid = false;
        }
        if (empty($data['message'])) {
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
        if(str_contains(strtolower($message), 'force')){
            $responseMessage = $this->formatMessage($this->swapiApiClient->getFilms(), 'films');
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

        return (string) json_encode($response);
    }

    /**
     * @return string
     */
    private function postProcessNotFound(): string{
        return $this->formatMessage($this->swapiApiClient->getPeople(), 'characters');
    }

    /**
     * @param array<String> $values
     * @param string $type
     * @return string
     */
    private function formatMessage(array $values, string $type): string{
        $list = '<ul>';
        foreach ($values as $value){
            $list.= "<li> $value </li>";
        }
        $list .= '</ul>';
        return "I haven't found any results, but here is a list of some Star Wars $type : $list";
    }
}
