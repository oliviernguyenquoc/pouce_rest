<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetUser()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/users/16');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['first_name']));
        $this->assertTrue(isset($content['school']['id']));
        $this->assertTrue(isset($content['school']['location']['lat']));

    }

}
