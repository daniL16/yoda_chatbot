<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageController
{
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return new JsonResponse(['error' => sprintf('Invalid JSON format: %s', json_last_error_msg())], 400);
        }
        if (!isset($data['message'])) {
            return new JsonResponse(['error' => 'message is required'], 400);
        }
        $handler = new ChatBotApiService();

        return new JsonResponse($handler->sendMessage($data['message'], $data['sessionToken'] ?? null), 200);
    }
}
