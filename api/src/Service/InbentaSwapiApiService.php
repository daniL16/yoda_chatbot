<?php

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;

class InbentaSwapiApiService extends InbentaApiService
{
    private const N_FILMS = 5;
    private const N_PEOPLE = 5;

    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = $_ENV['INBENTA_SWAPI_URL'];
        $this->apiConfig = [
            'api' => ['uri' => '/api', 'method' => 'POST', 'auth' => false],
        ];
    }

    /**
     */
    private function makeQuery(string $query, int $nItems): string
    {
        try {
            $response = $this->exec('api', ['query' => $query, 'variables' => ['first' => $nItems]]);
            return $response->getBody()->getContents();
        } catch (GuzzleException $exception){
            return json_encode(['status' => $exception->getCode(), 'error' => $exception->getMessage()]);
        }
    }

    /**
     */
    public function getFilms(): array
    {
        $query = <<<'GRAPHQL'
                    query allFilms($first:Int) {
                        allFilms(first:$first)  {
                            films{
                                title
                            }
                        }
                    }
                  GRAPHQL;
        $response = json_decode($this->makeQuery($query, self::N_FILMS), true);

        return array_column($response['data']['allFilms']['films'], 'title');
    }

    public function getPeople(): array
    {
        $query = <<<'GRAPHQL'
                 query allPeople($first:Int){
                       allPeople(first:$first){
                           people{
                            name
                           }
                        }
                 }
                 GRAPHQL;
        $response = json_decode($this->makeQuery($query, self::N_PEOPLE), true);

        return array_column($response['data']['allPeople']['people'], 'name');
    }
}
