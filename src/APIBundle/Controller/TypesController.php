<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/10/18
 * Time: 11:09
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TypesController extends AjaxAPIController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/api/poitype")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPOITypesAction(Request $request)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, "Not implemented");
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/api/organizer/sporttype")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPOISportTypesAction(Request $request)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, "Not implemented");
    }
}
