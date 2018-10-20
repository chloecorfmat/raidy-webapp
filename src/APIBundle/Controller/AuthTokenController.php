<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace APIBundle\Controller;

use APIBundle\Entity\AuthToken;
use APIBundle\Entity\Credentials;
use APIBundle\Form\Type\CredentialsType;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/api/auth-tokens")
     *
     * @param Request $request request
     *
     * @return response
     *
     * @throws \Exception
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $ret = [];
            $ret['code'] = Response::HTTP_BAD_REQUEST;
            $ret['message'] = 'Invalid values';

            $res = new Response(json_encode($ret));
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $res;
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->getRepository('AppBundle:User')->findOneByEmail($credentials->getEmail());

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        $ret = [];
        $ret['token'] = $authToken->getValue();
        $ret['code'] = Response::HTTP_OK;

        return new Response(json_encode($ret));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Delete("/api/auth-tokens")
     *
     * @param Request $request request
     *
     * @return response
     */
    public function deleteAuthTokensAction(Request $request)
    {
        $data = $request->request->all();
        $token = $data['token'];

        $em = $this->get('doctrine.orm.entity_manager');
        $authToken = $em->getRepository('APIBundle:AuthToken')->findOneByValue($token);

        if (null !== $authToken) {
            $em->remove($authToken);
            $em->flush();

            $ret = [];
            $ret['code'] = Response::HTTP_OK;
            $ret['message'] = 'deleted';

            return new Response(json_encode($ret));
        }
        $ret = [];
        $ret['code'] = Response::HTTP_BAD_REQUEST;
        $ret['message'] = 'Unknow token';

        $res = new Response(json_encode($ret));
        $res->setStatusCode(Response::HTTP_BAD_REQUEST);

        return $res;
    }

    /**
     * @return response
     */
    public function invalidCredentials()
    {
        $ret = [];
        $ret['code'] = Response::HTTP_BAD_REQUEST;
        $ret['message'] = 'Bad credentials';

        $res = new Response(json_encode($ret));
        $res->setStatusCode(Response::HTTP_BAD_REQUEST);

        return $res;
    }
}
