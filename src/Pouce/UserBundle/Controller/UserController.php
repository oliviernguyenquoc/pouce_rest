<?php

namespace Pouce\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends Controller
{
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get informations on a User with the id",
	 *   requirements={
	 *      {
	 *          "name"="id",
	 *          "dataType"="integer",
	 *          "requirement"="\d+",
	 *          "description"="id of the user"
	 *      }
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

		return array(
			'id'			=> $user->getId(),
			'first_name' 	=> $user->getFirstName(),
			'last_name' 	=> $user->getLastName(),
			'sex'			=> $user->getSex(),
			'promotion'		=> $user->getPromotion(),
			'telephone' 	=> $user->getTelephone(),
			'school' 		=> array(
				'id'		=> $user->getSchool()->getId(),
				'name' 		=> $user->getSchool()->getName(),
				'sigle'		=> $user->getSchool()->getSigle(),
				'address'	=> $user->getSchool()->getAddress(),
				'city'		=> $user->getSchool()->getCity()->getName(),
				'location'	=> array(
					'lat'		=> $user->getSchool()->getCity()->getLatitude(),
					'long'		=> $user->getSchool()->getCity()->getLongitude()
					)
			)
		);
	}
}
