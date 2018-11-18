<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Poi;
use AppBundle\Entity\Raid;
use OrganizerBundle\Security\RaidVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerPOIController extends AjaxAPIController
{
    /**
     * @Route("/organizer/raid/{raidId}/poi", name="addPoi", methods={"PUT"})
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPoi(Request $request, $raidId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raidId = (int) $raidId;

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $data = $request->request->all();
        $poiService = $this->container->get('PoiService');

        if (!$poiService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Tous les champs doivent être remplis.');
        }

        $poi = $poiService->poiFromArray($data, $raidId);

        $em->persist($poi);
        $em->flush();

        return new Response($poiService->poiToJson($poi));
    }

    /**
     * @Route("/organizer/raid/{raidId}/poi/{poiId}", name="displayPoi", methods={"PATCH"})
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     * @param int     $poiId   poi identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayPoi(Request $request, $raidId, $poiId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $data = $request->request->all();
        $poiService = $this->container->get('PoiService');

        if (!$poiService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Tous les champs doivent être remplis.');
        }

        $poiManager = $em->getRepository('AppBundle:Poi');
        $poi = $poiManager->find($poiId);

        if (null != $poi) {
            $poi = $poiService->updatePoiFromArray($poi, $raidId, $data);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Ce point d\'intérêt n\'existe pas.');
        }

        //Log change on db
        $raid->notifyChange($user, $em);

        return new Response($poiService->poiToJson($poi));
    }

    /**
     * @Route("/organizer/raid/{raidId}/poi/{poiId}", name="deletePoi", methods={"DELETE"})
     *
     * @param Request $request request
     * @param mixed   $raidId  raid identifier
     * @param id      $poiId   poi identifier
     *
     * @return Response
     */
    public function deletePoi(Request $request, $raidId, $poiId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $poiManager = $em->getRepository('AppBundle:Poi');
        $poi = $poiManager->find($poiId);

        if (null != $poi) {
            $em->remove($poi);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Ce point d\'intérêt n\'existe pas.');
        }

        return parent::buildJSONStatus(Response::HTTP_OK, 'Point d\'intérêt supprimé.');
    }

    /**
     * @Route("/organizer/raid/{raidId}/poi", name="listPoi", methods={"GET"})
     *
     * @param mixed $raidId raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPois($raidId)
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

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $pois = $poiManager->findBy(array('raid' => $raidId));
        $poiService = $this->container->get('PoiService');

        return new Response($poiService->poisArrayToJson($pois));
    }
}
