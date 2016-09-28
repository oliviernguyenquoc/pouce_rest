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
        $this->assertArrayHasKey('city', $content);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['distance']));
        $this->assertTrue(isset($content['city']['id']));
    }

    public function testGetFurthestPositionAction()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4/positions/furthest');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('city', $content);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['distance']));
        $this->assertTrue(isset($content['city']['id']));
    }

    public function testGetPositionAction()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/v1/teams/4/positions');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(1, count($content));
        $this->assertArrayHasKey('city', $content[0]);
        $this->assertTrue(isset($content[0]['id']));
        $this->assertTrue(isset($content[0]['distance']));
        $this->assertTrue(isset($content[0]['city']['id']));
        $this->assertTrue(isset($content[0]['city']['longitude']));
        $this->assertTrue(isset($content[0]['city']['name']));
        $this->assertTrue(isset($content[0]['city']['country']['name']));
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

        $content = $response->getContent();
        $this->assertEquals($content,"Position created.");

        /* ********* Test to edit position *********** */
        $client = $this->createClient();
        $client->request('GET','/api/v1/teams/'.$teamId.'/positions/last');
        $position = json_decode($client->getResponse()->getContent(), true);

        $data_edit = array(
            'created'  => '2020-06-05 12:15:00'
        );

        $client = $this->createClient();
        $client->request('PUT','/api/v1/teams/'.$teamId.'/positions',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(201,$response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"Position modified.");

        /* ********* Test to delete position *********** */
        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/positions/'.$position['id']);

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"Position deleted.");

        $this->deleteUser('1');
        $this->deleteTeam('1');
    }
}
