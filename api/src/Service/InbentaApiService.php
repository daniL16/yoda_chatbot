<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

abstract class InbentaApiService
{
    private string $token;

    protected string $baseUrl;

    private string $apiKey;

    private string $secret;

    protected Client $client;

    protected array $apiConfig = [
        'auth' => ['uri' => '/auth', 'method' => 'POST', 'auth' => false],
        'get_apis' => ['uri' => '/apis', 'method' => 'GET', 'auth' => true],
    ];

    public function __construct()
    {
        $this->baseUrl = $_ENV['INBENTA_API_URL'].'/'.$_ENV['INBENTA_API_VERSION'];
        $this->apiKey = $_ENV['INBENTA_API_KEY'];
        $this->secret = $_ENV['INBENTA_API_SECRET'];
        $this->client = new Client();
        $this->token = $this->getToken();
    }

    /**
     * @param string $api
     * @param array $data
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function exec(string $api, array $data = [], array $options = []): ResponseInterface
    {
        // Build url
        $url = $this->baseUrl;
        $url .= $this->apiConfig[$api]['uri'];

        $headers = $this->buildHeader($api, $options);

        return match ($this->apiConfig[$api]['method']) {
            'GET' => $this->client->get($url, ['headers' => $headers]),
            'POST' => $this->client->post($url, ['json' => $data, 'headers' => $headers]),
            default => throw new InvalidArgumentException('Method not allowed'),
        };
    }

    protected function getToken(): string
    {
        $body = ['secret' => $this->secret];
        try {
            $response = json_decode($this->exec('auth', $body)->getBody()->getContents());
            return $response->accessToken;
        }catch (GuzzleException){
            return '';
        }
    }

    /**
     * @param string $api
     * @param array $options
     * @return array
     */
    private function buildHeader(string $api, array $options = []): array{
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

        return $headers;
    }
}
