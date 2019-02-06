<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/12/18
 * Time: 11:25
 */

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Fraud;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceTrack;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Track;
use AppBundle\Service\RaceService;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RaceController extends AjaxAPIController
{
    /**
     * @Route("/organizer/raid/{raidId}/race", name="listRace")
     *
     * @param Request $request
     * @param int     $raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOrganizerProfile(Request $request, $raidId)
    {
        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        //$raid = $raidManager->find($raidId);
        $raid = $raidManager->findOneBy(['uniqid' => $raidId]);

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('OrganizerBundle:Race:listRace.html.twig', [
            "raid" => $raid,
        ]);
    }

    /**
     * @Route("/race/raid/{raidId}/race", name="putRace", methods={"PUT"})
     *
     * @param Request $request
     * @param int     $raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putRace(Request $request, $raidId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
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

        /** @var RaceService $raceService */
        $raceService = $this->container->get('RaceService');

        if (!$raceService->checkDataArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Every fields must be filled');
        }

        $race = $raceService->emptyRaceFromArray($data, $raid);
        $em->persist($race);
        $em->flush();

        $races = $raceManager->findBy(array('raid' => $raid));

        $raceService = $this->container->get('RaceService');

        return new Response($raceService->racesArrayToJson($races));
    }

    /**
     * @Route("/race/raid/{raidId}/race", name="getRaces", methods={"GET"})
     *
     * @param Request $request
     * @param int     $raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRaces(Request $request, $raidId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
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

        $races = $raceManager->findBy(array('raid' => $raid));

        $raceService = $this->container->get('RaceService');

        return new Response($raceService->racesArrayToJson($races));
    }

    /**
     * @Route("/race/raid/{raidId}/race/{raceId}", name="deleteRace", methods={"DELETE"})
     *
     * @param Request $request
     * @param int     $raidId
     * @param int     $raceId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRace(Request $request, $raidId, $raceId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
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

        $race = $raceManager->find($raceId);

        if (null != $race) {
            $em->remove($race);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'This track does not exist');
        }

        $races = $raceManager->findBy(array('raid' => $raid));

        $raceService = $this->container->get('RaceService');

        return new Response($raceService->racesArrayToJson($races));
    }

    /**
     * @Route("/organizer/raid/{raidId}/race/{raceId}/start", name="startRace")
     *
     * @param Request $request
     * @param int     $raidId
     * @param int     $raceId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function startRace(Request $request, $raidId, $raceId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceManager = $em->getRepository('AppBundle:Race');

        /** @var Raid $raid */
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        /** @var Race $race */
        $race = $raceManager->find($raceId);

        /** @var FlashBag $flashbag */
        $flashbag = $this->get('session')->getFlashBag();

        if (null != $race) {
            $race->setStartTime(new \DateTime());
            $em->flush();
            $flashbag->add("success", "La course a été démarrée");
        } else {
            $flashbag->add("error", "Problème dans le démarrage de la course");
        }

        return $this->redirectToRoute('listRace', ['raidId' => $raid->getUniqid()]);
    }

    /**
     * @Route("/organizer/raid/{raidId}/race/{raceId}/stop", name="stopRace")
     *
     * @param Request $request
     * @param int     $raidId
     * @param int     $raceId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function stopRace(Request $request, $raidId, $raceId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceManager = $em->getRepository('AppBundle:Race');
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raceCheckpointManager = $em->getRepository('AppBundle:RaceCheckpoint');
        $raceTimingManager = $em->getRepository('AppBundle:RaceTiming');

        // Find the user
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        /** @var Race $race */
        $race = $raceManager->find($raceId);

        /** @var FlashBag $flashbag */
        $flashbag = $this->get('session')->getFlashBag();

        if (null != $race) {
            $race->setEndTime(new \DateTime());
            $em->flush();
            $flashbag->add("success", "La course a été arrêtée ");

            $competitors = $competitorManager->findBy(["race" => $race->getId()]);

            $tracks = $race->getTracks();
            $checkpoints = [];
            /** @var RaceTrack $track */
            foreach ($tracks as $track) {
                foreach ($track->getCheckpoints() as $checkpoint) {
                    $checkpoints[] = $checkpoint;
                }
            }

            /** @var Competitor $competitor */
            foreach ($competitors as $competitor) {
                foreach ($checkpoints as $checkpoint) {
                    $rt = $raceTimingManager->findOneBy(
                        [
                            "competitor" => $competitor->getId(),
                            "checkpoint" => $checkpoint->getId(),
                        ]
                    );

                    if ($rt == null) {
                        $competitor->setIsFraud(true);

                        $fraud = new Fraud();
                        $fraud->setCompetitor($competitor);
                        $fraud->setCheckpoint($checkpoint);
                        $em->persist($fraud);

                        $em->flush();
                    }
                }
            }
        } else {
            $flashbag->add("error", "Problème dans l'arrêt de la course");
        }

        return $this->redirectToRoute('listRace', ['raidId' => $raid->getUniqid()]);
    }
}
