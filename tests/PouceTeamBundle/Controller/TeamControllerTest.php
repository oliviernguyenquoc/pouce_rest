<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamControllerTest extends WebTestCase
{
    public function testGetTeam()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $content);
        $this->assertTrue(isset($content['id']));
    }

    public function testGetLastIdOfAUser()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/users/16/teams/last');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $content);
        $this->assertTrue(isset($content['id']));
    }
}
