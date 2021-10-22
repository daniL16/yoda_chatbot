<?php

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;

final class ChatBotApiService extends InbentaApiService
{
    /**
     * @throws GuzzleException
     */
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = $this->getApiUrl().'/'.$_ENV['INBENTA_API_VERSION'];
        $this->apiConfig = array_merge($this->apiConfig, [
            'new_conversation' => ['uri' => '/conversation', 'method' => 'POST', 'auth' => true],
            'send_message' => ['uri' => '/conversation/message', 'method' => 'POST', 'auth' => true],
        ]);
    }

    /**
     * @throws GuzzleException
     */
    private function getApiUrl(): string
    {
        $response = json_decode($this->exec('get_apis')->getBody()->getContents());

        return $response->apis->chatbot;
    }

    /**
     * @throws GuzzleException
     */
    private function openConversation(): string
    {
        $payload = [
            'lang' => 'en',
        ];
        $response = json_decode($this->exec('new_conversation', $payload)->getBody()->getContents());

        return $response->sessionToken;
    }

    /**
     * @param string $message Message to send
     * @param string $conversation Session token
     *
     * @return array with bot's answer and sessionToken
     * @throws GuzzleException
     */
    public function sendMessage(string $message, string $conversationToken = ''): array
    {
        if (empty($conversationToken)) {
            $conversationToken = $this->openConversation();
        }

        try {
            $response = $this->exec('send_message', ['message' => $message], ['x-inbenta-session' => 'Bearer ' . $conversationToken]);
            $response = json_decode($response->getBody()->getContents());
        }catch (GuzzleException $exception){
            // If session expired, open a new conversation
           if(str_contains($exception->getMessage(),'Session expired')){
               return $this->sendMessage($message);
           }
           // Aquí habría que tratar el error mejor
           return ['session_token' => $conversationToken, 'response_message' => ''];
        }

        return ['session_token' => $conversationToken, 'response_message' => $response->answers[0]->message];
    }
}
