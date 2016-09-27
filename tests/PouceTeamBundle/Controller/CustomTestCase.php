<?php

namespace Pouce\TeamBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomTestcase extends WebTestCase
{
	public static function setUpBeforeClass()
	{
	    // Temporarily increase memory limit to 256MB
	    ini_set('memory_limit','256M');
	}    

    /*
    *   Private function that create a test user with email tryteam.$number.@tryteam.com
    */
    protected function createUser($number, $genre)
    {
        $client_temp_user = $this->createClient();

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
                'promotion'     => 'Bac +1',
                'telephone'     => '0600000000'
            )
        );

        $client_temp_user->request('POST','/api/v1/users', array(),array(),array('CONTENT_TYPE' => 'application/json'), json_encode($data_post_user));
    }

    /*
    *   Private function that delete a test user with email tryteam.$number.@tryteam.com
    */
    protected function deleteUser($number)
    {
        $client_temp = $this->createClient();
        $client_temp->request('GET', '/api/v1/users/email/tryteam'.$number.'@tryteam.com');
        $response = $client_temp->getResponse();
        $content = json_decode($response->getContent(), true);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/users/'.$content['id']);
    }

    /*
    *	Private function that create a test team
    */
    protected function createTeam($number1, $number2)
    {
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
        $client = $this->createClient();
        $client->request('POST','/api/v1/teams',array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
    }

    /*
    *	Private function that delete a test team
    */
    protected function deleteTeam($number)
    {
        /**************  Test remove team  **********************/
        //Get user to have his id
        $client_1 = $this->createClient();
        $client_1->request('GET', '/api/v1/users/email/'.'tryteam'.$number.'@tryteam.com');
        $user = json_decode($client_1->getResponse()->getContent(), true);

        //Find his team
        $client_2 = $this->createClient();
        $client_2->request('GET', '/api/v1/users/'.$user['id'].'/teams/last');
        $team = json_decode($client_2->getResponse()->getContent(), true);

        $client = $this->createClient();
        $client->request('DELETE', '/api/v1/teams/'.$team['id']);
    }

    protected function getTeamId($number)
    {
    	$client_1 = $this->createClient();
        $client_1->request('GET', '/api/v1/users/email/tryteam'.$number.'@tryteam.com');
        $user = json_decode($client_1->getResponse()->getContent(), true);

        //Find his team
        $client_2 = $this->createClient();
        $client_2->request('GET', '/api/v1/users/'.$user['id'].'/teams/last');
        $team = json_decode($client_2->getResponse()->getContent(), true);

        return $team['id'];
    }

    /**
    *   Get access token from Oauth autentification
    */
    protected function getAccessToken()
    {
        require('config_oauth.php');
        
        $client = $this->createClient();

        $client->request('GET', '/oauth/v2/token?grant_type=password&username='.$username.'&password='.$password.'&client_id='.$client_test.'&client_secret='.$client_secret_test);
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $access_token = $content['access_token'];

        return $access_token;
    }

}