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

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (null == $user) {
            return $this->redirectToRoute('fos_user_security_login');
        } elseif ($user->hasRole('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('admin');
        } elseif ($user->hasRole('ROLE_ORGANIZER')) {
            return $this->redirectToRoute('listRaid');
        } elseif ($user->hasRole('ROLE_COLLABORATOR')) {
            return $this->redirectToRoute('listRaid');
        } elseif ($user->hasRole('ROLE_HELPER')) {
            return $this->redirectToRoute('helper');
        }
    }

    /**
     * @Route("/admin/config", name="adminConfig")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminConfig()
    {
        $configs['app.twitter.account'] =
            $this->container->getParameter('app.twitter.account');

        $configs['app.twitter.oauth_access_token'] =
            $this->container->getParameter('app.twitter.oauth_access_token');

        $configs['app.twitter.oauth_access_token_secret'] =
            $this->container->getParameter('app.twitter.oauth_access_token_secret');

        $configs['app.twitter.consumer_key'] =
            $this->container->getParameter('app.twitter.consumer_key');

        $configs['app.twitter.consumer_secret'] =
            $this->container->getParameter('app.twitter.consumer_secret');

        return $this->render('AppBundle:Admin:config.html.twig', compact('configs'));
    }

    /**
     * @Route("/mentions-legales", name="legalNotice")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function legalNotice()
    {
        return $this->render('AppBundle:Admin:legalNotice.html.twig');
    }
}
