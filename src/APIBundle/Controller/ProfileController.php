<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/10/18
 * Time: 13:58
 */

namespace APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
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
}
