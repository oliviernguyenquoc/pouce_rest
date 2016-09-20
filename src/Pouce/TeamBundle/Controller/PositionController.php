<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

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
}
