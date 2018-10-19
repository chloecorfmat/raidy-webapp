<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Track;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerTrackController extends AjaxAPIController
{
    /**
     * @Route("/organizer/raid/{raidId}/track", name="addTrack", methods={"PUT"})
     *
     * @param Request $request request
     * @param int     $raidId  raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTrack(Request $request, $raidId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, "This raid does not exist");
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "You are not allowed to access this raid");
        }

        $data = $request->request->all();
        $trackService = $this->container->get('TrackService');

        if (!$trackService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "Every fields must be filled");
        }

        $track = $trackService->trackFromArray($data, $raidId);

        $em->persist($track);
        $em->flush();

        return new Response($trackService->trackToJson($track));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/{trackId}", name="editTrack", methods={"PATCH"})
     *
     * @param Request $request request
     * @param int     $raidId  raidId
     * @param int     $trackId track id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editTrack(Request $request, $raidId, $trackId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, "This raid does not exist");
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "You are not allowed to access this raid");
        }

        $data = $request->request->all();
        $trackService = $this->container->get('TrackService');

        if (!$trackService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "Every fields must be filled");
        }

        $trackManager = $em->getRepository('AppBundle:Track');
        $track = $trackManager->find($trackId);

        if ($track != null) {
            $track = $trackService->updateTrackFromArray($track, $raidId, $data);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "This track does not exist");
        }

        return new Response($trackService->trackToJson($track));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track", name="listTrack", methods={"GET"})
     *
     * @param mixed $raidId raidId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTracks($raidId)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');

        $raid = $raidManager->findOneBy(array('id' => $raidId));
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, "This raid does not exist");
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "You are not allowed to access this raid");
        }

        $tracks = $trackManager->findBy(array('raid' => $raidId));
        $trackService = $this->container->get('TrackService');

        return new Response($trackService->tracksArrayToJson($tracks));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/{trackId}", name="deleteTrack", methods={"DELETE"})
     *
     * @param Request $request request
     * @param mixed   $raidId  raidId
     * @param id      $trackId track id
     * @return Response
     */
    public function deleteTrack(Request $request, $raidId, $trackId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, "This raid does not exist");
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "You are not allowed to access this raid");
        }

        $trackManager = $em->getRepository('AppBundle:Track');
        $track = $trackManager->find($trackId);

        if ($track != null) {
            $em->remove($track);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, "This track does not exist");
        }

        return parent::buildJSONStatus(Response::HTTP_OK, "Deleted");
    }
}
