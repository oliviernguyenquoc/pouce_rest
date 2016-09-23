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
        $this->assertTrue(isset($content['user 1']['id']));
        $this->assertTrue(isset($content['user 2']['id']));
        $this->assertTrue(isset($content['edition']['id']));
        $this->assertTrue(isset($content['positions'][0]['id']));
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

    public function testPostTeam()
    {
        $access_token = $this->getAccessToken();

        $client = $this->createClient();

        $response_temp_user = $client->getResponse();
        $content_temp_user = json_decode($response_temp_user->getContent(), true);

        $data = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'user'              => $content_temp_user
        );
        
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',json_encode($data), array(), array('HTTP_AUTHORIZATION' => "Bearer {$accessToken}"));
        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        //$this->assertTrue($response->hasHeader('Location'));
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('nickname', $data);
    }

    private function getAccessToken()
    {
        require('config_oauth.php');
        
        $client = $this->createClient();

        $client->request('GET', '/oauth/v2/token?grant_type=password&username='.$username.'&password='.$password.'&client_id='.$client_test.'&client_secret='.$client_secret_test);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $access_token = $content['access_token'];

        return access_token;
    }
}
