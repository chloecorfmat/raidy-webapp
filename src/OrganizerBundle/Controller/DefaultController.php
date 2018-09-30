<?php

namespace OrganizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/organizer")
     *
     * @return template
     */
    public function indexAction()
    {
        return $this->render('OrganizerBundle:Default:index.html.twig');
    }
}
