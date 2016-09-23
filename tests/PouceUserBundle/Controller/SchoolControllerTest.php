<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchoolControllerTest extends WebTestCase
{
    public function testGetEditionSchools()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/editions/1/schools');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content[0]['id']));
        $this->assertTrue(isset($content[0]['city']));
        $this->assertTrue(isset($content[0]['name']));
        $this->assertGreaterThanOrEqual(1, count($content));

    }

}
