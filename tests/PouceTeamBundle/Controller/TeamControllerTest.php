<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Pouce\TeamBundle\Tests\Controller\CustomTestcase;

require_once('CustomTestcase.php');

class TeamControllerTest extends CustomTestCase
{

    protected function setUp()
    {
        //We create 3 tests users
        $this->createUser('1','Homme');
        $this->createUser('2','Femme');
        $this->createUser('3','Femme');
    }

    protected function tearDown()
    {
        //We delete tests users
        $this->deleteUser('1');
        $this->deleteUser('2');
        $this->deleteUser('3');
    }

    public function testGetTeam()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['team_name']));
        $this->assertTrue(isset($content['comment']));
        $this->assertTrue(isset($content['target_destination']));
        $this->assertTrue(isset($content['users'][0]['id']));
        $this->assertTrue(isset($content['users'][1]['id']));
        $this->assertTrue(isset($content['edition']['id']));
        $this->assertTrue(isset($content['edition']['date_of_event']));
        $this->assertTrue(isset($content['edition']['status']));
    }

    public function testGetUserLastTeam()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/users/16/teams/last');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['team_name']));
        $this->assertTrue(isset($content['comment']));
        $this->assertTrue(isset($content['target_destination']));
        $this->assertTrue(isset($content['users'][0]['id']));
        $this->assertTrue(isset($content['users'][1]['id']));
        $this->assertTrue(isset($content['edition']['id']));
        $this->assertTrue(isset($content['edition']['date_of_event']));
        $this->assertTrue(isset($content['edition']['status']));
    }

    public function testPostTeam()
    {
        $data = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'        => 'tryteam1@tryteam.com',
            'userEmail2'        => 'tryteam2@tryteam.com',
            'startCity'         => 2990969
        );
        
        //Test create a team
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"Team created.");


        /**************  Test remove team  **********************/
        $teamId = $this->getTeamId('1');

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/teams/'.$teamId);

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());
    }

    /**
    *   Test all the different conditions that fails to create a team (have already a team ...)
    */
    public function testConditionInPostTeam()
    {
        /* ************  Test to create team F/F  *************/
        $data_1 = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'        => 'tryteam2@tryteam.com',
            'userEmail2'        => 'tryteam3@tryteam.com',
            'startCity'         => 2990969
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
            'userEmail1'        => 'tryteam1@tryteam.com',
            'userEmail2'        => 'tryteam2@tryteam.com',
            'startCity'         => 2990969
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
            'userEmail2'        => 'tryteam3@tryteam.com',
            'startCity'         => 2990969
        );
        
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_3));

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());


        /* ***************  Remove team  *************/
        $this->deleteTeam('1');
    }

    public function testPutTeam()
    {
        /* **************  We create a test team  *************** */
        $data_1 = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'        => 'tryteam1@tryteam.com',
            'userEmail2'        => 'tryteam2@tryteam.com',
            'startCity'         => 2990969
        );

        $client_1 = $this->createClient();
        $client_1->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_1));

        /* **************  We modify the team  *************** */
        $data_2 = array(
            'teamName'          => 'tryTeam2',
            'targetDestination' => 'tryDestination2',
            'comment'           => 'A Try Comment2'
        );

        $teamId = $this->getTeamId('1');

        //PUT test
        $client = $this->createClient();
        $client->request('PUT','/api/v1/teams/'.$teamId,array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_2));

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"Team modified.");


        /* ***** Verify if the team has been modified  ******/
        //Verify that the team have been modified
        $client_2 = $this->createClient();
        $client_2->request('GET', '/api/v1/teams/'.$teamId);
        $team = json_decode($client_2->getResponse()->getContent(), true);

        $this->assertEquals('tryTeam2', $team['team_name']);
        $this->assertEquals('tryDestination2', $team['target_destination']);
        $this->assertEquals('A Try Comment2', $team['comment']);


        /* ***************  Remove team  *************/
        $this->deleteTeam('1');
    }
}
