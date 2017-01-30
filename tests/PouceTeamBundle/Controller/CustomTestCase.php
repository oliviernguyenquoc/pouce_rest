<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomTestcase extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = $this->createClient();
    }

	public static function setUpBeforeClass()
	{
	    // Temporarily increase memory limit to 256MB
	    ini_set('memory_limit','512M');
	}    

    /*
    *   Private function that create a test user with email tryteam.$number.@tryteam.com
    */
    protected function createUser($number, $genre)
    {
        $client = $this->createClient();

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
                'telephone'     => '0600000000'
            )
        );

        $client->request('POST','/api/v1/users', array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post_user));
    }

    /*
    *   Private function that delete a test user with email tryteam.$number.@tryteam.com
    */
    protected function deleteUser($number)
    {
        $client = $this->createClient();

        $client->request('GET', '/api/v1/users/email/tryteam'.$number.'@tryteam.com');
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $client->request('DELETE', '/api/v1/users/'.$content['id']);
    }

    /*
    *	Private function that create a test team
    */
    protected function createTeam($number1, $number2)
    {
        $client = $this->createClient();

    	$data = array(
            'teamName'          => 'tryTeam',
            'targetDestination' => 'tryDestination',
            'comment'           => 'A Try Comment',
            'editionId'         => 1,
            'userEmail1'        => 'tryteam'.$number1.'@tryteam.com',
            'userEmail2'        => 'tryteam'.$number2.'@tryteam.com',
            'startCity'         => 2990969
        );
        
        //Test create a team

        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
    }

    /*
    *	Private function that delete a test team
    */
    protected function deleteTeam($number)
    {
        $client = $this->createClient();

        /**************  Test remove team  **********************/
        //Get user to have his id
        $client->request('GET', '/api/v1/users/email/'.'tryteam'.$number.'@tryteam.com');
        $user = json_decode($client->getResponse()->getContent(), true);

        //Find his team
        $client->request('GET', '/api/v1/users/'.$user['id'].'/teams/last');
        $team = json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', '/api/v1/teams/'.$team['id']);
    }

    protected function getTeamId($number)
    {
    	$client = $this->createClient();

        $client->request('GET', '/api/v1/users/email/tryteam'.$number.'@tryteam.com');
        $user = json_decode($client->getResponse()->getContent(), true);

        //Find his team
        $client = $this->createClient();
        $client->request('GET', '/api/v1/users/'.$user['id'].'/teams/last');
        $team = json_decode($client->getResponse()->getContent(), true);

        return $team['id'];
    }

    /**
    *   Get access token from Oauth autentification
    */
    protected function getAccessToken()
    {
        $client = $this->createClient();

        require('config_oauth.php');

        $client->request('GET', '/oauth/v2/token?grant_type=password&username='.$username.'&password='.$password.'&client_id='.$client_test.'&client_secret='.$client_secret_test);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $access_token = $content['access_token'];

        return $access_token;
    }

}