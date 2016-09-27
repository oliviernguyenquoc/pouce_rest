<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Pouce\TeamBundle\Tests\Controller\CustomTestcase;

require_once('CustomTestcase.php');

class PositionControllerTest extends CustomTestcase
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

    public function postPositionAction()
    {
        $this->createUser('1','Homme');
        $this->createUser('2','Femme');
        $this->createTeam('1', '2');
        $teamId = $this->getTeamId('1');

        $data = array(
            'city'         => 2990969
        );

        /* ********* Test to create position *********** */
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams/'.$teamId.'/positions',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(201,$response->getStatusCode());

        /* ********* Test to delete position *********** */
        $client = $this->createClient();
        $client->request('GET', '/api/v1/teams/'.$teamId.'/positions/last');
        $positionLast = json_decode($client->getResponse()->getContent(), true);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/positions/'.$positionLast['id']);

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());

        $this->deleteUser('1');
        $this->deleteTeam('1');
    }
}
