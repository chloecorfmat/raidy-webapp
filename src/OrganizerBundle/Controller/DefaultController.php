<?php

namespace OrganizerBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/organizer")
     */
    public function indexAction()
    {
        return $this->render('OrganizerBundle:Default:index.html.twig');
    }
}
