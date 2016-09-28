<?php

namespace Pouce\UserBundle\Controller;

//use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\View\View;

class UserController extends Controller
{
	/**
	 * ## Response Example ##
	 *
	 * ```
	 *{
	 *	"email": "oliv.nguyen@hotmail.fr"
	 *	"id": 18
	 *	"first_name": "Olivier"
	 *	"last_name": "Nguyen"
	 *	"sex": "Homme"
	 *	"promotion": "Bac +5"
	 *	"telephone": "0659277852"
	 *	"school": 
	 *	{
	 *		"id": 1
	 *		"name": "Ecole Centrale de Nantes"
	 *		"sigle": "ECN"
	 *		"city":
	 *		{
	 *			"id": 2990969
	 *			"name": "Nantes"
	 *		}
	 *	}
	 *}
	 * ```
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a User with the id",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="User id"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\UserBundle\Entity\User",
     *      "groups"={"user"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no position have been found"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/users/{id}")
	 */
	public function getUserAction($id){
		$user = $this->getDoctrine()->getRepository('PouceUserBundle:User')->findOneBy(array('id' => $id));
		
		if(!is_object($user)){
			throw $this->createNotFoundException();
		}

		$serializer = $this->container->get('serializer');
		$userJSON = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user')));

		return new Response($userJSON,200,['content_type' => 'application/json']);
	}


	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a User with the first name and the last name",
	 *   requirements={
	 *      {
	 *          "name"="first_name",
	 *          "dataType"="string",
	 *          "requirement"="\d+",
	 *          "description"="first_name of the user"
	 *      },
	 *      {
	 *          "name"="last_name",
	 *          "dataType"="string",
	 *          "requirement"="\d+",
	 *          "description"="last_name of the user"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\UserBundle\Entity\User",
     *      "groups"={"user"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no position have been found"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/users/first_name/{first_name}/last_name/{last_name}")
	 */
	public function getUserByFirstNameAndLastNameAction($first_name,$last_name){
		$user = $this->getDoctrine()->getRepository('PouceUserBundle:User')->findOneBy(array('first_name' => $first_name, 'last_name' => $last_name));
		
		if(!is_object($user)){
			throw $this->createNotFoundException();
		}

		$serializer = $this->container->get('serializer');
		$userJSON = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user')));

		return new Response($userJSON,200,['content_type' => 'application/json']);
	}

	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a User with the email",
	 *   requirements={
	 *      {
	 *          "name"="email",
	 *          "dataType"="string",
	 *          "requirement"="\d+",
	 *          "description"="email of the user"
	 *      }
	 *   },
     *   output={
     *      "class"="Pouce\UserBundle\Entity\User",
     *      "groups"={"user"},
     *      "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   },
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when no position have been found"
     *   }
	 * )
	 *
	 * GET Route annotation
	 * @Get("/users/email/{email}.{extension}")
	 */
	public function getUserByEmailAction($email,$extension){
		$user = $this->getDoctrine()->getRepository('PouceUserBundle:User')->findOneBy(array('email' => $email.'.'.$extension));
		$user_json = $this->forward('PouceUserBundle:User:getUser', array('id' => $user->getId()), array('_format' => 'json'));
		if(!is_object($user)){
			throw $this->createNotFoundException();
		}

		$serializer = $this->container->get('serializer');
		$userJSON = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user')));

		return new Response($userJSON,200,['content_type' => 'application/json']);
	}

	/**
     * @ApiDoc(
     *   resource = true,
     *   description = "Create new user",
     *   statusCodes={
     *         201="Returned when successful"
     *   }
     * )
     *
     * POST Route annotation
     * @POST("/users")
     *
     *   Ré-écriture de la fonction register du FOSUserBundle pour pouvoir ajouter l'envoie d'une notification pushbullet
     * 
     */
    public function postUserAction(Request $request)
    {        
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $jsonData = json_decode($request->getContent(), true);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $user->setImageName('default.jpg');

            $userManager->updateUser($user);

            /*******************************************************
								ADD PUSHBULLET MESSAGE
            ********************************************************/

			$message="Email : ".$user->getEmail()." \n"."Ecole : ".$user->getSchool()->getName()."\n"."Id : ".$user->getId();

			$curl = curl_init('https://api.pushbullet.com/v2/pushes');

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer 2ycsRTr0WOlUy6Achxrw1dtOsyZLF3B2']);
			curl_setopt($curl, CURLOPT_POSTFIELDS, [
				"email" => "shigeru94@gmail.com", 
				"type" => "note", 
				"title" => "Une personne s'est inscrite", 
				"body" => $message, 
			]);

			// UN-COMMENT TO BYPASS THE SSL VERIFICATION IF YOU DON'T HAVE THE CERT BUNDLE (NOT RECOMMENDED).
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			//$responsePush = curl_exec($curl);

			/*******************************************************
								END PUSHBULLET MESSAGE
            ********************************************************/

            $response = new Response("User created.", 201);               
            return $response;
        }

        $view = View::create($form, 400);
        return $this->get('fos_rest.view_handler')->handle($view);
    }

	/**
     * @ApiDoc(
	 *   resource = true,
	 *   description = "Delete a User",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the user"
	 *      }
	 *   },
     *   statusCodes={
     *         204="Returned when successful"
     *   }
	 * )
	 *
	 * DELETE Route annotation
	 * @Delete("/users/{id}")
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('PouceUserBundle:User')
        				->find($request->get('id'));

        $em->remove($user);
        $em->flush();
    }
}
