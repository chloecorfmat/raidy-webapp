<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 13/11/18
 * Time: 22:57.
 */

namespace OrganizerBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Controller\AjaxAPIController;
use Symfony\Component\HttpFoundation\Response;

class OrganizerSportTypeController extends AjaxAPIController
{
    /**
     * @Route("/editor/sporttype", name="listsporttypeeditor", methods={"GET"})
     *
     * @return Response
     */
    public function listSportTypeEditor()
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        $sportTypes = $sportTypeManager->findAll();
        $sportTypesService = $this->container->get('SportTypeService');

        return new Response($sportTypesService->sportTypesArrayToJson($sportTypes));
    }
}
