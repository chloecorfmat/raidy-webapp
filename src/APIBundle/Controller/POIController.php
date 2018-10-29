<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/10/18
 * Time: 10:11.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class POIController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/api/organizer/raid/{raidId}/poi")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPOIAction(Request $request, $raidId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $poiManager = $em->getRepository('AppBundle:Poi');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé pour ce raid.');
        }

        $pois = $poiManager->findBy(array('raid' => $raidId));
        $poiService = $this->container->get('PoiService');

        return new Response($poiService->poisArrayToJson($pois));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/api/helper/raid/{raidId}/poi/user/{userId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getHelperPOIAction(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Put("/api/helper/raid/{raidId}/check-in/user/{userId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     * @param int     $userId  user id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putHelperCheckinAction(Request $request, $raidId, $userId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Put("/api/organizer/raid/{raidId}/poi")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putPOIAction(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Patch("/api/organizer/raid/{raidId}/poi/{poiId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     * @param int     $poiId   poi id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchPOIAction(Request $request, $raidId, $poiId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Delete("/api/organizer/raid/{raidId}/poi/{poiId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePOIAction(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }
}
