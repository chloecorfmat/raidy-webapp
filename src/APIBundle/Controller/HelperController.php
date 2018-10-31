<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 31/10/2018
 * Time: 08:16
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelperController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Patch("/api/organizer/raid/{raidId}/helper/{helperId}")
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
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
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
            $helper = $helperService->updateHelperToPoiFromArray($helper, $raidId, $data);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'This helper does not exist');
        }

        return new Response($helperService->helperToJson($helper));
    }
}
