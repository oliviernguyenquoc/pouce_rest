<?php

namespace Pouce\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pouce\SiteBundle\Entity\Edition;

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
     *  "country": "France",
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
    public function postCityAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryCity = $em->getRepository('PouceSiteBundle:City');

        //Get request objects
        $editionId = $request->request->get("editionId");
        $cityId = $request->request->get('cityId');

        $city = $repositoryCity->find($cityId);
        $team = $repositoryTeam->find($id);

        //Check if team and city exist
        if(!is_object($city) or !is_object($team)){
            throw $this->createNotFoundException();
        }

        $position = new Position();

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(PositionType::class, $position);

        $form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

        if ($form->isValid()) {

            $position->setTeam($team);
            $position->setCity($city);

            $longArrivee = $ville->getLongitude();
            $latArrivee = $ville->getLatitude();

            //Calcule du trajet
            $trajet = $this->container->get('pouce_team.trajet');

            $startCity = $team->getStartCity();
            $distance = $trajet->calculDistance($startCity->getLongitude(),$startCity->getLatitude(),$longArrivee,$latArrivee);

            $position->setDistance($distance);

            //Enregistrement
            $em->persist($position);
            $em->flush();

            //TODO: Check the result thing
            // if($result==NULL)
            // {
            //  $result = new Result();
            //  $result->setTeam($team);
            //  $result->setPosition($position);
            //  $result->setLateness(0);
            //  $result->setIsValid(false);
            //  $result->setRank(0);
            // }
            // else
            // {
            //  $previousDistance = $result->getPosition()->getDistance();

            //  //On regarde si le record à été battu. Si oui, on enregistre le nouveau record
            //  if($previousDistance < $distance)
            //  {
            //      //S'il est battu on le remplace
            //      $result->setPosition($position);
            //  }
            // }

            $response = new Response("Position created.", 201);               
            return $response;
        }
        else {
            return $form;
        }
    }
}
