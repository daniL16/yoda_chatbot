<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    private static $client = null;
    private const NOT_FOUND_ATTEMPTS = 2;

    public function testSendMessage()
    {
        $client = static::createClient();
        $client->request('POST', '/send_message', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['message' => 'hello', 'sessionToken' => '']));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSendEmptyMessage()
    {
        $client = static::createClient();
        $client->request('POST', '/send_message', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['message' => '', 'sessionToken' => '']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testForceMessage()
    {
        $films = $this->sendMessage('may the force be with you');
        $this->assertStringContainsString('Star Wars films', $films);
    }

    /*
     * When the value of NOT_FOUND_ATTEMPTS is exceeded, a list of characters must be returned.
     */
    public function testNotFoundMessages()
    {
        $response = $this->sendMessage('testing');
        $this->assertStringNotContainsString('characters', $response);
        // The following run should return the list of characters
        $characters = $this->sendMessage('testing', self::NOT_FOUND_ATTEMPTS + 1);
        $this->assertStringContainsString('characters', $characters);
    }

    private function sendMessage(string $message, int $attempts = 0): string
    {
        self::ensureKernelShutdown();
        if (null === self::$client) {
            self::$client = static::createClient();
        }

        self::$client->request('POST',
            '/send_message',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['message' => $message, 'sessionToken' => '', 'notFoundAttempts' => $attempts]));
        $response = json_decode(self::$client->getResponse()->getContent());
        // Our response is another JSON
        return json_decode($response)->response_message;
    }
}
