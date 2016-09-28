<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityControllerTest extends WebTestCase
{
    public function testGetCity()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/cities/2982652');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('country', $content);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['name']));
        $this->assertTrue(isset($content['longitude']));
    }
}
