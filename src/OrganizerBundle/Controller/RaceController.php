<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/12/18
 * Time: 11:25
 */

namespace OrganizerBundle\Controller;


use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RaceController extends Controller
{
    /**
     * @Route("/organizer/raid/{raidId}/race", name="listRace")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOrganizerProfile(Request $request, $raidId)
    {
        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        //$raid = $raidManager->find($raidId);
        $raid = $raidManager->findOneBy(['uniqid' => $raidId]);

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::COLLAB, $raid)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('OrganizerBundle:Race:listRace.html.twig', [
            "raid" => $raid,
        ]);
    }

}