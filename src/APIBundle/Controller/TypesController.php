<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/10/18
 * Time: 11:09.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TypesController extends AjaxAPIController
{

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/organizer/poitype")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPoiTypesOrganizer(Request $request)
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

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/poitype")
     *
     * @param Request $request
     * @param int     $raidId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPoiTypesHelper(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');

        /*$em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->find($raidId);

        if($raid == null){
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, "Ce raid n'existe pas");
        }

        $raidOwner = $raid->getUser();

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');
        $poiTypes = $poiTypeManager->findBy(["user"=>$raidOwner]);

        $poiTypesService = $this->container->get('PoiTypeService');
        return new Response($poiTypesService->poisArrayToJson($poiTypes));
        */
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/sporttype")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSportTypesOrganizer(Request $request)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $sportTypes = $sportTypeManager->findAll();
        $sportTypesService = $this->container->get('SportTypeService');

        return new Response($sportTypesService->sportTypesArrayToJson($sportTypes));
    }
}
