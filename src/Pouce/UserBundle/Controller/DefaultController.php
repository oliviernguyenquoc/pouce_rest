<?php

namespace Pouce\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PouceUserBundle:Default:index.html.twig');
    }
}
