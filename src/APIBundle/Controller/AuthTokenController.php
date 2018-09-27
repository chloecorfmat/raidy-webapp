<?php
namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use APIBundle\Form\Type\CredentialsType;
use APIBundle\Entity\AuthToken;
use APIBundle\Entity\Credentials;
use FOS\RestBundle\View\View;

class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/api/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);
        $form->submit($request->request->all());

        if (!$form->isValid()) {

            $ret = [];
            $ret["code"] = Response::HTTP_BAD_REQUEST;
            $ret["message"] = "Invalid values";

            return new Response(json_encode($ret));
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->getRepository('AppBundle:User')->findOneByUsername($credentials->getLogin());

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

        return new Response($authToken->getValue());
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Delete("/api/auth-tokens")
     */
    public function deleteAuthTokensAction(Request $request)
    {
        $data = $request->request->all();
        $token = $data['token'];

        $em = $this->get('doctrine.orm.entity_manager');
        $authToken = $em->getRepository('APIBundle:AuthToken')->findOneByValue($token);

        if($authToken != null){
            $em->remove($authToken);
            $em->flush();

            $ret = [];
            $ret["code"] = Response::HTTP_OK;
            $ret["message"] = "deleted";


            return new Response(json_encode($ret));
        }else{
            $ret = [];
            $ret["code"] = Response::HTTP_BAD_REQUEST;
            $ret["message"] = "Unknow token";

            $res = new Response(json_encode($ret));
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $res;
        }
    }

    public function invalidCredentials()
    {
        $ret = [];
        $ret["code"] = Response::HTTP_BAD_REQUEST;
        $ret["message"] = "Bad credentials";

        $res = new Response(json_encode($ret));
        $res->setStatusCode(Response::HTTP_BAD_REQUEST);
        return $res;
    }
}
