<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/10/18
 * Time: 11:15.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/contact")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getContactAction(Request $request, $raidId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $contactManager = $em->getRepository('AppBundle:Contact');
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $contacts = $contactManager->findBy(array('raid' => $raid->getId()));
        $contactService = $this->container->get('ContactService');

        return new Response($contactService->contactsArrayToJson($contacts));
    }
}
