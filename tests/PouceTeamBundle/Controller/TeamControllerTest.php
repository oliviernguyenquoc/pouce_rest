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

    public function testGetUserLastTeam()
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
        //We create 2 tests users
        $this->createUser('1','Homme');
        $this->createUser('2','Femme');

        $data = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'             => 'tryteam1@tryteam.com',
            'userEmail2'             => 'tryteam2@tryteam.com'
        );
        
        //Test create a team
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"Team created.");


        /**************  Test remove team  **********************/
        //Get user to have his id
        $client_1 = $this->createClient();
        $client_1->request('GET', '/api/v1/users/email/'.'tryteam1@tryteam.com');
        $content_1 = json_decode($client_1->getResponse()->getContent(), true);

        //Find his team
        $client_2 = $this->createClient();
        $client_2->request('GET', '/api/v1/users/'.$content_1['id'].'/teams/last');
        $content_2 = json_decode($client_2->getResponse()->getContent(), true);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/teams/'.$content_2['id']);

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());

        //We delete tests users
        $this->deleteUser('1');
        $this->deleteUser('2');
    }

    /**
    *   Test all the different conditions that fails to create a team (have already a team ...)
    */
    public function testConditionInPostTeam()
    {
         //We create 3 tests users
        $this->createUser('1','Homme');
        $this->createUser('2','Femme');
        $this->createUser('3','Femme');

        /* ************  Test to create team F/F  *************/
        $data_1 = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'             => 'tryteam2@tryteam.com',
            'userEmail2'             => 'tryteam3@tryteam.com'
        );
        
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_1));

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());


        //Create a real team
        $data_2 = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'             => 'tryteam1@tryteam.com',
            'userEmail2'             => 'tryteam2@tryteam.com'
        );
        
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_2));


        /* ******  Test create a team with 1 already in a team  ******/
        $data_3 = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'        => 'tryteam1@tryteam.com',
            'userEmail2'        => 'tryteam3@tryteam.com'
        );
        
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_3));

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());


        /* ***************  Remove team  *************/
        //Get user to have his id
        $client_1 = $this->createClient();
        $client_1->request('GET', '/api/v1/users/email/'.'tryteam1@tryteam.com');
        $content_1 = json_decode($client_1->getResponse()->getContent(), true);

        //Find his team
        $client_2 = $this->createClient();
        $client_2->request('GET', '/api/v1/users/'.$content_1['id'].'/teams/last');
        $content_2 = json_decode($client_2->getResponse()->getContent(), true);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/teams/'.$content_2['id']);

        //We delete tests users
        $this->deleteUser('1');
        $this->deleteUser('2');
        $this->deleteUser('3');
    }

    /*
    *   Private function that create a test user with email tryteam.$number.@tryteam.com
    */
    private function createUser($number, $genre)
    {
        $client_temp_user = $this->createClient();

        $data_post_user = array(
            "fos_user_registration_form" => array(
                'email'         => 'tryteam'.$number.'@tryteam.com',
                'plainPassword' => array(
                    'first'     => 'passwordTest',
                    'second'    => 'passwordTest'
                ),
                'first_name'    => 'PrenomTest'.$number,
                'last_name'     => 'NomTest'.$number,
                'sex'           => $genre,
                'school'        => 'École centrale de Lille',
                'promotion'     => 'Bac +1',
                'telephone'     => '0600000000'
            )
        );

        $client_temp_user->request('POST','/api/v1/users', array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post_user));
    }

    /*
    *   Private function that delete a test user with email tryteam.$number.@tryteam.com
    */
    private function deleteUser($number)
    {
        $client_temp = $this->createClient();
        $client_temp->request('GET', '/api/v1/users/email/'.'tryteam'.$number.'@tryteam.com');
        $response = $client_temp->getResponse();
        $content = json_decode($response->getContent(), true);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/users/'.$content['id']);
    }

    /**
    *   Get access token from Oauth autentification
    */
    private function getAccessToken()
    {
        require('config_oauth.php');
        
        $client = $this->createClient();

        $client->request('GET', '/oauth/v2/token?grant_type=password&username='.$username.'&password='.$password.'&client_id='.$client_test.'&client_secret='.$client_secret_test);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $access_token = $content['access_token'];

        return $access_token;
    }
}
