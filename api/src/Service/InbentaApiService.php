<?php


namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class InbentaApiService
{

    /** @var string Api Bearer Token */
    private $token;

    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var string */
    private $apiVersion;

    /** @var array */
    protected $apiConfig = [
        'auth' => ['uri' => '/auth', 'method' => 'POST', 'auth' => false],
        'get_apis' => ['uri' => '/apis', 'method' => 'GET', 'auth' => true]
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


    protected function exec(string $api, array $data = [], array $options = []): \Psr\Http\Message\ResponseInterface
    {
        // Build url
        $url = $this->baseUrl.'/'.$this->apiVersion;
        $url .= $this->apiConfig[$api]['uri'];

        // Build headers array
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


        try {
            switch ($this->apiConfig[$api]['method']) {
                case 'GET':
                    $response = $this->client->get($url, ['headers'=> $headers]);
                    break;
                case 'POST':
                    $response = $this->client->post($url, ['json' => $data,'headers'=> $headers]);
                    break;
                default:
                    throw new \InvalidArgumentException('Method not allowed');
            }
        }catch (GuzzleException $exception){
            die($exception->getMessage());
        }

        return $response;
    }

    protected function getToken(): string
    {
        $body = ['secret' => $this->secret];
        $response = json_decode($this->exec('auth',$body)->getBody()->getContents());
        return $response->accessToken;
    }

    protected function getApiUrls(): \stdClass {
        return json_decode($this->exec('get_apis')->getBody()->getContents());
    }

}