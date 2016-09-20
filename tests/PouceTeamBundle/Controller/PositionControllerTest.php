<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PositionControllerTest extends WebTestCase
{
    public function testGetLastPosition()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4/positions/last');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('position', $content);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['distance']));
        $this->assertTrue(isset($content['position']['id']));
    }

    public function testGetImportantPositionsAction()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4/positions/important');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['count']));
        $this->assertTrue(isset($content['last_position']));
        $this->assertTrue(isset($content['last_position']['id']));
        $this->assertTrue(isset($content['furthest_position']));
        $this->assertTrue(isset($content['furthest_position']['id']));
    }

    public function testGetPositionsAction()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4/positions');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(1, count($content));
        $this->assertArrayHasKey('position', $content[0]);
        $this->assertTrue(isset($content[0]['id']));
        $this->assertTrue(isset($content[0]['distance']));
        $this->assertTrue(isset($content[0]['position']['id']));
        $this->assertTrue(isset($content[0]['position']['city']));
    }
}
