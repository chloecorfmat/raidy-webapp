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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/api/helper/raid/{raidId}/contact")
     *
     * @param Request $request request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getContactAction(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }
}
