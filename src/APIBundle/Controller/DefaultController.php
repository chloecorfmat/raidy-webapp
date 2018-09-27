<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class DefaultController extends Controller
{

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/users")
     * @deprecated Useless and dangerous to expose users - for API test purpose only
     */
    public function indexAction()
    {
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findAll();

        $viewHandler = $this->get('fos_rest.view_handler');

        // Création d'une vue FOSRestBundle
        $view = View::create($users);
        $view->setFormat('json');

        // Gestion de la réponse
        return $viewHandler->handle($view);
    }
}
