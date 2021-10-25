<?php

namespace App\Tests\Service;

use App\Service\InbentaSwapiApiService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InbentaSwapApiTest extends WebTestCase{

    private InbentaSwapiApiService $apiClient;

    public function setUp(): void
    {
        $this->apiClient = new InbentaSwapiApiService();
    }

    public function testGetFilms()
    {
        $films = $this->apiClient->getFilms();
        /*
         * We expect an array with films.
         */
        $this->assertIsArray($films);
        $this->assertGreaterThan(0, $films);
    }

    public function testGetPeople()
    {
        $people = $this->apiClient->getPeople();
        /*
         * We expect an array with some characters
         */
        $this->assertIsArray($people);
        $this->assertGreaterThan(0, $people);
    }

}