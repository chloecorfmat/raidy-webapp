<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Component\Routing\Annotation\Route;
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

            if (null != $helper->getPoi()) {
                /* Send email to helper */
                $message = \Swift_Message::newInstance()
                    ->setSubject('Nouvelle affectation pour le raid ' . $raid->getName())
                    ->setFrom($this->container->getParameter('app.mail.from'))
                    ->setReplyTo($this->container->getParameter('app.mail.reply_to'))
                    ->setTo($helper->getUser()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'OrganizerBundle:Emails:affectation.html.twig',
                            ['helper' => $helper->getUser(), 'raid' => $raid, 'poi' => $helper->getPoi()->getName()]
                        ),
                        'text/html'
                    );

                $this->get('mailer')->send($message);
            }

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
            'OrganizerBundle:Helpers:helpers.html.twig',
            [
            'raid_id' => $id,
            'raidName' => $raid->getName(),
            'helpers' => $helpers,
            'pois' => $pois,
            ]
        );
    }

    /**
     * @Route("/organizer/raid/{raidId}/helper/{helperId}/checkin", name="patchHelperCheckin", methods={"PATCH"})
     *
     * @param Request $request
     * @param int     $raidId   raid id
     * @param int     $helperId helper id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchHelperCheckin(Request $request, $raidId, $helperId)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(['uniqid' => $raidId]);

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $helperManager = $em->getRepository('AppBundle:Helper');
        $helper = $helperManager->find($helperId);

        if (null == $helper) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This helper does not exist');
        }

        $now = new \DateTime('now');

        $diff = $raid->getDate()->diff($now);
        if ($diff->days > 0 || (0 == $diff->invert && $diff->days > 0)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You can not check in for this raid today');
        }

        $helper->setIsCheckedIn(1);
        $helper->setCheckInTime($now);
        $em->flush();

        $ret = [];
        $ret['checkInTime'] = $helper->getCheckInTime();
        $ret['helperId'] = $helper->getId();
        $ret['code'] = Response::HTTP_OK;

        return new Response(json_encode($ret));
    }
}
