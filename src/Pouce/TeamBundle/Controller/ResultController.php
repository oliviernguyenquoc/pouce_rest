<?php

namespace Pouce\TeamBundle\Controller;

use Pouce\TeamBundle\Form\ResultEditType;
use Pouce\TeamBundle\Form\Type\ResultAdminType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\NoResultException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

// Add a use statement to be able to use the class
// use Sioen\Converter;

class ResultController extends Controller
{
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get results of a team",
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
	 * @Get("/teams/{id}/results")
	 * 
	*/
	public function getTeamResultAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$repositoryResult = $em->getRepository('PouceTeamBundle:Result');

		$result = $repositoryResult->findOneByTeam($id);
		// $comment = $result->getComment();

		$team = $this->forward('PouceTeamBundle:Team:getTeam', array('id' => $id), array('_format' => 'json'));

		// // create a converter object and handle the input
		// $converter = new Converter();
		// if($comment!=NULL){
		// 	$html = $converter->toHtml($comment->getBlock());			
		// }
		// else{
		// 	$html = NULL;
		// }

		$positionFuthest = $result->getPosition();

		return  array(
			// 'comment'	=> $html,
			'id' 				=> $result->getId(),
			'lateness'			=> $result->getLateness(),
			'isValid'			=> $result->getIsValid(),
			'nbCar'				=> $result->getNbCar(),
			'avis'				=> $result->getAvis(),
			'furthest position' => array(
				'id' 		=> $positionFuthest->getId(),
				'city' 		=> $positionFuthest->getCity()->getName(),
				'country' 	=> $positionFuthest->getCity()->getCountry()->getName(),
				'latitude'	=> $positionFuthest->getCity()->getLatitude(),
				'longitude'	=> $positionFuthest->getCity()->getLongitude()
			),
			'rank'				=> $result->getRank(),
		);	
	}

}