<?php

namespace Pouce\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use JMS\Serializer\SerializationContext;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;

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
	 *   },
     *   output={
     *      "class"="Pouce\UserBundle\Entity\School",
     *      "groups"={"school"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful"
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

        $serializer = $this->container->get('serializer');
        $schoolsEditionJSON = $serializer->serialize($schoolsEdition, 'json', SerializationContext::create()->setGroups(array('school')));

        return new Response($schoolsEditionJSON,200,['content_type' => 'application/json']);

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
	 *   },
     *   output={
     *      "class"="Pouce\UserBundle\Entity\School",
     *      "groups"={"school"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful"
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

		$serializer = $this->container->get('serializer');
        $school = $serializer->serialize($school, 'json', SerializationContext::create()->setGroups(array('school')));

        return new Response($school,200,['content_type' => 'application/json']);

	}

}
