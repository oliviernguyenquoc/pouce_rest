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
	 *          "name"="teamId",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the team"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{teamId}/positions/last/")
	 */
	public function getLastPositionAction($teamId){

		$em = $this->getDoctrine()->getManager();
		
		$lastPosition = $em->getRepository('PouceTeamBundle:Position')->findLastPosition($teamId)->getSingleResult();

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
			'location'	=> array(
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
	 *          "name"="teamId",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the team"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{teamId}/positions/all/")
	 */
	public function getAllPositionsAction($teamId){
		
		$positions = $this->getDoctrine()->getRepository('PouceTeamBundle:Position')->findAllPositionsByTeam($teamId);

		$result = array();
		foreach ($positions as $key => $position) 
		{
				$result[$key] =
					array(
					'id'		=> $position->getId(),
					'distance'	=> $position->getDistance(),
					'location'	=> array(
						'city' 		=> $position->getCity()->getId(),
						'city' 		=> $position->getCity()->getName(),
						'country' 	=> $position->getCity()->getCountry()->getName(),
						'latitude'	=> $position->getCity()->getLatitude(),
						'longitude'	=> $position->getCity()->getLongitude(),
						'timestamp' => $position->getCreated()
					)
				);
		}


		return array('positions' => $result);

	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on important positions of a team",
	 *   requirements={
	 *      {
	 *          "name"="teamId",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the team"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{teamId}/positions")
	 */
	public function getPositionsAction($teamId){

		$em = $this->getDoctrine()->getManager();
		
		$positions = $em->getRepository('PouceTeamBundle:Position')->findAllPositionsByTeam($teamId);

		$count = count($positions);


		$positionFuthest =  $em->getRepository('PouceTeamBundle:Position')->findFuthestPosition($teamId)->getSingleResult();
		$positionLast = $em->getRepository('PouceTeamBundle:Position')->findLastPosition($teamId)->getSingleResult();

		return array(
			'count' 			=> $count,
			'last_position' 	=> array(
				'position'	=> array(
					'id' 		=> $positionLast->getId(),
					'city' 		=> $positionLast->getCity()->getName(),
					'country' 	=> $positionLast->getCity()->getCountry()->getName(),
					'latitude'	=> $positionLast->getCity()->getLatitude(),
					'longitude'	=> $positionLast->getCity()->getLongitude()
				)
			),
			'furthest_position'	=> array(
				'position'	=> array(
					'id' 		=> $positionFuthest->getId(),
					'city' 		=> $positionFuthest->getCity()->getName(),
					'country' 	=> $positionFuthest->getCity()->getCountry()->getName(),
					'latitude'	=> $positionFuthest->getCity()->getLatitude(),
					'longitude'	=> $positionFuthest->getCity()->getLongitude()
				)
			)
		);

	}
}
