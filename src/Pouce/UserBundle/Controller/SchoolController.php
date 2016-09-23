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
	 * @Get("/editions/{id}/schools")
	 */
	public function getEditionSchoolsAction($id){
		$em = $this->getDoctrine()->getManager();
        $edition = $em ->getRepository('PouceSiteBundle:Edition')->find($id);
    
    	$repositorySchool = $em->getRepository('PouceUserBundle:School');
        $schools = $repositorySchool->findBy([], ['name' => 'ASC']);
        $schoolsEdition = $edition->getSchools();

        return($schoolsEdition);

	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a school",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the school"
	 *      }
	 *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/schools/{id}")
	 */
	public function getSchoolAction($id)
	{
		$em = $this->getDoctrine()->getManager();
        $school = $em->getRepository('PouceUserBundle:School')->find($id);

        $city =  $this->forward('PouceSiteBundle:City:getCity', array('id' => $school->getCity()->getId()), array('_format' => 'json'));

        return array(
        	'id'		=> $school->getId(),
        	'name'		=> $school->getName(),
        	'sigle'		=> $school->getSigle(),
        	'address'	=> $school->getAddress(),
        	'telephone'	=> $school->getTelephone(),
        	'city'		=> json_decode($city->getContent(), true),
        	'updated'	=> $school->getUpdated()
        );

	}

}
