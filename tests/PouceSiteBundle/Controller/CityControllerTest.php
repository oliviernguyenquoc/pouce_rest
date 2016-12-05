<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityRepository;

class CityControllerTest extends WebTestCase
{
    private $em;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        // get the Entity Manager
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown()
    {
        $this->em->close();
    }

    public function testGetCity()
    {
    	$client = $this->createClient();
        $client->request('GET', '/api/v1/cities/2982652');

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
 
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('country', $content);
        $this->assertTrue(isset($content['id']));
        $this->assertTrue(isset($content['name']));
        $this->assertTrue(isset($content['longitude']));
    }

    public function testPostCity()
    {
        $client = $this->createClient();
        $data = array(
            "name"      => "TryCity",
            "country"   => "France", 
            "latitude"  => 49.0011,
            "longitude" => 1.0011
        );
        
        /*****************  Test create a city  *******************/
        $client->request('POST','/api/v1/cities',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"City created.");

        /*****************  Test create the same city  *******************/
        $client->request('POST','/api/v1/cities',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(500, $response->getStatusCode());


        /*****************  Remove city  ******************/
        $repositoryCity = $this->em->getRepository('PouceSiteBundle:City');

        $city = $repositoryCity->findOneBy(array(),array('id' => 'DESC'));


        $this->em->remove($city);
        $this->em->flush();
    }

    public function testPostCityWithCountryId()
    {
        $client = $this->createClient();
        $data = array(
            "name"      => "TryCity",
            "country"   => 8, 
            "latitude"  => 49.0011,
            "longitude" => 1.0011
        );

        $client->request('POST','/api/v1/cities',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertEquals($content,"City created.");

        /*****************  Remove city  ******************/
        $repositoryCity = $this->em->getRepository('PouceSiteBundle:City');

        $city = $repositoryCity->findOneBy(array(),array('id' => 'DESC'));


        $this->em->remove($city);
        $this->em->flush();
    }
}
