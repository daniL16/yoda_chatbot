<?php

namespace App\Service;

class InbentaSwapiApiService extends InbentaApiService
{

    private const N_FILMS = 5;
    private const N_PEOPLE = 5;

    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = $_ENV['INBENTA_SWAPI_URL'];
        $this->apiConfig = [
            'api' => ['uri' => '/api', 'method' => 'POST', 'auth' => false]
        ];
    }

    /**
     * @param string $query
     * @param int $nItems
     * @return string
     */
    private function makeQuery(string $query, int $nItems): string{
        $response = $this->exec('api',  ['query' => $query, 'variables' => ['first' => $nItems]]);
        return $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    public function getFilms(): string
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
        $response = json_decode($this->makeQuery($query, self::N_FILMS),true);
        return json_encode($response['data']['allFilms']['films']);

    }

    /**
     * @return string
     */
    public function getPeople(): string{
        $query = <<<'GRAPHQL'
                 query allPeople($first:Int){
                       allPeople(first:$first){
                           people{
                            name
                           }
                        }
                 }
                 GRAPHQL;
        $response = json_decode($this->makeQuery($query, self::N_PEOPLE),true);
        return json_encode($response['data']['allPeople']['people']);
    }
}