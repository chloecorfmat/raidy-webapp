<?php

namespace OrganizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class OrganizerHelpersController extends Controller
{
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

        $helpers = $helperManager->findBy([
            'raid' => $id,
        ]);

        $poiManager = $manager->getRepository('AppBundle:Poi');
        $pois = $poiManager->findBy(['raid' => $id]);

        return $this->render('OrganizerBundle:Helpers:helpers.html.twig', [
            'helpers' => $helpers,
            'pois' => $pois,
        ]);
    }
}
