<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 18/10/2018
 * Time: 16:28
 */

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\PoiType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrganizerPoiTypeController extends AjaxAPIController
{
    /**
     * @Route("/organizer/poitype", name="listPoiType", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPoiTypes()
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $poiTypes = $poiTypeManager->findAll();
        $poiTypesService = $this->container->get('PoiTypeService');

        return new Response($poiTypesService->poisArrayToJson($poiTypes));
    }
}
