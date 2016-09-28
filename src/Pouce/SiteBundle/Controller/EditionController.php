<?php

namespace Pouce\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pouce\SiteBundle\Entity\Edition;
use Pouce\SiteBundle\Form\EditionType;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;

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

        /**
     * @ApiDoc(
     *   resource = true,
     *   section="Edition",
     *   description = "Get informations on a edition",
     *   requirements={
     *      {
     *          "name"="id",
     *          "dataType"="string",
     *          "requirement"="MM-dd-yyyy",
     *          "description"="Date of the edition"
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
     * @Get("/editions/date/{date}")
     * 
    */
    public function getEditionByDateAction($date)
    {
        $em = $this->getDoctrine()->getManager();

        $datetime = new \DateTime($date);

        $edition = $em->getRepository('PouceSiteBundle:Edition')->findOneBy(array('dateOfEvent' => $datetime));

        $serializer = $this->container->get('serializer');
        $editionJSON = $serializer->serialize($edition, 'json', SerializationContext::create()->setGroups(array('edition')));

        return new Response($editionJSON,200,['content_type' => 'application/json']);
    }

    /**
     * ## Input Example ##
     * 
     * ```  
     *{
     *  "dateOfEvent": "01-01-2015"
     *}
     * ```
     * 
     * @ApiDoc(
     *   resource = true,
     *   section="Edition",
     *   description = "Add a edition",
     *   statusCodes={
     *         201="Returned when successful",
     *         404="Returned when the team is not found"
     *   }
     * )
     *
     * POST Route annotation
     * @Post("/editions")
     */
    public function postEditionAction(Request $request)
    {
        $edition = new Edition();

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(EditionType::class, $edition);

        $form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $edition->setStatus("scheduled");

            //Enregistrement
            $em->persist($edition);
            $em->flush();

            $response = new Response("Edition created.", 201);
            return $response;
        }
        else {
            return $form;
        }
    }

    /**
     * ## Input Example ##
     * 
     * ```  
     *{
     *  "dateOfEvent": "01-01-2015"
     *}
     * ```  
     * 
     * @ApiDoc(
     *   resource = true,
     *   section="Edition",
     *   description = "Update a edition",
     *   requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Edition id"
     *      }
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         400="Returned when edition is not found"
     *   }
     * )
     *
     * PUT Route annotation
     * @Put("/editions/{id}")
     */
    public function putEditionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $edition = $em ->getRepository('PouceSiteBundle:Edition')->find($request->get('id'));

        if (empty($edition)) {
            throw $this->createNotFoundException();
        }

        $form = $this->get('form.factory')->create(EditionType::class, $edition);

        $form->submit($request->request->all());

        if($form->isValid()){
            //On enregistre la edition
            $em->persist($edition);
            $em->flush();

            $response = new Response("Edition modified.", 200);  
            return $response;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   section="Edition",
     *   description = "Delete a edition",
     *   requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Edition id"
     *      }
     *   },
     *   statusCodes={
     *         204="Returned when successful"
     *   }
     * )
     *
     * DELETE Route annotation
     * @Delete("/editions/{id}")
     */
    public function removeEditionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $edition = $em->getRepository('PouceSiteBundle:Edition')
                        ->find($request->get('id'));

        $em->remove($edition);
        $em->flush();

        $response = new Response("Edition deleted.", 204);  
        return $response;
    }
}
