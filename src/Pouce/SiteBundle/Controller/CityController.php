<?php

namespace Pouce\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pouce\SiteBundle\Entity\Edition;
use Pouce\SiteBundle\Entity\City;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

class CityController extends Controller
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   section="City",
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

    /**
     * ## Input Example ##
     * 
     * ```  
     *{
     *  "city": "Rouen",
     *  "country": "France", // Or "country": "321"
     *  "latitude": 49.44313,
     *  "longitude": 1.09932
     *}
     * ```
     * 
     * @ApiDoc(
     *   resource = true,
     *   section="City",
     *   description = "Add a city",
     *   statusCodes={
     *         201="Returned when successful",
     *   }
     * )
     *
     * POST Route annotation
     * @Post("/cities")
     */
    public function postCityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryCountry = $em->getRepository('PouceSiteBundle:Country');

        //Get request objects
        $countryJSON = $request->request->get('country');

        //Check if id of country is sent or name of country
        if(is_string($countryJSON))
        {
            $country = $repositoryCountry->findByName($countryJSON);
        }
        elseif(is_integer($countryJSON))
        {
            $country = $repositoryCountry->find($countryJSON);
        }
        else
        {
            throw $this->createNotFoundException();
        }
        

        //Check if team and city exist
        if(!is_object($country)){
            throw $this->createNotFoundException();
        }

        $city = new City();

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(CityType::class, $city);

        $form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

        if ($form->isValid()) {

            //Enregistrement
            $em->persist($city);
            $em->flush();

            $response = new Response("City created.", 201);               
            return $response;
        }
        else {
            return $form;
        }
    }
}
