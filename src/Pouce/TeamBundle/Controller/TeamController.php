<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;

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
	 *   description = "Add a team",
	 * )
	 *
	 * POST Route annotation
	 * @Post("/teams")
	 */
	public function postTeamAction(Request $request)
	{
		$user = $this->get('security.context')->getToken()->getUser();

		//Check if the user have already a team for one of the next edition
		$teamService = $this->container->get('pouce_team.team');
		$hasATeam = $teamService->isRegisterToNextRaceOfItsSchool($user);

		//On vérifie si la personne a déjà une équipe
		if(!$hasATeam)
		{
			// On crée un objet Team
			$team = new Team();

			//Des variables pour le formType
			$user = $this->getUser();

			$school = $user->getSchool();

			// On crée le FormBuilder grâce au service form factory
			$form = $this->get('form.factory')->create(new TeamType($school,$user), $team);

			$form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

			if ($form->isValid()) {

				//Vérifie qu'il n'y a pas 2 filles dans le mêm binome
				$data = $form->get('users')->getData();

				if($user->getSex()=='Femme' && $data->getSex()=='Femme')
				{
					$request->getSession()->getFlashBag()->add('updateInformations', 'Vous ne pouvez pas inscrire 2 filles dans le même binôme');
					return $this->render('PouceTeamBundle:Team:addTeam.html.twig', array(
					  'teamForm' => $form->createView(),
					  'user' => $user,
					));
				}

				$team->addUser($user);
				$team->setFinishRegister(false);

				$em = $this->getDoctrine()->getManager();
				$edition = $em -> getRepository('PouceSiteBundle:Edition')->findNextEditionByUserSchool($user)->getSingleResult();

				$team->setEdition($edition);

				$em = $this->getDoctrine()->getManager();
				$em->persist($team);
				$em->merge($user);
				$em->flush();

				return $team;
			}
			else {
	            return $form;
	        }
		}
		else {
			throw new HttpException(400, "You have already a team");
		}
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Add a team",
	 * )
	 *
	 * PUT Route annotation
	 * @Put("/teams/{id}")
	 */
	public function putTeamAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$team = $em ->getRepository('PouceTeamBundle:Team')->find($request->get('id'));

		if (empty($team)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

		//Des variables pour le formType
		$user = $this->getUser();
		$school =$user->getSchool();

		$form = $this->get('form.factory')->create(new TeamEditType($school,$user), $team);

		$form->submit($request->request->all());

		if($form->isValid()){
			//On enregistre la team
			$em->persist($team);
			$em->flush();

			return $team;
		}
		else
		{
			return $form;
		}
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
