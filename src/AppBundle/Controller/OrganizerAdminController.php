<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrganizerAdminController extends Controller
{
    /**
     * @Route("/admin/organizer/add", name="addOrganizer")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addOrganizer(Request $request)
    {
        $formUser = new User();

        $form = $this->createFormBuilder($formUser)
            ->add('username', TextType::class, array('label' => 'Nom d\'utilisateur'))
            ->add('phone', TelType::class, array('label' => 'Numéro de téléphone'))
            ->add('email', EmailType::class, array('label' => 'Adresse e-mail'))
            ->add('plainPassword', PasswordType::class, array('label' => 'Mot de passe'))
            ->add('submit', SubmitType::class, array('label' => 'Ajouter un organisateur'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $emailExist = $userManager->findUserByEmail($formUser->getEmail());

            if (!$emailExist) {
                $formUser = $form->getData();

                $user = $userManager->createUser();
                $user->setUsername($formUser->getUsername());
                $user->setLastName('');
                $user->setFirstName('');
                $user->setPhone($formUser->getPhone());
                $user->setEmail($formUser->getEmail());
                $user->setEmailCanonical($formUser->getEmail());
                $user->setEnabled(1);
                $user->setPlainPassword($formUser->getPlainPassword());
                $user->setRoles(['ROLE_ORGANIZER']);

                $userManager->updateUser($user);

                return $this->redirectToRoute('listOrganizer');
            } else {
                $form->addError(new FormError('Un utilisateur avec cette adresse email est déjà enregistré'));
            }
        }

        return $this->render('AppBundle:Admin:addOrganizer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/organizer/edit/{id}", name="editOrganizer")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOrganizer(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $formUser = $userManager->findUserBy(['id' => $id]);

        if (null == $formUser) {
            throw $this->createNotFoundException('The organizer does not exist');
        }

        $form = $this->createFormBuilder($formUser)
            ->add('username', TextType::class, array('label' => 'Nom d\'utilisateur'))
            ->add('phone', TelType::class, array('label' => 'Numéro de téléphone'))
            ->add('email', EmailType::class, array('label' => 'Adresse e-mail'))
            ->add('submit', SubmitType::class, array('label' => 'Editer un organisateur'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailExist = $userManager->findUserByEmail($formUser->getEmail());

            if (!$emailExist || $emailExist->getId() == $formUser->getId()) {
                $formUser = $form->getData();
                $user = $userManager->findUserBy(array('id' => $formUser->getId()));
                $user->setUsername($formUser->getUsername());
                $user->setPhone($formUser->getPhone());
                $user->setEmail($formUser->getEmail());

                $userManager->updateUser($user);
            } else {
                $form->addError(new FormError('Un utilisateur avec cette adresse email est déjà enregistré'));
            }
        }

        return $this->render('AppBundle:Admin:editOrganizer.html.twig', [
            'form' => $form->createView(),
            'username' => $formUser->getUsername() ?? '',
            'userId' => $id,
        ]);
    }

    /**
     * @Route("/admin/organizer/delete/{id}", name="deleteOrganizer")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteOrganizer(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        $userManager->deleteUser($user);

        return $this->redirectToRoute('organizerList');
    }

    /**
     * @Route("/admin/organizer", name="listOrganizer")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listOrganizers()
    {
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role'
            )->setParameter('role', '%"ROLE_ORGANIZER"%');

        $users = $query->getResult();

        return $this->render(
            'AppBundle:Admin:listOrganizer.html.twig',
            [
                'users' => $users,
            ]
        );
    }
}
