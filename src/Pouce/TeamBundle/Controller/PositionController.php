<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Pouce\TeamBundle\Entity\Position;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

class PositionController extends Controller
{
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on the last position of a team",
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
	 * @Get("/teams/{id}/positions/last")
	 */
	public function getLastPositionAction($id){

		$em = $this->getDoctrine()->getManager();
		
		$lastPosition = $em->getRepository('PouceTeamBundle:Position')->findLastPosition($id)->getSingleResult();

		if(!is_object($lastPosition)){
			throw $this->createNotFoundException();
		}

		$city = $lastPosition->getCity()->getName();
		$country = $lastPosition->getCity()->getCountry()->getName();
		$latitude = $lastPosition->getCity()->getLatitude();
		$longitude = $lastPosition->getCity()->getLongitude();
		
		return array(
			'id'		=> $lastPosition->getId(),
			'distance'	=> $lastPosition->getDistance(),
			'position'	=> array(
				'id'		=> $lastPosition->getCity()->getId(),
				'city' 		=> $city,
				'country' 	=> $country,
				'latitude'	=> $latitude,
				'longitude'	=> $longitude
			)
		);
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on the all positions of a team",
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
	 * @Get("/teams/{id}/positions")
	 */
	public function getPositionsAction($id){
		
		$positions = $this->getDoctrine()->getRepository('PouceTeamBundle:Position')->findAllPositionsByTeam($id);

		$result = array();
		foreach ($positions as $key => $position) 
		{
			array_push($result,
				array(
					'id'		=> $position->getId(),
					'distance'	=> $position->getDistance(),
					'position'	=> array(
						'id'		=> $position->getId(),
						'city' 		=> $position->getCity()->getId(),
						'city' 		=> $position->getCity()->getName(),
						'country' 	=> $position->getCity()->getCountry()->getName(),
						'latitude'	=> $position->getCity()->getLatitude(),
						'longitude'	=> $position->getCity()->getLongitude(),
						'timestamp' => $position->getCreated()
					)
				)
			);
		}


		return array_values($result);

	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on important positions of a team",
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
	 * @Get("/teams/{id}/positions/important")
	 */
	public function getImportantPositionsAction($id){

		$em = $this->getDoctrine()->getManager();
		
		$positions = $em->getRepository('PouceTeamBundle:Position')->findAllPositionsByTeam($id);

		$count = count($positions);


		$positionFuthest =  $em->getRepository('PouceTeamBundle:Position')->findFuthestPosition($id)->getSingleResult();
		$positionLast = $em->getRepository('PouceTeamBundle:Position')->findLastPosition($id)->getSingleResult();

		return array(
			'count' 			=> $count,
			'last_position' 	=> array(
				'id' 		=> $positionLast->getId(),
				'city' 		=> $positionLast->getCity()->getName(),
				'country' 	=> $positionLast->getCity()->getCountry()->getName(),
				'latitude'	=> $positionLast->getCity()->getLatitude(),
				'longitude'	=> $positionLast->getCity()->getLongitude()
			),
			'furthest_position'	=> array(
				'id' 		=> $positionFuthest->getId(),
				'city' 		=> $positionFuthest->getCity()->getName(),
				'country' 	=> $positionFuthest->getCity()->getCountry()->getName(),
				'latitude'	=> $positionFuthest->getCity()->getLatitude(),
				'longitude'	=> $positionFuthest->getCity()->getLongitude()
			)
		);
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Add a position",
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

			$response = new Response("Result created.", 201);               
            return $response;
		}
		else {
            return $form;
        }
	}

	/**
     * @ApiDoc(
	 *   resource = true,
	 *   description = "Delete a position",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the position"
	 *      }
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
    }
}
