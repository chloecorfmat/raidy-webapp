<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/12/18
 * Time: 11:25
 */

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Service\RaceService;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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

        return new Response($raceService->raceToJson($race));
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

        return parent::buildJSONStatus(Response::HTTP_OK, 'Deleted');
    }
}
