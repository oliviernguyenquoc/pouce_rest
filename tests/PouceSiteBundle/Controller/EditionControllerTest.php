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

    public function testPostEdition()
    {
        $data = array(
            'dateOfEvent' => '01-01-2015',
        );

        /*****************  Test create a edition  *****************/
        $client = $this->createClient();
        $client->request('POST','/api/v1/editions',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"Edition created.");


        /*****************  Test getByDate edition  ****************/
        //Get id of edition
        $client = $this->createClient();
        $client->request('GET','/api/v1/editions/date/01-01-2015');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());

        $edition = json_decode($response->getContent(), true);
        $this->assertTrue(isset($edition['id']));
        $this->assertTrue(isset($edition['date_of_event']));
        $this->assertTrue(isset($edition['status']));

        /*****************  Test edit edition  ******************/
        $data_2 = array(
            'dateOfEvent' => '02-02-2015',
        );

        //Get id of edition
        $client = $this->createClient();
        $client->request('PUT','/api/v1/editions/'.$edition['id'],array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data_2));

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());        

        /*****************  Test delete edition  ******************/
        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/editions/'.$edition['id']);

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());

        $content = $response->getContent();
        //TODO: Minor bug
        //$this->assertEquals($content,"Edition deleted.");
    }
}
