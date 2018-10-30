<?php

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AjaxAPIController
{
    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/profile")
     *
     * @return Response
     */
    public function profileInfo()
    {
        $user = $this->getUser();

        $profileService = $this->container->get('ProfileService');

        return new Response($profileService->profileToJson($user));
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Patch("/api/profile")
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function editProfileAction(Request $request)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if (null != $user) {
            $data = $request->request->all();
            $profileService = $this->container->get('ProfileService');

            $profile = $profileService->updateProfileFromArray($user, $data);
            $em->flush();
        } else {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'This user does not exist');
        }

        return new Response($profileService->profileToJson($profile));
    }
}
