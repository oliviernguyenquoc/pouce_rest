<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Pouce\TeamBundle\Tests\Controller\CustomTestcase;

require_once('CustomTestcase.php');

class ResultControllerTest extends CustomTestcase
{
    public function testGetTeamResult()
    {
        $this->client->request('GET', '/api/v1/teams/16/results');

        $response = $this->client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['lateness']));
        $this->assertTrue(isset($content['is_valid']));
        $this->assertTrue(isset($content['nb_car']));
        $this->assertTrue(isset($content['position']));
        $this->assertTrue(isset($content['rank']));
    }

    public function testPostResult()
    {
        // $this->createUser('1','Homme');
        // $this->createUser('2','Femme');
        // $this->createTeam('1', '2');
        // $teamId = $this->getTeamId('1');

        // $this->deleteUser('1');
        // $this->deleteUser('2');
        // $this->deleteTeam('1');
    }
}
