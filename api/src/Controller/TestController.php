<?php

namespace App\Controller;

use App\Service\ChatBotApiService;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function index()
    {
        $handler = new ChatBotApiService();
        $response = json_decode($handler->sendMessage('test'));
        var_dump($response);
        $response = json_decode($handler->sendMessage('Que dise Juan', $response->sessionToken));
        var_dump($response);

        return new Response($response);
    }
}
