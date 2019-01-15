<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 31/12/18
 * Time: 12:51
 */

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\RaceTrack;
use AppBundle\Service\RaceService;
use AppBundle\Service\RaceTrackService;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RaceTrackController extends AjaxAPIController
{
    /**
     * @Route("/race/raid/{raidId}/race/{raceId}/racetrack", name="putRaceTrack", methods={"PUT"})
     *
     * @param Request $request request
     *
     * @param $raidId
     * @param $raceId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putRaceTrack(Request $request, $raidId, $raceId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceManager = $em->getRepository('AppBundle:Race');
        $trackManager = $em->getRepository('AppBundle:Track');

        // Find the user
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));
        $race = $raceManager->find($raceId);

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        if (null == $race) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This race does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $data = $request->request->all();

        $track = $trackManager->find($data['track']);

        /** @var RaceTrackService $raceService */
        $raceTrackService = $this->container->get('RaceTrackService');

        if (!$raceTrackService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Every fields must be filled');
        }

        $raceTrack = $raceTrackService->emptyRaceTrackFromArray($data, $race, $track);
        $em->persist($raceTrack);
        $em->flush();

        return new Response(json_encode($raceTrackService->raceTrackToObj($raceTrack)));
    }

    /**
     * @Route("/race/raid/{raidId}/race/{raceId}/racetrack/{racetrackId}", name="patchRaceTrack", methods={"PATCH"})
     *
     * @param Request $request request
     *
     * @param $raidId
     * @param $racetrackId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchRaceTrack(Request $request, $raidId, $racetrackId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceTrackManager = $em->getRepository('AppBundle:RaceTrack');

        // Find the user
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $data = $request->request->all();
        $raceTrack = $raceTrackManager->find($racetrackId);

        $raceTrack->setOrder($data['order']);

        if (null != $raceTrack) {
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'This track does not exist');
        }

        return parent::buildJSONStatus(Response::HTTP_OK, 'Updated');
    }

    /**
     * @Route("/race/raid/{raidId}/race/{raceId}/racetrack/{racetrackId}", name="deleteRaceTrack", methods={"DELETE"})
     *
     * @param Request $request request
     *
     * @param $raidId
     * @param $racetrackId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRaceTrack(Request $request, $raidId, $racetrackId, $raceId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceTrackManager = $em->getRepository('AppBundle:RaceTrack');

        // Find the user
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $raceTracks = $raceTrackManager->findBy(["race" => $raceId], ["order"=>"ASC"]);


        $afterDeleted = false;

        /** @var RaceTrack $track */
        foreach($raceTracks as $track) {

            if($afterDeleted){
                $oldOrder = $track->getOrder();
                $track->setOrder($oldOrder-1);
            }

            if($racetrackId == $track->getId()){
                $em->remove($track);
                $afterDeleted = true;
            }
        }

        $em->flush();

        return parent::buildJSONStatus(Response::HTTP_OK, 'Deleted');
    }
}