<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function index()
    {
        $handler = new ChatBotApiService();
        $response = json_decode($handler->sendMessage('hello!'));
        $response = $handler->sendMessage('what if fear?', $response->session_token);

        return new Response($response);
    }
}
