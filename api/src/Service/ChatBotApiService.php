<?php

declare(strict_types=1);

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
     * Start a new conversation. Returns the token of the conversation,.
     *
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
     * Send a new message to the bot. If the conversion token is not provided, start a new one.
     *
     * @param string $message Message to send
     *
     * @return array<String> with bot's answer and sessionToken
     *
     * @throws GuzzleException
     */
    public function sendMessage(string $message, string $conversationToken = ''): array
    {
        if (empty($conversationToken)) {
            $conversationToken = $this->openConversation();
        }

        $responseMessage = '';

        try {
            $response = $this->exec('send_message', ['message' => $message], ['x-inbenta-session' => 'Bearer '.$conversationToken]);
            $response = json_decode($response->getBody()->getContents());
            $responseMessage = $response->answers[0]->message;
        } catch (GuzzleException $exception) {
            // If session expired, open a new conversation
            if (str_contains($exception->getMessage(), 'Session expired')) {
                return $this->sendMessage($message);
            }
        }

        return ['session_token' => $conversationToken, 'response_message' => $responseMessage];
    }
}
