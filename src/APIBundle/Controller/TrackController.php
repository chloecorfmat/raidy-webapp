<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/10/18
 * Time: 14:57
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/api/organizer/raid/{raidId}/track")
     * @Rest\Get("/api/helper/raid/{raidId}/track")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTrackAction(Request $request, $raidId)
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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Put("/api/organizer/raid/{raidId}/track")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTrackAction(Request $request, $raidId)
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
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Patch("/api/organizer/raid/{raidId}/track/{trackId}")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     * @param int     $trackId raid id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editTrackAction(Request $request, $raidId, $trackId)
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
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Delete("/api/organizer/raid/{raidId}/track/{trackId}")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     * @param int     $trackId track id
     * @return \Symfony\Component\HttpFoundation\Response
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
