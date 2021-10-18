<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class InbentaApiService
{
    /** @var string  */
    private string $token;

    /** @var string */
    private string $apiVersion;

    /** @var string */
    protected string $baseUrl;

    /** @var string */
    private string $apiKey;

    /** @var string  */
    private string $secret;

    /** @var Client  */
    protected Client $client;

    /** @var array */
    protected array $apiConfig = [
        'auth' => ['uri' => '/auth', 'method' => 'POST', 'auth' => false],
        'get_apis' => ['uri' => '/apis', 'method' => 'GET', 'auth' => true],
        'new_conversation' => ['uri' => '/conversation', 'method' => 'POST', 'auth' => true],
        'send_message' => ['uri' => '/conversation/message', 'method' => 'POST', 'auth' => true],
    ];

    public function __construct()
    {
        $this->baseUrl = $_ENV['INBENTA_API_URL'];
        $this->apiKey = $_ENV['INBENTA_API_KEY'];
        $this->secret = $_ENV['INBENTA_API_SECRET'];
        $this->apiVersion = $_ENV['INBENTA_API_VERSION'];
        $this->client = new Client();
        $this->token = $this->getToken();
    }

    protected function exec(string $api, array $data = [], array $options = []): ResponseInterface
    {
        // Build url
        $url = $this->baseUrl . '/' . $this->apiVersion;
        $url .= $this->apiConfig[$api]['uri'];

        // Build headers array
        $headers = [
            'x-inbenta-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
        if ($this->apiConfig[$api]['auth']) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }
        foreach ($options as $key => $value) {
            $headers[$key] = $value;
        }

        try {
            $response = match ($this->apiConfig[$api]['method']) {
                'GET' => $this->client->get($url, ['headers' => $headers]),
                'POST' => $this->client->post($url, ['json' => $data, 'headers' => $headers]),
                default => throw new InvalidArgumentException('Method not allowed'),
            };
        } catch (GuzzleException $exception) {
            $response = new JsonResponse(['url' => $url,'error' => $exception->getMessage(), 'headers' => $headers, 'data' => $data]);
            die($response);
        }

        return $response;
    }

    protected function getToken(): string
    {
        $body = ['secret' => $this->secret];
        $response = json_decode($this->exec('auth', $body)->getBody()->getContents());
        return $response->accessToken;
    }

    /**
     * @param string $token
     * @return InbentaApiService
     */
    public function setToken(string $token): InbentaApiService
    {
        $this->token = $token;
        return $this;
    }
}
