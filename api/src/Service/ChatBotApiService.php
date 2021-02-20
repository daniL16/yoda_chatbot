<?php

namespace App\Service;

class ChatBotApiService extends InbentaApiService
{

    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = $this->getApiUrl();
        $serviceApiConfig = [
            'new_conversation' => ['uri' => '/conversation', 'method' => 'POST', 'auth' => true],
            'send_message' => ['uri' => '/conversation/message', 'method' => 'POST', 'auth' => true]
        ];
        $this->apiConfig = array_merge($this->apiConfig, $serviceApiConfig);
    }

    private function openConversation(): string
    {
        $payload = [
            'lang' => 'es'
        ];
        $response = json_decode($this->exec('new_conversation', $payload)->getBody()->getContents());
        return $response->sessionToken;
    }

    /**
     * @param string $message Message to send
     * @param string|null $conversation Session token
     * @return array Array with bot's answer and sessionToken
     */
    public function sendMessage(string $message, string $conversation = null): array {
        if(!$conversation){
            $conversation = $this->openConversation();
        }

        $response = $this->exec('send_message',['message'=>$message],['x-inbenta-session' => 'Bearer '.$conversation]);
        $response = json_decode($response->getBody()->getContents());
        return ['session_token' => $conversation , 'response_message' => $response->answers[0]->message];
    }

    private function getApiUrl(): string {
        return $this->getApiUrls()->apis->chatbot;
    }
}