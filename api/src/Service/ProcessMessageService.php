<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;

final class ProcessMessageService
{

    public const NOT_FOUND_ATTEMPTS = 2;

    public function __construct(
        private ChatBotApiService $chatBotApiClient,
        private InbentaSwapiApiService $swapiApiClient
    )
    {
    }

    /**
     * @param string $message
     * @param string $conversationToken
     * @param int $previousNotFound
     * @return string
     * @throws GuzzleException
     */
    public function getResponseMessage(string $message, string $conversationToken, int $previousNotFound = 0): string{
        // If the message contains the word force we get the list of movies
        if(str_contains(strtolower($message), 'force')){
            $responseMessage = $this->formatMessage($this->swapiApiClient->getFilms(), 'films');
            $response = ['session_token' => $conversationToken, 'response_message' => $responseMessage];
        }
        else{
            $response = $this->chatBotApiClient->sendMessage($message, $conversationToken);
            // If the response message is of type 'not_found' we get the list of characters.
            if(str_contains($response['response_message'],'couldn\'t find')
                || str_contains($response['response_message'], 'Please search again')
            ){
                $response['not_found_message'] = true;
                if($previousNotFound >= self::NOT_FOUND_ATTEMPTS){
                    $response['response_message'] = $this->formatMessage($this->swapiApiClient->getPeople(), 'characters');
                }
            }
        }

        return (string) json_encode($response);
    }

    /**
     * @param array<String> $values
     * @param string $type
     * @return string
     */
    private function formatMessage(array $values, string $type): string{
        $list = '<ul>';
        foreach ($values as $value){
            $list.= "<li> $value </li>";
        }
        $list .= '</ul>';
        return "I haven't found any results, but here is a list of some Star Wars $type : $list";
    }
}