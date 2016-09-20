<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultControllerTest extends WebTestCase
{
    public function testGetTeamResult()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/teams/16/results');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['lateness']));
        $this->assertTrue(isset($content['isValid']));
        $this->assertTrue(isset($content['nbCar']));
        $this->assertTrue(isset($content['furthest position']));
        $this->assertTrue(isset($content['rank']));
    }
}
