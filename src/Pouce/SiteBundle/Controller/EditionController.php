<?php

namespace Pouce\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Pouce\SuperAdminBundle\Form\Type\EditionType;
use Pouce\SuperAdminBundle\Form\Type\EditionEditType;

use Pouce\SiteBundle\Entity\Edition;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

class EditionController extends Controller
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get informations on a edition",
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
     * @Get("/editions/{id}")
     * 
    */
    public function getEditionAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('PouceSiteBundle:Edition')->findOneBy(array('id' => $id));

        return array(
            'id'        => $edition->getId(),
            'date'      => $edition->getDateOfEvent(),
            'status'    => $edition->getStatus(),
        );
    }
}
