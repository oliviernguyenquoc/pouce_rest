<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected $client;

     /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = $this->createClient();
    }

    public function testPostAndPutUser()
    {
        /* *********************  Test Post User ******************* */
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
                'telephone'     => '0600000000'
            )
        );

        $this->client->request('POST','/api/v1/users', array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post));

        $response = $this->client->getResponse();
        $this->assertEquals(201,$response->getStatusCode());
 
        $content = $response->getContent();
        $this->assertEquals($content,"User created.");

        /* *********************  Get User id ******************* */
        $this->client->request('GET', '/api/v1/users/first_name/PrenomTest/last_name/NomTest');

        $user = json_decode($this->client->getResponse()->getContent(), true);

        /* *********************  Test Put User ******************* */
        $data_post = array(
            'first_name'    => 'PrenomTest2',
            'last_name'     => 'NomTest2',
            'sex'           => 'Homme',
            'school'        => 'Ecole Centrale de Nantes',
            'telephone'     => '0611111111'
        );

        $this->client->request('PUT','/api/v1/users/'.$user['id'], array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post));

        $response = $this->client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());

        // Verify changes
        $this->client->request('GET', '/api/v1/users/'.$user['id']);

        $user_modified = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('PrenomTest2',$user_modified['first_name']);
        $this->assertEquals(1,$user_modified['school']['id']);
        $this->assertEquals('0611111111', $user_modified['telephone']);
        $this->assertTrue(isset($user_modified['email']));
        $this->assertNotEquals('',$user_modified['email']);
    }

    public function testGetUser()
    {
        $this->client->request('GET', '/api/v1/users/16', array(),array(),
            array(
                'accept'            => 'application/json',
                'accept-encoding'   => 'gzip, deflate',
                'accept-language'   => 'en-US,en;q=0.8',
                'user-agent'        => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
                'content_type'      => 'application/json'
        ));

        $response = $this->client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['first_name']));
        $this->assertTrue(isset($content['email']));
        $this->assertTrue(isset($content['school']['id']));
        $this->assertTrue(isset($content['school']['name']));
        $this->assertTrue(isset($content['school']['city']['name']));
    }

    public function testGetUserByFirstNameAndLastName()
    {
        $this->client->request('GET', '/api/v1/users/first_name/PrenomTest2/last_name/NomTest2');

        $response = $this->client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['first_name']));
        $this->assertTrue(isset($content['email']));
        $this->assertTrue(isset($content['school']['id']));
        $this->assertTrue(isset($content['school']['name']));
        $this->assertTrue(isset($content['school']['city']['name']));
    }

    public function testGetUserByEmail()
    {
        $this->client->request('GET', '/api/v1/users/email/'.'tryTeam@tryteam.com');

        $response = $this->client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['first_name']));
        $this->assertTrue(isset($content['email']));
        $this->assertTrue(isset($content['school']['id']));
        $this->assertTrue(isset($content['school']['name']));
        $this->assertTrue(isset($content['school']['city']['name']));
    }

    public function testRemoveUser()
    {
        $this->client->request('GET', '/api/v1/users/email/'.'tryTeam@tryteam.com');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->client->request('DELETE', '/api/v1/users/'.$content['id']);

        $response = $this->client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());
    }

}
