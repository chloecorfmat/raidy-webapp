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
            return $this->redirectToRoute('listOrganizer');
        } elseif ($user->hasRole('ROLE_ORGANIZER')) {
            return $this->redirectToRoute('listRaid');
        } elseif ($user->hasRole('ROLE_HELPER')) {
            return $this->redirectToRoute('helper');
        }
    }
}
