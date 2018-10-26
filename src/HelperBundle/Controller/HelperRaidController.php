<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 25/10/2018
 * Time: 22:33
 */

namespace HelperBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelperRaidController extends AjaxAPIController
{
    /**
     * @Route("/api/helper/raid", name="api_helper_raids", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRaids()
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raids = $raidManager->findBy([
            'user' => $user,
        ]);

        $raidService = $this->container->get('RaidService');

        return new Response($raidService->raidsArrayToJson($raids));
    }
}
