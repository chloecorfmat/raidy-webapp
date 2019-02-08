<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/01/19
 * Time: 19:33
 */

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTrack;
use AppBundle\Service\RaceCheckpointService;
use AppBundle\Service\RaceService;
use AppBundle\Service\RaceTrackService;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RaceCheckpointController extends AjaxAPIController
{

    /**
     * @Route("/race/raid/{raidId}/race/{raceId}/racetrack/{raceTrackId}/raceCheckpoint",
     *      name="putRaceCheckpoint", methods={"PUT"})
     *
     * @param Request $request
     * @param int     $raidId
     * @param int     $raceId
     * @param int     $raceTrackId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putRaceCheckpoint(Request $request, $raidId, $raceId, $raceTrackId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceManager = $em->getRepository('AppBundle:Race');
        $raceTrackManager = $em->getRepository('AppBundle:RaceTrack');
        $poiManager = $em->getRepository('AppBundle:Poi');

        // Find the user
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));
        $raceTrack = $raceTrackManager->find($raceTrackId);

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        if (null == $raceTrack) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This race does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(
                Response::HTTP_BAD_REQUEST,
                'You are not allowed to access this raid'
            );
        }

        $data = $request->request->all();

        $poi = $poiManager->find($data['poi']);

        /** @var RaceCheckpointService $raceCheckpointService */
        $raceCheckpointService = $this->container->get('RaceCheckpointService');

        if (!$raceCheckpointService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Every fields must be filled');
        }

        $raceCheckpoint = $raceCheckpointService->raceCheckpointFromArray($data, $raceTrack, $poi);
        $em->persist($raceCheckpoint);
        $em->flush();

        $races = $raceManager->findBy(array('raid' => $raid));

        $raceService = $this->container->get('RaceService');

        return new Response($raceService->racesArrayToJson($races));
    }

    /**
     * @Route("/race/raid/{raidId}/race/{raceId}/racetrack/{racetrackId}/raceCheckpoint/{raceCheckpointId}",
     *     name="patchRaceCheckpoint", methods={"PATCH"})
     *
     * @param Request $request
     * @param int     $raidId
     * @param int     $raceCheckpointId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchRaceCheckpoint(Request $request, $raidId, $raceCheckpointId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceCheckpointManager = $em->getRepository('AppBundle:RaceCheckpoint');
        $raceManager = $em->getRepository('AppBundle:Race');

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

        /** @var RaceTrack $raceTrack */
        $raceCheckpoint = $raceCheckpointManager->find($raceCheckpointId);
        $order = $raceCheckpoint->getOrder();

        if (null != $raceCheckpoint) {
            if ($data['direction'] == "up") {
                if ($order > 0) {
                    /** @var RaceCheckpoint $raceCheckpoint */
                    $prevCp = $raceCheckpointManager->findOneBy(
                        [
                            'order' => $order-1,
                            'raceTrack' => $raceCheckpoint->getRaceTrack(),
                        ]
                    );

                    $raceCheckpoint->setOrder($order-1);
                    $prevCp->setOrder($order);
                }
            } else {
                /** @var RaceTrack $raceTrack */
                $raceTrack = $raceCheckpoint->getRaceTrack();
                $checkpointCount = count($raceTrack->getCheckpoints());
                if ($order < $checkpointCount-1) {
                    /** @var RaceCheckpoint $prevCp */
                    $prevCp = $raceCheckpointManager->findOneBy(
                        [
                            'order' => $order+1,
                            'raceTrack' => $raceCheckpoint->getRaceTrack(),
                        ]
                    );

                    $raceTrack->setOrder($order+1);
                    $prevCp->setOrder($order);
                }
            }
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'This track does not exist');
        }

        $races = $raceManager->findBy(array('raid' => $raid));

        $raceService = $this->container->get('RaceService');

        return new Response($raceService->racesArrayToJson($races));
    }

    /**
     * @Route("/race/raid/{raidId}/race/{raceId}/racetrack/{racetrackId}/racecheckpoint/{raceCheckpointId}", name="deleteRaceCheckpoint", methods={"DELETE"})
     *
     * @param Request $request
     * @param int     $raidId
     * @param int     $racetrackId
     * @param int     $raceId
     * @param int     $raceCheckpointId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRaceCheckpoint(Request $request, $raidId, $racetrackId, $raceId, $raceCheckpointId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceCheckpointManager = $em->getRepository('AppBundle:RaceCheckpoint');
        $raceManager = $em->getRepository('AppBundle:Race');

        // Find the user
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $raceCheckpoints = $raceCheckpointManager->findBy(["raceTrack" => $racetrackId], ["order" => "ASC"]);

        $afterDeleted = false;

        /** @var RaceCheckpoint $checkpoint */
        foreach ($raceCheckpoints as $checkpoint) {
            if ($afterDeleted) {
                $oldOrder = $checkpoint->getOrder();
                $checkpoint->setOrder($oldOrder-1);
            }

            if ($raceCheckpointId == $checkpoint->getId()) {
                $em->remove($checkpoint);
                $afterDeleted = true;
            }
        }

        $em->flush();

        $races = $raceManager->findBy(array('raid' => $raid));

        $raceService = $this->container->get('RaceService');

        return new Response($raceService->racesArrayToJson($races));
    }
}
