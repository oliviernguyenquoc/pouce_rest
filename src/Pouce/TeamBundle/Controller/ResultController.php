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

		$repositoryPosition = $em->getRepository('PouceTeamBundle:Position');
		$repositoryResult = $em->getRepository('PouceTeamBundle:Result');
		$repositoryUser = $em->getRepository('PouceUserBundle:User');
		$repositoryTeam = $em->getRepository('PouceTeamBundle:Team');


		$team = $repositoryTeam->find($id);
		$edition = $team->getEdition();

		$positions = $repositoryPosition->findAllPositionsByTeam($id);
		$school = $repositoryUser->findAUserOfTeam($team)->getSchool();

		$result = $repositoryResult->findOneByTeam($id);
		$comment = $result->getComment();

		$team = $this->forward('PouceTeamBundle:Team:getTeam', array('id' => $team->getId()), array('_format' => 'json'));

		// // create a converter object and handle the input
		// $converter = new Converter();
		// if($comment!=NULL){
		// 	$html = $converter->toHtml($comment->getBlock());			
		// }
		// else{
		// 	$html = NULL;
		// }

		return  array(
			// 'html'	=> $html,
			'result' 	=> $result,
			// 'edition' => 
			// 	array(
			// 		'id'  => $edition->getId()
			// 	),
			//'positions' => $positions,
			//'school' => $school,
			'team' => $team
		);	
	}

}