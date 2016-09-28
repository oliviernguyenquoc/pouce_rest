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

class CityController extends Controller
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get informations on a city",
     *   requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the city"
     *      },
     *   },
     *   output={
     *      "class"="Pouce\SiteBundle\Entity\City",
     *      "groups"={"city"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful"
     *   }
     * )
     *
     * GET Route annotation
     * @Get("/cities/{id}")
     * 
    */
    public function getCityAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository('PouceSiteBundle:City')->findOneBy(array('id' => $id));

        $serializer = $this->container->get('serializer');
        $cityJSON = $serializer->serialize($city, 'json', SerializationContext::create()->setGroups(array('position')));

        return new Response($cityJSON,200,['content_type' => 'application/json']);
    }
}
