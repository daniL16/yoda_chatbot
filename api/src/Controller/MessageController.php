<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use Symfony\Component\HttpFoundation\Response;

class MessageController
{
    public function send(string $message = 'Hi!', string $sessionToken = null)
    {
        $handler = new ChatBotApiService();
        $response = $handler->sendMessage($message, $sessionToken);

        return new Response(json_encode(['sessionToken' => $response['session_token'], 'answer' => $response['response_message']]));
    }
}
