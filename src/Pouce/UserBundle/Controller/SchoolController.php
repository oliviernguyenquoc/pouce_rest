<?php

namespace Pouce\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SchoolController extends Controller
{
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get all schools in a edition",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the edition"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/edition/{id}/schools")
	 */
	public function getEditionSchoolsAction($id){
		$em = $this->getDoctrine()->getManager();
        $edition = $em ->getRepository('PouceSiteBundle:Edition')->find($id);
    
    	$repositorySchool = $em->getRepository('PouceUserBundle:School');
        $schools = $repositorySchool->findBy([], ['name' => 'ASC']);
        $schoolsEdition = $edition->getSchools();

        return($schoolsEdition);

	}
}
