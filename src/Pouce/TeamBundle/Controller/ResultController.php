<?php

namespace Pouce\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pouce\TeamBundle\Form\ResultType;
use Pouce\TeamBundle\Entity\Result;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

// Add a use statement to be able to use the class
// use Sioen\Converter;

class ResultController extends Controller
{
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get results of a team",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="Team id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\TeamBundle\Entity\Result",
     *      "groups"={"result"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no result have been found"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/teams/{id}/results")
	 * 
	*/
	public function getTeamResultAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$result = $em->getRepository('PouceTeamBundle:Result')->findOneByTeam($id);

		if(!is_object($result)){
			throw $this->createNotFoundException();
		}

		$serializer = $this->container->get('serializer');
		$resultJSON = $serializer->serialize($result, 'json', SerializationContext::create()->setGroups(array('result')));

		return  new Response($resultJSON,200,['content_type' => 'application/json']);
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Add a result",
     *   statusCodes={
     *         201="Returned when successful"
     *   }
	 * )
	 *
	 * POST Route annotation
	 * @Post("/results")
	 */
	public function postResultAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$repositoryTeam = $em->getRepository('PouceTeamBundle:Team');

		$user_email = $request->request->get("userEmail");
		$editionId = $request->request->get("editionId");

		$user = $em -> getRepository('PouceUserBundle:User')->findOneByEmail($user_email);
		$team = $repositoryTeam->findOneTeamByEditionAndUsers($editionId, $user->getId())->getSingleResult();

		$result = new Result();

		// On crée le FormBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(ResultType::class, $result);

		$form->submit($request->request->all()); // Validation des données / adaptation de symfony au format REST

		if ($form->isValid()) {

			//Enregistrement
			$em->persist($result);
			$em->flush();

			$response = new Response("Result created.", 201);               
            return $response;
		}
		else {
            return $form;
        }

	}

}