<?php

namespace Pouce\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Pouce\SuperAdminBundle\Form\Type\EditionType;
use Pouce\SuperAdminBundle\Form\Type\EditionEditType;

use Pouce\SiteBundle\Entity\Edition;

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
     *      }
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

        return array(
            'id'            => $city->getId(),
            'name'          => $city->getName(),
            'country'       => array(
                'id'        => $city->getCountry()->getId(),
                'name'      => $city->getCountry()->getName(),
                'province'  => $city->getCountry()->getProvince()
            ),
            'population'    => $city->getPopulation(),
            'longitude'     => $city->getLongitude(),
            'latitude'      => $city->getLatitude()
        );
    }
}
