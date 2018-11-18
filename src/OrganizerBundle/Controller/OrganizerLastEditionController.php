<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\PoiType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerPoiTypeController extends AjaxAPIController
{
    /**
     * @Route("/organizer/raid/{raidId}/lastedit", name="listTrack", methods={"GET"})
     *
     * @param mixed $raidId raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lastEdition($raidId)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');

        $raid = $raidManager->findOneBy(array('id' => $raidId));
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $lastEdition = $raid->getLastEdition();
        $lastEditor = $raid->getLastEditor();

        $obj = [];
        if ($user->getId() != $lastEditor->getId()) {
            $obj['$lastEditor'] = $lastEditor;
        } else {
            $obj['$lastEditor'] = false;
        }
        $obj['lastEdition'] = $lastEdition;

        return json_encode($obj);
    }
}
