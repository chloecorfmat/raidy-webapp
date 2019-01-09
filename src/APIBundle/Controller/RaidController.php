<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/10/18
 * Time: 14:57.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RaidController extends AjaxAPIController
{
    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/organizer/raid")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRaidOrganizer(Request $request)
    {
        $user = $this->getUser();

        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raids = $raidManager->findBy(array('user' => $user));
        $raidService = $this->container->get('RaidService');

        return new Response($raidService->raidsArrayToJson($raids));
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/helper/raid")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRaidHelper(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $helpers = $helperManager->findBy(array('user' => $user));

        $raidIds = [];
        foreach ($helpers as $h) {
            $rid = $h->getRaid()->getId();
            $raidIds[] = $rid;
        }

        $raids = $raidManager->findBy(array('id' => $raidIds));
        $raidService = $this->container->get('RaidService');

        return new Response($raidService->raidsArrayToJson($raids));
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/helper/raid/{raidId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRaidHelper(Request $request, $raidId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(['uniqid' => $raidId]);

        $helper = $helperManager->findOneBy(array('user' => $user, 'raid' => $raid->getId()));

        if (null == $helper) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        //$raid = $raidManager->findOneBy(array('id' => $helper->getRaid()->getId()));
        $raidService = $this->container->get('RaidService');

        return new Response($raidService->raidToJson($raid));
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/organizer/raid/{raidId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRaidOrganizer(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }
}
