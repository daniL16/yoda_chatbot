<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController
{
    public function send(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return new JsonResponse(
                ['error' => sprintf('Invalid JSON format: %s', json_last_error_msg())], 400
            );
        }

        if (!isset($data['message'])) {
            return new JsonResponse(['error' => 'message is required'], 400);
        }
        $message = $data['message'];
        $sessionToken = isset($data['sessionToken']) ? $data['sessionToken'] : null;
        $handler = new ChatBotApiService();
        $response = $handler->sendMessage($message, $sessionToken);

        return new Response(json_encode(['sessionToken' => $response['session_token'], 'answer' => $response['response_message']]));
    }
}
