<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\InvalidArgumentException;

class ChatBotApiService
{
    /** @var string Api Bearer Token */
    private $token;

    /** @var \GuzzleHttp\Client */
    private $client;

    private $chatbotApiUrl;

    private $apiConfig = [
        'auth' => ['uri' => '/auth', 'method' => 'POST', 'auth' => false,'chatbot'=>false],
        'get_apis' => ['uri' => '/apis', 'method' => 'GET', 'auth' => true, 'chatbot' => false],
        'new_conversation' => ['uri' => '/conversation', 'method' => 'POST', 'auth' => true, 'chatbot' => true],
        'send_message' => ['uri' => '/conversation/message', 'method' => 'POST', 'auth' => true, 'chatbot' => true]
    ];

    public function __construct()
    {
        $this->baseUrl = 'https://api.inbenta.io/v1';
        $this->apiKey = 'nyUl7wzXoKtgoHnd2fB0uRrAv0dDyLC+b4Y6xngpJDY=';
        $this->secret = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm9qZWN0IjoieW9kYV9jaGF0Ym90X2VuIn0.anf_eerFhoNq6J8b36_qbD4VqngX79-yyBKWih_eA1-HyaMe2skiJXkRNpyWxpjmpySYWzPGncwvlwz5ZRE7eg';

        $this->client = new Client();
        $this->token = $this->getToken();
        $this->chatbotApiUrl = $this->getApiUrl();
    }

    private function exec(string $api, array $data = [], array $options = []): \Psr\Http\Message\ResponseInterface
    {
        $headers = [
            'x-inbenta-key' => $this->apiKey,
            'Content-Type' => 'application/json'
        ];
        if ($this->apiConfig[$api]['auth']) {
            $headers['Authorization'] = 'Bearer '.$this->token;
        }
            foreach ($options as $key => $value){
                $headers[$key] = $value;
            }


        $url = $this->apiConfig[$api]['chatbot'] ? $this->chatbotApiUrl : $this->baseUrl;
        $url .= $this->apiConfig[$api]['uri'];
        try {
            switch ($this->apiConfig[$api]['method']) {

                case 'GET':
                    $response = $this->client->get($url, ['headers'=> $headers]);
                    break;
                case 'POST':
                    $response = $this->client->post($url, ['json' => $data,'headers'=> $headers]);
                    break;
                default:
                    throw new InvalidArgumentException('method not allowed');
            }
        }catch (GuzzleException $exception){
            die($exception->getMessage());
        }

        return $response;
    }

    private function getToken(): string
    {
        $body = ['secret' => $this->secret];
        $response = json_decode($this->exec('auth',$body)->getBody()->getContents());
        return $response->accessToken;
    }

    private function getApiUrl(): string {
        $response = json_decode($this->exec('get_apis')->getBody()->getContents());
        return $response->apis->chatbot.'/v1';
    }

    private function openConversation(): string
    {
        $payload = [
            'lang' => 'es'
        ];
        $response = json_decode($this->exec('new_conversation', $payload)->getBody()->getContents());
        return $response->sessionToken;
    }

    public function sendMessage(string $message, string $conversation = null){
        if(!$conversation){
            $conversation = $this->openConversation();
        }

        $response = $this->exec('send_message',['message'=>$message],['x-inbenta-session' => 'Bearer '.$conversation]);
        $response = json_decode($response->getBody()->getContents());
        var_dump($response);
        return json_encode(['sessionToken' => $conversation , 'message' => $response->answers[0]->message]);
    }
}