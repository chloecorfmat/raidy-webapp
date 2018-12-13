<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use OrganizerBundle\Security\RaidVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerHelpersController extends AjaxAPIController
{
    /**
     * @Route("/editor/raid/{raidId}/helper/{helperId}", name="patchHelperToPoi", methods={"PATCH"})
     *
     * @param Request $request
     * @param int     $raidId   raid id
     * @param int     $helperId helper id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchHelperToPoi(Request $request, $raidId, $helperId)
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
        $helperService = $this->container->get('HelperService');

        if (!$helperService->checkDataAffectationArray($data, false)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Every fields must be filled');
        }

        $helperManager = $em->getRepository('AppBundle:Helper');
        $helper = $helperManager->find($helperId);

        if (null != $helper) {
            $helper = $helperService->updateHelperToPoiFromArray($helper, $raid->getId(), $data);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'This helper does not exist');
        }

        return new Response($helperService->helperToJson($helper));
    }

    /**
     * @Route("/organizer/raid/helpers/{id}", name="listHelpers")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helpersList(Request $request, $id)
    {
        $manager = $this->getDoctrine()
            ->getManager();

        $helperManager = $manager->getRepository('AppBundle:Helper');

        $raidManager = $manager->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('uniqid' => $id));

        $helpers = $helperManager->findBy(
            [
            'raid' => $raid->getId(),
            ]
        );

        $poiManager = $manager->getRepository('AppBundle:Poi');
        $pois = $poiManager->findBy(['raid' => $raid->getId()]);

        return $this->render(
            'OrganizerBundle:Helpers:helpers.html.twig', [
            'raid_id' => $id,
            'raidName' => $raid->getName(),
            'helpers' => $helpers,
            'pois' => $pois,
            ]
        );
    }
}
