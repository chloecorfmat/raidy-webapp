<?php

namespace HelperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/helper")
     */
    public function indexAction()
    {
        return $this->render('HelperBundle:Default:index.html.twig');
    }
}
