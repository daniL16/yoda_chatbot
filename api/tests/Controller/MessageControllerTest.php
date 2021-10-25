<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{

    public function testSendMessage(){
        $client = static::createClient();
        $client->request('POST','/send_message', [],[],['CONTENT_TYPE' => 'application/json'],json_encode(["message" => 'hello', "sessionToken" => '']));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

    public function testSendEmptyMessage(){
        $client = static::createClient();
        $client->request('POST','/send_message', [],[],['CONTENT_TYPE' => 'application/json'],json_encode(["message" => '', "sessionToken" => '']));
        $this->assertEquals(400,$client->getResponse()->getStatusCode());
    }

    public function testForceMessage(){
        $client = static::createClient();
        $client->request('POST','/send_message', [],[],['CONTENT_TYPE' => 'application/json'],json_encode(["message" => 'may the force be with you', "sessionToken" => '']));
        $this->assertStringContainsString('but here is a list', $client->getResponse()->getContent());
    }
}