<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchoolControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = $this->createClient();
    }

    public function testGetEditionSchools()
    {
        $this->client->request('GET', '/api/v1/editions/1/schools');

        $response = $this->client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content[0]['id']));
        $this->assertTrue(isset($content[0]['city']));
        $this->assertTrue(isset($content[0]['name']));
        $this->assertGreaterThanOrEqual(1, count($content));

    }

}
