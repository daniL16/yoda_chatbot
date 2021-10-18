<?php

namespace App\Service;

final class ChatBotApiService extends InbentaApiService
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = $this->getApiUrl();
    }

    private function getApiUrl(): string
    {
        $response = json_decode($this->exec('get_apis')->getBody()->getContents());

        return $response->apis->chatbot;
    }

    private function openConversation(): string
    {
        $payload = [
                'lang' => 'es',
            ];
        $response = json_decode($this->exec('new_conversation', $payload)->getBody()->getContents());

        return $response->sessionToken;
    }

    /**
     * @param string $message      Message to send
     * @param string $conversation Session token
     *
     * @return string Json with bot's answer and sessionToken
     */
    public function sendMessage(string $message, string $conversation = ''): string
    {
        if (empty($conversation)) {
            $conversation = $this->openConversation();
        }

        $response = $this->exec('send_message', ['message' => $message], ['x-inbenta-session' => 'Bearer '.$conversation]);
        $response = json_decode($response->getBody()->getContents());

        return json_encode(['session_token' => $conversation, 'response_message' => $response->answers[0]->message]);
    }
}
