<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAdminController extends AjaxAPIController
{
    /**
     * @Route("/admin/users", name="listUsers")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function users()
    {
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u'
            );

        $users = $query->getResult();

        return $this->render('AppBundle:Admin:users.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/admin/users/organizer-roles", name="patchOrganizerRoles", methods={"PATCH"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchOrganizerRoles(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $em->getRepository('AppBundle:User');
        $users = $userManager->findAll();

        $data = $request->request->get('data');

        foreach ($users as $user) {
            if (in_array($user->getId(), $data)) {
                // Check if user has organizer roles.
                // If not complete.
                if (!in_array('ROLE_ORGANIZER', $user->getRoles())) {
                    $user->addRole('ROLE_ORGANIZER');
                }
            } else {
                // Check if user has organizer roles.
                // If it is true, remove.
                if (in_array('ROLE_ORGANIZER', $user->getRoles())) {
                    $user->removeRole('ROLE_ORGANIZER');
                }
            }
        }

        $em->flush();

        return new Response();
    }
}
