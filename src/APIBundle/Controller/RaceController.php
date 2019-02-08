<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/12/18
 * Time: 11:25
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Race;
use AppBundle\Service\RaceService;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class RaceController extends AjaxAPIController
{
    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/organizer/raid/{raidId}/race")
     *
     * @param Request $request
     * @param int     $raidId
     * @return Response
     */
    public function getRaces(Request $request, $raidId)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raceManager = $em->getRepository('AppBundle:Race');

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
}
