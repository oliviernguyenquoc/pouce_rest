<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

     /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testPostUser()
    {
        $client = $this->createClient();

        $data_post = array(
            "fos_user_registration_form" => array(
                'email'         => 'tryTeam@tryteam.com',
                'plainPassword' => array(
                    'first'     => 'passwordTest',
                    'second'    => 'passwordTest'
                ),
                'first_name'    => 'PrenomTest',
                'last_name'     => 'NomTest',
                'sex'           => 'Femme',
                'school'        => 'École centrale de Lille',
                'promotion'     => 'Bac +1',
                'telephone'     => '0600000000'
            )
        );

        $client->request('POST','/api/v1/users', array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post));

        $response = $client->getResponse();
        $this->assertEquals(201,$response->getStatusCode());
 
        $content = $response->getContent();

        $this->assertEquals($content,"User created.");
    }

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

    public function testGetUserByFirstNameAndLastName()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/v1/users/first_name/PrenomTest/last_name/NomTest');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['first_name']));
        $this->assertTrue(isset($content['school']['id']));
        $this->assertTrue(isset($content['school']['location']['lat']));
    }

    public function testGetUserByEmail()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/v1/users/email/'.'tryTeam@tryteam.com');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['first_name']));
        $this->assertTrue(isset($content['school']['id']));
        $this->assertTrue(isset($content['school']['location']['lat']));
    }

    public function testRemoveUser()
    {
        $client_temp = $this->createClient();
        $client_temp->request('GET', '/api/v1/users/email/'.'tryTeam@tryteam.com');
        $response = $client_temp->getResponse();
        $content = json_decode($response->getContent(), true);
        //dump($content['id']);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/users/'.$content['id']);

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());
    }

}
