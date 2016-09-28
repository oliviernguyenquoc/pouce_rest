<?php

namespace Pouce\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pouce\SuperAdminBundle\Form\Type\EditionType;
use Pouce\SuperAdminBundle\Form\Type\EditionEditType;

use Pouce\SiteBundle\Entity\Edition;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

class EditionController extends Controller
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   section="Edition",
     *   description = "Get informations on a edition",
     *   requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the edition"
     *      }
     *   },
     *   output={
     *      "class"="Pouce\SiteBundle\Entity\Edition",
     *      "groups"={"edition"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful"
     *   }
     * )
     *
     * GET Route annotation
     * @Get("/editions/{id}")
     * 
    */
    public function getEditionAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('PouceSiteBundle:Edition')->findOneBy(array('id' => $id));

        $serializer = $this->container->get('serializer');
        $editionJSON = $serializer->serialize($edition, 'json', SerializationContext::create()->setGroups(array('edition')));

        return new Response($editionJSON,200,['content_type' => 'application/json']);
    }
}
