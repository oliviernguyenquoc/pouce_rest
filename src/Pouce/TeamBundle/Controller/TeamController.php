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

		$user1 = $this->forward('PouceUserBundle:UserRest:getUser', array('id' => $userId1), array('_format' => 'json'));
		$user2 = $this->forward('PouceUserBundle:UserRest:getUser', array('id' => $userId2), array('_format' => 'json'));

		$positions = $this->forward('PouceTeamBundle:PositionRest:getPositions', array('teamId' => $team->getId()), array('_format' => 'json'));

		return array(
			'id'		=> $team->getId(),
			'name'		=> $team->getTeamName(),
			'user 1' 	=> json_decode($user1->getContent(), true),
			'user 2' 	=> json_decode($user2->getContent(), true),
			'edition'	=> array(
				'date'	=> $team->getEdition()->getDateOfEvent()
			),
			'positions' => json_decode($positions->getContent(), true)
		);
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a team with the id of a user",
	 *   requirements={
	 *      {
	 *          "name"="idUser",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the user"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("users/{idUser}/teams/last")
	 */
	public function getLastTeamIdOfAUserAction($idUser){
		
		$team = $this->getDoctrine()->getRepository('PouceTeamBundle:Team')->getLastTeam($idUser)->getSingleResult();
		

		if(!is_object($team)){
			throw $this->createNotFoundException();
		}

		return array(
			'teamId' 	=> $team->getId()
		);
	}
}
