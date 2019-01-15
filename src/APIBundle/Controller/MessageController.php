<?php

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/api/helper/raid/{raidId}/message")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMessageAction(Request $request, $raidId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce raid n\'existe pas');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $helper = $helperManager->findOneBy(['user' => $user, 'raid' => $raid]);

        if (null == $helper) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'Ce bénévolat n\'existe pas');
        }

        $messageManager = $em->getRepository('AppBundle:Message');
        $messages = $messageManager->findByPoitype($raid->getId(), $helper->getPoi()->getPoitype()->getId());

        $messageService = $this->container->get('MessageService');

        return new Response($messageService->messagesToJson($messages));
    }
}
