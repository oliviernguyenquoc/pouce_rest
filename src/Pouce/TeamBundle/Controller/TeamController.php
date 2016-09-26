<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\NoResultException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;

use Pouce\UserBundle\Entity\User;
use Pouce\TeamBundle\Entity\Team;
use Pouce\TeamBundle\Form\TeamType;

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
		//$user = $this->get('security.token_storage')->getToken()->getUser();

		//Get users and throw Exception if not exist
		$user_email_1 = $request->request->get("userEmail1");
		$user_email_2 = $request->request->get("userEmail2");

		$em = $this->getDoctrine()->getManager();

		$user1 = $em -> getRepository('PouceUserBundle:User')->findOneByEmail($user_email_1);
		$user2 = $em -> getRepository('PouceUserBundle:User')->findOneByEmail($user_email_2);

		//Check if users exists
		if($user1 == null)
		{
			throw new HttpException(400, "There is no user with email : ".$user_email_1);
		}
		elseif($user2 == null)
		{
			throw new HttpException(400, "There is no user with email : ".$user_email_2);
		}

		//Check if someone have already a team for this edition
		$editionId = $request->request->get("editionId");
		
		$hasATeam = $this->isUsersRegisteredInThisEdition($user1,$user2,$editionId);

		if($hasATeam)
		{
			throw new HttpException(400, "One or two user(s) has already a team");
		}

		//Vérifie qu'il n'y a pas 2 filles dans le même binome
		if($user1->getSex()=='Femme' && $user2->getSex()=='Femme')
		{
			throw new HttpException(400, 'Vous ne pouvez pas inscrire 2 filles dans le même binôme');
		}

		// On crée un objet Team
		$team = new Team();

		// On crée le FormBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(TeamType::class, $team);

		$form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

		if ($form->isValid()) {

			$team->addUser($user1);
			$team->addUser($user2);
			$team->setFinishRegister(false);

			$em = $this->getDoctrine()->getManager();
			$edition = $em->getRepository('PouceSiteBundle:Edition')->find($editionId);

			$team->setEdition($edition);

			$em = $this->getDoctrine()->getManager();
			$em->persist($team);
			$em->merge($user1);
			$em->merge($user2);
			$em->flush();

			$response = new Response("Team created.", 201);               
            return $response;
		}
		else {
            return $form;
        }

	}

	/**
	* Check if the user1 and user2 is register in a edition
	*
	* @param User $user1, User $user2, int $editionId
	* @return bool
	*
	*/
	private function isUsersRegisteredInThisEdition(User $user1, User $user2, $editionId)
    {
        $em = $this->getDoctrine()->getManager();
        $is_user1_has_a_team = true;
        $is_user1_has_a_team = true;
        
        try
		{
			$team1 = $em->getRepository('PouceTeamBundle:Team')->findOneTeamByEditionAndUsers($editionId, $user1->getId())->getSingleResult();
		}
		catch(NoResultException $e) 
		{
			$is_user1_has_a_team = false;
		}

		try
		{
			$team2 = $em->getRepository('PouceTeamBundle:Team')->findOneTeamByEditionAndUsers($editionId, $user2->getId())->getSingleResult();
		}
		catch(NoResultException $e) 
		{
			$is_user2_has_a_team = false;
		}

		if($is_user1_has_a_team == true or $is_user2_has_a_team == true)
		{
			$response = true;
		}
		else
		{
			$response = false;
		}

        return $response;
    }

    /**
     * @ApiDoc(
	 *   resource = true,
	 *   description = "Delete a Team",
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
	 * DELETE Route annotation
	 * @Delete("/teams/{id}")
     */
    public function removeTeamAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('PouceTeamBundle:Team')
        				->find($request->get('id'));

        $em->remove($team);
        $em->flush();
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
