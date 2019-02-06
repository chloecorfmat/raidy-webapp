<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/10/18
 * Time: 11:15.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTiming;
use AppBundle\Entity\RaceTrack;
use AppBundle\Service\CompetitorService;
use DateTime;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitorController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/competitor")
     * @Rest\Get("/api/organizer/raid/{raidId}/competitor")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCompetitorAction(Request $request, $raidId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $competitors = $competitorManager->findBy(array('raid' => $raid->getId()));
        $competitorService = $this->container->get('CompetitorService');

        return new Response($competitorService->competitorsArrayToJson($competitors));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/race/{raceId}/competitor")
     * @Rest\Get("/api/organizer/raid/{raidId}/race/{raceId}/competitor")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     * @param int     $raceId  race id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCompetitorByRaceAction(Request $request, $raidId, $raceId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceManager = $em->getRepository('AppBundle:Race');

        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $competitors = $competitorManager->findBy(array('race' => $raceId));

        $competitorService = $this->container->get('CompetitorService');

        return new Response($competitorService->competitorsArrayToJson($competitors));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/race/{raceId}/competitor/numbersign/{numberSign}")
     * @Rest\Get("/api/organizer/raid/{raidId}/race/{raceId}/competitor/numbersign/{numberSign}")
     *
     * @param Request $request    request
     * @param int     $raidId     raid id
     * @param int     $raceId     race id
     * @param int     $numberSign Number sign
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCompetitorByNumberSignAction(Request $request, $raidId, $raceId, $numberSign)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $competitor = $competitorManager->findOneBy(array('race' => $raceId, 'numberSign' => $numberSign));

        if ($competitor != null) {
            $competitorService = $this->container->get('CompetitorService');
            return new Response($competitorService->competitorToJson($competitor));
        } 
        
        return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce competitor n\'existe pas');        
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/race/competitor/numbersign/{numberSign}")
     *
     * @param Request $request    request
     * @param int     $raidId     raid id
     * @param int     $numberSign Number sign
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCompetitorByNumberSignActionWithoutRace(Request $request, $raidId, $numberSign)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $competitors = $competitorManager->findBy(array('numberSign' => $numberSign));

        foreach ($competitors as $competitor) {
            $race = $competitor->getRace();

            if (!is_null($race)) {
                if ($race->getRaid() === $raid) {
                    $competitorService = $this->container->get('CompetitorService');

                    return new Response($competitorService->competitorToJson($competitor));
                }
            }
        }

        if (!empty($competitors)) {
            return parent::buildJSONStatus(
                Response::HTTP_NOT_FOUND,
                'Ce participant n\'est pas associé à une épreuve.'
            );
        } else {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce numéro de dossard n\'existe pas');
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/race/{raceId}/competitor/nfcserialid/{nfcserialid}")
     * @Rest\Get("/api/organizer/raid/{raidId}/race/{raceId}/competitor/nfcserialid/{nfcserialid}")
     *
     * @param Request $request     request
     * @param int     $raidId      raid id
     * @param int     $raceId      race id
     * @param int     $nfcserialid nfc badge serial id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCompetitorByNumberNFCSerialId(Request $request, $raidId, $raceId, $nfcserialid)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $competitor = $competitorManager->findOneBy(array('race' => $raceId, 'NFCSerialId' => $nfcserialid));

        if ($competitor != null) {
            $competitorService = $this->container->get('CompetitorService');
            return new Response($competitorService->competitorToJson($competitor));    
        } 
        
        return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce competitor n\'existe pas');
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Patch("/api/helper/raid/{raidId}/race/{raceId}/competitor/{numberSign}")
     * @Rest\Patch("/api/organizer/raid/{raidId}/race/{raceId}/competitor/{numberSign}")
     *
     * @param Request $request    request
     * @param int     $raidId     raid id
     * @param int     $raceId     race id
     * @param int     $numberSign competitor number sign
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setNFCSerialIdAction(Request $request, $raidId, $raceId, $numberSign)
    {
        $data = $request->request->all();

        if (!isset($data['NFCSerialId']) || $data['NFCSerialId'] == null) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Every fields must be filled.');
        }

        $NFCSerialId = $data['NFCSerialId'];

        // Get managers
        $em = $this->getDoctrine()->getManager();
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        /** @var Competitor $competitor */
        $competitor = $competitorManager->findOneBy(array('race' => $raceId, 'numberSign' => $numberSign));

        if ($competitor != null) {
            $competitor->setNFCSerialId($NFCSerialId);
            $em->flush();
            return parent::buildJSONStatus(Response::HTTP_OK, 'Competitor updated');
        } 
        
        return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce competitor n\'existe pas');
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/api/helper/raid/{raidId}/racetiming")
     * @Rest\Put("/api/organizer/raid/{raidId}/racetiming")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRaceTimingAction(Request $request, $raidId)
    {
        /** @var CompetitorService $competitorService */
        $competitorService = $this->container->get('CompetitorService');
        $data = $request->request->all();

        if (!$competitorService->checkRaceTimingData($data)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Every fields must be filled.');
        }

        $NFCSerialId = $data['NFCSerialId'];
        $time = new DateTime($data['time']);
        $poiId = $data['poi_id'];

        // Get managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $competitorManager = $em->getRepository('AppBundle:Competitor');
        /** @var Competitor $competitor */
        $competitor = $competitorManager->findOneBy(["NFCSerialId" => $NFCSerialId]);

        if ($competitor == null) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce competitor n\'existe pas');
        } 
               
        $raceTracksManager = $em->getRepository('AppBundle:RaceTrack');
        $raceTracks = $raceTracksManager->findBy(["race" => $competitor->getRace()], ['order' => 'ASC']);

        $raceCheckpointManager = $em->getRepository('AppBundle:RaceCheckpoint');
        $raceTimingManager = $em->getRepository('AppBundle:RaceTiming');

        /** @var RaceTrack $raceTrack */
        foreach ($raceTracks as $raceTrack) {
            /** @var RaceCheckpoint $raceCheckpoint */
            $raceCheckpoints = $raceCheckpointManager->findBy(
                [
                    "poi" => $poiId,
                    "raceTrack" => $raceTrack,
                ],
                [
                    'order' => 'ASC',
                ]
            );

            foreach ($raceCheckpoints as $raceCheckpoint) {
                $rt = $raceTimingManager->findOneBy(['checkpoint' => $raceCheckpoint, 'competitor' => $competitor]);

                if ($rt == null) {
                    $raceTiming = new RaceTiming();
                    $raceTiming->setCheckpoint($raceCheckpoint);
                    $raceTiming->setCompetitor($competitor);
                    $raceTiming->setTime($time);

                    $em->persist($raceTiming);
                    $em->flush();

                    return parent::buildJSONStatus(Response::HTTP_OK, 'Competitor updated');
                }
            }
        }
        
        return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'No checkpoint for this poi and this competitor');
    }
}
