<?php

namespace App\Tests\Service;

use App\Service\ChatBotApiService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatServiceTest extends WebTestCase
{

    private ChatBotApiService $apiClient;

    public function setUp(): void
    {
        $this->apiClient = new ChatBotApiService();
    }

    public function testCreateConversation(){
        $botResponse = $this->apiClient->sendMessage('hello');
        $this->assertIsArray($botResponse);
        $this->assertIsString($botResponse['session_token']);
        $this->assertNotEquals('', $botResponse['session_token']);
        $this->assertIsString($botResponse['response_message']);
        $this->assertNotEquals('', $botResponse['response_message']);
    }
}