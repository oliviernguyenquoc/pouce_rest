<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

class TeamController extends Controller
{
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a team with the id of a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the team"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{id}")
	 */
	public function getTeamAction($id){

		$em = $this->getDoctrine()->getManager();

		$team = $em->getRepository('PouceTeamBundle:Team')->findOneBy(array('id' => $id));
		if(!is_object($team)){
			throw $this->createNotFoundException();
		}
		$users = $team->getUsers();

		$userId1 = $users->get(0)->getId();
		$userId2 = $users->get(1)->getId();

		$user1 = $this->forward('PouceUserBundle:User:getUser', array('id' => $userId1), array('_format' => 'json'));
		$user2 = $this->forward('PouceUserBundle:User:getUser', array('id' => $userId2), array('_format' => 'json'));

		$positions = $this->forward('PouceTeamBundle:Position:getPositions', array('id' => $team->getId()), array('_format' => 'json'));

		$edition = $this->forward('PouceSiteBundle:Edition:getEdition', array('id' => $team->getEdition()->getId()), array('_format' => 'json'));

		return array(
			'id'		=> $team->getId(),
			'name'		=> $team->getTeamName(),
			'user 1' 	=> json_decode($user1->getContent(), true),
			'user 2' 	=> json_decode($user2->getContent(), true),
			'edition'	=> json_decode($edition->getContent(), true),
			'positions' => json_decode($positions->getContent(), true)
		);
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a team with the id of a user",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the user"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/users/{id}/teams/last")
	 */
	public function getUserLastTeamAction($id){
		
		$team = $this->getDoctrine()->getRepository('PouceTeamBundle:Team')->getLastTeam($id)->getSingleResult();

		if(!is_object($team)){
			throw $this->createNotFoundException();
		}

		$team = $this->forward('PouceTeamBundle:Team:getTeam', array('id' => $team->getId()), array('_format' => 'json'));

		return json_decode($team->getContent(), true);
	}
}
