<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OrganizerBundle\Security\RaidVoter;

class RaidLastEditionController extends AjaxAPIController
{
    /**
     * @Route("/editor/raid/{raidId}/lastEdit", name="lastEdit", methods={"GET"})
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lastEdition(Request $request, $raidId)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $lastEdition = $raid->getLastEdition();
        $lastEditor = $raid->getLastEditor();

        $obj = [];
        if (null != $lastEditor && $user->getId() != $lastEditor->getId()) {
            $obj['lastEditor'] = $lastEditor->getFirstName().' '.$lastEditor->getLastName();
        } else {
            $obj['lastEditor'] = false;
        }
        $obj['lastEdition'] = $lastEdition;

        return new Response(json_encode($obj));
    }
}
