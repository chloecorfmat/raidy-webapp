<?php

namespace OrganizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use OrganizerBundle\Security\RaidVoter;

class EditorController extends Controller
{
    /**
     * @Route("/organizer/editor/{id}", name="editor")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editorAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(['id' => $id]);

        if ($raid == null) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        $poiTypes = $poiTypeManager->findBy([
            'user' => $raid->getUser(),
        ]);

        $sportManager = $em->getRepository('AppBundle:SportType');
        $sportTypes = $sportManager->findAll();

        return $this->render('OrganizerBundle:Editor:editor.html.twig', [
            'id' => $id,
            'poiTypes' => $poiTypes,
            'sportTypes' => $sportTypes,
        ]);
    }
}
