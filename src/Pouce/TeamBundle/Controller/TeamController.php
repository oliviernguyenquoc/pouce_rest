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

use JMS\Serializer\SerializationContext;

use Pouce\UserBundle\Entity\User;
use Pouce\TeamBundle\Entity\Team;
use Pouce\TeamBundle\Form\TeamType;

class TeamController extends Controller
{
	/**
	 * ## Response Example ##
	 * 
	 * ```  
	 *{
	 *	"id": 3,
	 *	"users":
	 *	[
	 *		{
	 *			"id": 43
	 *			"first_name": "Olivier"
	 *			"last_name": "Dupond"
	 *			"sex": "Homme"
	 *			"promotion": "Bac +4"
	 *			"telephone": "0606060606"
	 *		}
	 *		{
	 *			"id": 47
	 *			"first_name": "Olivia"
	 *			"last_name": "Dupond"
	 *			"sex": "Femme"
	 *			"promotion": "Bac +4"
	 *			"telephone": "0606060606"
	 *		}
	 *	],
	 *	"edition": 
	 *	{
	 *		"id": 1
	 *		"date_of_event": "2015-10-03T00:00:00+0200"
	 *		"status": "finished"
	 *	},
	 *	"team_name":"Best team ever",
	 *	"target_destination":"Bruxelles"
	 *}
	 * ```  
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Team",
	 *   description = "Get informations on a team with the id of a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\TeamBundle\Entity\Team",
     *      "groups"={"team"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no position have been found"
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

		$serializer = $this->container->get('serializer');
		$teamJSON = $serializer->serialize($team, 'json', SerializationContext::create()->setGroups(array('team')));

		return new Response($teamJSON,200,['content_type' => 'application/json']);
	}

	/**
	 *  ## Input Example ##
	 * 
	 * ``` 
	 *{
	 *	"teamName":"tryTeam",
	 *	"targetDestination":"tryDestination",
	 *	"comment":"A Try Comment",
	 *	"editionId": 1,
	 *	"userEmail1": "tryteam1@tryteam.com"
	 *	"userEmail2": "tryteam2@tryteam.com",
	 *	"startCity": 2990969
	 *}
	 * ```
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Team",
	 *   description = "Add a team",
     *   statusCodes={
     *         201="Returned when successful",
     *         400="Returned when a user can't be found or doesn't respect constraints"
     *   }
	 * )
	 *
	 * POST Route annotation
	 * @Post("/teams")
	 */
	public function postTeamAction(Request $request)
	{
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
		$startCityResquested = $request->request->get("startCity");

		//Check if startCity exist
		if(!is_null($startCityResquested))
		{
			$startCity = $em -> getRepository('PouceSiteBundle:City')->find($startCityResquested);
			if(is_null($startCity)){
				throw $this->createNotFoundException();
			}
		}
		else
		{
			//TODO: Change for better exception
			throw $this->createNotFoundException();
		}


		//Check if someone have already a team for this edition
-       $editionId = $request->request->get("editionId");

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
	 *   section="Team",
	 *   description = "Delete a Team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   statusCodes={
     *         204="Returned when successful"
     *   }
	 * )
	 *
	 * DELETE Route annotation
	 * @Delete("/teams/{id}")
     */
    public function removeTeamAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('PouceTeamBundle:Team')->find($request->get('id'));

        $em->remove($team);
        $em->flush();
    }

	/**
	 * ## Input Example ##
	 * 
	 * ```  
	 *{
	 *	"teamName": "tryTeamUpdate",
	 *	"targetDestination": "tryDestinationUpdate",
	 *	"comment":"A Try Comment update"
	 *}
	 * ```  
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Team",
	 *   description = "Update a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   statusCodes={
     *         200="Returned when successful"
     *   }
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
            throw $this->createNotFoundException();
        }

		$form = $this->get('form.factory')->create(TeamType::class, $team);

		$form->submit($request->request->all());

		if($form->isValid()){
			//On enregistre la team
			$em->persist($team);
			$em->flush();

			$response = new Response("Team modified.", 200);  
			return $response;
		}
		else
		{
			return $form;
		}
	}

	/**
	 * ## Response Example ##
	 * 
	 * ```  
	 *{
	 *	"id": 3,
	 *	"users":
	 *	[
	 *		{
	 *			"id": 43
	 *			"first_name": "Olivier"
	 *			"last_name": "Dupond"
	 *			"sex": "Homme"
	 *			"promotion": "Bac +4"
	 *			"telephone": "0606060606"
	 *		}
	 *		{
	 *			"id": 47
	 *			"first_name": "Olivia"
	 *			"last_name": "Dupond"
	 *			"sex": "Femme"
	 *			"promotion": "Bac +4"
	 *			"telephone": "0606060606"
	 *		}
	 *	],
	 *	"edition": 
	 *	{
	 *		"id": 1
	 *		"date_of_event": "2015-10-03T00:00:00+0200"
	 *		"status": "finished"
	 *	},
	 *	"team_name":"Best team ever",
	 *	"target_destination":"Bruxelles"
	 *}
	 * ```  
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Team",
	 *   description = "Get informations on a team with the id of a user",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="User id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\TeamBundle\Entity\Team",
     *      "groups"={"team"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no team have been found"
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

		$serializer = $this->container->get('serializer');
		$teamJSON = $serializer->serialize($team, 'json', SerializationContext::create()->setGroups(array('team')));

		return new Response($teamJSON,200,['content_type' => 'application/json']);
	}
}
