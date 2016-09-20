<?php

namespace Pouce\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Post;

class RegistrationController extends BaseController
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Create new user",
     * )
     *
     * POST Route annotation
     * @POST("/users/")
     *
     *   Ré-écriture de la fonction register du FOSUserBundle pour pouvoir ajouter l'envoie d'une notification pushbullet
     *
     * 
     */
    public function registerAction(Request $request)
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

        if ('POST' === $request->getMethod()) {
            $form->bind($jsonData);

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

    			$responsePush = curl_exec($curl);

    			/*******************************************************
    								END PUSHBULLET MESSAGE
                ********************************************************/

                $response = new Response("User created.", 201);               
                return $response;
            }
        }

        $view = View::create($form, 400);
        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
