<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/10/18
 * Time: 13:58.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AjaxAPIController
{
    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/profile")
     *
     * @return JsonResponse
     */
    public function profileInfo()
    {
        $user = $this->getUser();

        $data = [];
        $data['username'] = $user->getUsername();
        $data['firstname'] = $user->getFirstName();
        $data['lastname'] = $user->getLastName();
        $data['email'] = $user->getEmail();
        $data['phone'] = $user->getPhone();

        return new JsonResponse($data);
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Patch("/api/profile")
     *
     * @return Response
     */
    public function editProfileAction()
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }
}
