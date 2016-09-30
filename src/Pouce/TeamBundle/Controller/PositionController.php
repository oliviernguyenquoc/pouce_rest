<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pouce\TeamBundle\Entity\Position;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;

class PositionController extends Controller
{
	/**
	 * ## Response Example ##
	 * 
	 * ```  
	 *{
	 *	"id": 701,
	 *	"distance": 383385,
	 *	"position": 
	 *	{
	 *		"id": 2982652,
	 *		"city": "Rouen",
	 *		"country": "France",
	 *		"latitude": 49.44313,
	 *		"longitude": 1.09932
	 *	}
	 *}
	 * ```
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Position",
	 *   description = "Get informations on the last position of a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\TeamBundle\Entity\Position",
     *      "groups"={"position"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no position have been found"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{id}/positions/last")
	 */
	public function getLastPositionAction($id){

		$em = $this->getDoctrine()->getManager();
		
		$lastPosition = $em->getRepository('PouceTeamBundle:Position')->findLastPosition($id)->getSingleResult();

		if(!is_object($lastPosition)){
			throw $this->createNotFoundException();
		}

		$serializer = $this->container->get('serializer');
		$lastPositionJSON = $serializer->serialize($lastPosition, 'json', SerializationContext::create()->setGroups(array('position')));

		return new Response($lastPositionJSON,200,['content_type' => 'application/json']);
	}

	/**
	 * ### Response example ###
	 * 
	 * ```
	 *{
	 *	"id": 353
	 *	"city":
	 *	{
	 *		"id": 3037656
	 *		"name": "Angers"
	 *		"country":
	 *		{
	 *			"name": "France"
	 *		}
	 *		"longitude": -0.55
	 *		"latitude": 47.46667
	 *	}
	 *	"distance": 87245
	 *	"created": "2015-10-03T11:50:52+0200"
	 *}
	 *{
	 *	"id": 375
	 *	"city":
	 *	{
	 *		"id": 3037656
	 *		"name": "Angers"
	 *		"country":
	 *		{
	 *			"name": "France"
	 *		}
	 *		"longitude": -0.55
	 *		"latitude": 47.46667
	 *	}
	 *	"distance": 87245
	 *	"created": "2015-10-03T12:40:13+0200"
	 *}
	 * ```
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Position",
	 *   description = "Get informations on the all positions of a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\TeamBundle\Entity\Position",
     *      "groups"={"position"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
	 *   statusCodes={
     *         200="Returned when successful"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{id}/positions")
	 */
	public function getPositionsAction($id){
		
		$positions = $this->getDoctrine()->getRepository('PouceTeamBundle:Position')->findAllPositionsByTeam($id);

		$serializer = $this->container->get('serializer');
		$positionsJSON = $serializer->serialize($positions, 'json', SerializationContext::create()->setGroups(array('position')));

		return new Response($positionsJSON,200,['content_type' => 'application/json']);
	}

	/**
	 * ## Response Example ##
	 * 
	 * ```  
	 *{
	 *	"id": 492
	 *	"city":
	 *	{
	 *		"id": 2982652
	 *		"name": "Rouen"
	 *		"country":
	 *		{
	 *			"name": "France"
	 *		}
	 *		"longitude": 1.09932
	 *		"latitude": 49.44313
	 *	}
	 *	"distance": 386330
	 *	"created": "2015-10-03T17:01:51+0200"
	 *}
	 * ```
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Position",
	 *   description = "Get informations on the furthest position of a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\TeamBundle\Entity\Position",
     *      "groups"={"position"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{id}/positions/furthest")
	 */
	public function getFurthestPositionAction($id){

		$em = $this->getDoctrine()->getManager();

		$positionFuthest =  $em->getRepository('PouceTeamBundle:Position')->findFuthestPosition($id)->getSingleResult();

		$serializer = $this->container->get('serializer');
		$positionFuthestJSON = $serializer->serialize($positionFuthest, 'json', SerializationContext::create()->setGroups(array('position')));

		return new Response($positionFuthestJSON,200,['content_type' => 'application/json']);
	}

	/**
	 * ## Input Example ##
	 * 
	 * ```  
	 *{
	 *	'city': 2990969
	 *}
	 * ```
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   section="Position",
	 *   description = "Add a position",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   statusCodes={
     *         201="Returned when successful",
     *         404="Returned when the team is not found"
     *   }
	 * )
	 *
	 * POST Route annotation
	 * @Post("/teams/{id}/positions")
	 */
	public function postPositionAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$repositoryTeam = $em->getRepository('PouceTeamBundle:Team');
		$repositoryCity = $em->getRepository('PouceSiteBundle:City');
		$repositoryResult = $em->getRepository('PouceTeamBundle:Result');

		//Get request objects
		$editionId = $request->request->get("editionId");
		$cityId = $request->request->get('cityId');

		$city = $repositoryCity->find($cityId);
		$team = $repositoryTeam->find($id);

		//Check if team and city exist
		if(!is_object($city) or !is_object($team)){
			throw $this->createNotFoundException();
		}

		$position = new Position();

		// On crée le FormBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(PositionType::class, $position);

		$form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

		if ($form->isValid()) {

			$position->setTeam($team);
			$position->setCity($city);

			$longArrivee = $ville->getLongitude();
			$latArrivee = $ville->getLatitude();

			//Calcule du trajet
			$trajet = $this->container->get('pouce_team.trajet');

			$startCity = $team->getStartCity();
			$distance = $trajet->calculDistance($startCity->getLongitude(),$startCity->getLatitude(),$longArrivee,$latArrivee);

			$position->setDistance($distance);

			//Enregistrement
			$em->persist($position);
			$em->flush();

			//TODO: Check the result thing
			// if($result==NULL)
			// {
			// 	$result = new Result();
			// 	$result->setTeam($team);
			// 	$result->setPosition($position);
			// 	$result->setLateness(0);
			// 	$result->setIsValid(false);
			// 	$result->setRank(0);
			// }
			// else
			// {
			// 	$previousDistance = $result->getPosition()->getDistance();

			// 	//On regarde si le record à été battu. Si oui, on enregistre le nouveau record
			// 	if($previousDistance < $distance)
			// 	{
			// 		//S'il est battu on le remplace
			// 		$result->setPosition($position);
			// 	}
			// }

			$response = new Response("Position created.", 201);               
            return $response;
		}
		else {
            return $form;
        }
	}

	/**
     * ## Input Example ##
     * 
     * ```  
     *{
     *  "created": "2011-06-05 12:15:00"
     *}
     * ```  
     * 
     * @ApiDoc(
     *   resource = true,
     *   section="Position",
     *   description = "Update a position",
     *   requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="yyyy-MM-dd hh:mm:ss",
     *          "description"="Position id"
     *      }
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         400="Returned when position is not found"
     *   }
     * )
     *
     * PUT Route annotation
     * @Put("/position/{id}")
     */
    public function putPositionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $position = $em ->getRepository('PouceTeamBundle:Position')->find($request->get('id'));

        if (empty($position)) {
            throw $this->createNotFoundException();
        }

        $form = $this->get('form.factory')->create(PositionEditType::class, $position);

        $form->submit($request->request->all());

        if($form->isValid()){
            //On enregistre la position
            $em->persist($position);
            $em->flush();

            $response = new Response("Position modified.", 200);  
            return $response;
        }
        else
        {
            return $form;
        }
    }

	/**
     * @ApiDoc(
	 *   resource = true,
	 *   section="Position",
	 *   description = "Delete a position",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Position id"
	 *      }
	 *   },
     *   statusCodes={
     *         204="Returned when successful"
     *   }
	 * )
	 *
	 * DELETE Route annotation
	 * @Delete("/positions/{id}")
     */
    public function removePositionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $position = $em->getRepository('PouceTeamBundle:Position')
        				->find($request->get('id'));

        $em->remove($position);
        $em->flush();

        $response = new Response("Position deleted.", 204);               
        return $response;
    }
}
