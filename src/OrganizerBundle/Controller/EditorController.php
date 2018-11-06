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

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $poiTypes = $poiTypeManager->findBy([
            'user' => $raid->getUser(),
        ]);

        $sportManager = $em->getRepository('AppBundle:SportType');
        $sportTypes = $sportManager->findAll();

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('OrganizerBundle:Editor:editor.html.twig', [
            'id' => $id,
            'poiTypes' => $poiTypes,
            'sportTypes' => $sportTypes,
        ]);
    }
}
