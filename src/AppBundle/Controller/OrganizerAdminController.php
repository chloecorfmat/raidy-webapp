<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrganizerAdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin()
    {
        return $this->render('AppBundle:Admin:admin.html.twig');
    }

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

        $form = $this->createFormBuilder($formUser, array('validation_groups' => array('Profile')))
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Nom d\'utilisateur',
                    'attr' => array('maxlength' => 180),
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'attr' => array('maxlength' => 45),
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Nom',
                    'attr' => array('maxlength' => 45),
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => 'Numéro de téléphone',
                    //'attr' => array('maxlength' => 10),
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                'label' => 'Adresse e-mail',
                'attr' => array('maxlength' => 180),
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'attr' => array('maxlength' => 255),
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Ajouter un organisateur'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formatService = $this->container->get('FormatService');
            $userManager = $this->get('fos_user.user_manager');
            $emailExist = $userManager->findUserByEmail($formUser->getEmail());
            $usernameExist = $userManager->findUserByUsername($formUser->getUsername());

            if (!$emailExist) {
                if (!$usernameExist) {
                    $phone = $formatService->telephoneNumber($formUser->getPhone());

                    if (10 === strlen($phone)) {
                        $formUser = $form->getData();

                        $user = $userManager->createUser();
                        $user->setUsername($formUser->getUsername());
                        $user->setLastName($formUser->getLastName());
                        $user->setFirstName($formUser->getFirstName());
                        $user->setPhone($phone);
                        $user->setEmail($formUser->getEmail());
                        $user->setEmailCanonical($formUser->getEmail());
                        $user->setEnabled(1);
                        $user->setPlainPassword($formUser->getPlainPassword());
                        $user->setRoles(['ROLE_ORGANIZER']);

                        $userManager->updateUser($user);

                        $this->addFlash('success', 'L\'organisateur a bien été ajouté.');

                        return $this->redirectToRoute('listOrganizer');
                    } else {
                        $form->addError(new FormError('Un numéro de téléphone doit comporter 10 chiffres'));
                    }
                } else {
                    $form->addError(new FormError('Un utilisateur avec ce nom d\'utilisateur est déjà enregistré'));
                }
            } else {
                $form->addError(new FormError('Un utilisateur avec cette adresse email est déjà enregistré'));
            }
        }

        return $this->render(
            'AppBundle:Admin:addOrganizer.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
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

        if (null === $formUser) {
            throw $this->createNotFoundException('The organizer does not exist');
        }

        $form = $this->createFormBuilder($formUser, array('validation_groups' => array('Profile')))
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Nom d\'utilisateur',
                    'attr' => array('maxlength' => 180),
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'attr' => array('maxlength' => 45),
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Nom',
                    'attr' => array('maxlength' => 45),
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => 'Numéro de téléphone',
                    //'attr' => array('maxlength' => 10),
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse e-mail',
                    'attr' => array('maxlength' => 180),
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Editer un organisateur'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formatService = $this->container->get('FormatService');
            $emailExist = $userManager->findUserByEmail($formUser->getEmail());

            if (!$emailExist || $emailExist->getId() === $formUser->getId()) {
                $formUser = $form->getData();
                $phone = $formatService->telephoneNumber($formUser->getPhone());

                if (10 === strlen($phone)) {
                    $user = $userManager->findUserBy(['id' => $formUser->getId()]);
                    $user->setUsername($formUser->getUsername());
                    $user->setLastName($formUser->getLastName());
                    $user->setFirstName($formUser->getFirstName());
                    $user->setPhone($phone);
                    $user->setEmail($formUser->getEmail());

                    $userManager->updateUser($user);
                    $this->addFlash('success', 'Le profil a bien été modifié.');

                    return $this->redirectToRoute('editOrganizer', ['id' => $id]);
                } else {
                    $form->addError(new FormError('Un numéro de téléphone doit comporter 10 chiffres'));
                }
            } else {
                $form->addError(new FormError('Un utilisateur avec cette adresse email est déjà enregistré'));
            }
        }

        return $this->render(
            'AppBundle:Admin:editOrganizer.html.twig',
            [
            'form' => $form->createView(),
            'username' => $formUser->getUsername() ?? '',
            'userId' => $id,
            ]
        );
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
        // We have to delete raids about this user.
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        $raids = $raidManager->findBy(array('user' => $id));

        foreach ($raids as $raid) {
            $em->remove($raid);
        }

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        $userManager->deleteUser($user);

        $this->addFlash('danger', 'L\'utilisateur a bien été supprimé.');

        return $this->redirectToRoute('listOrganizer');
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
