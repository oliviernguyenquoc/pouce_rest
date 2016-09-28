<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditionControllerTest extends WebTestCase
{
    public function testGetEdition()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/editions/1');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['date_of_event']));
        $this->assertTrue(isset($content['status']));
    }
}
