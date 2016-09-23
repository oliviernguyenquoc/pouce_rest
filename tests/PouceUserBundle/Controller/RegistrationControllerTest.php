<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
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
                'promotion'     => 'Master 1',
                'telephone'     => '0600000000'
            )
        );

        $client->request('POST','/api/v1/users', array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post));

        $response = $client->getResponse();
        $this->assertEquals(201,$response->getStatusCode());
 
        //$content = json_decode($response->getContent(), true);
        //dump($content);
        $this->assertTrue($response,"User created.");
    }

}
