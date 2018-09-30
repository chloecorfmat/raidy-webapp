<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 30/09/18
 * Time: 14:26
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrganizerAdminController extends Controller {

    /**
     * @Route("/admin/organizer/new", name="addOrganizer")
     */
    public function addOrganizer(Request $request) {

        $formUser = new User();

        $form = $this->createFormBuilder($formUser)
            ->add('username', TextType::class)
            ->add('phone', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('submit', SubmitType::class, array('label' => 'Ajouter un organisateur'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump($formUser->getPlainPassword());

            $userManager = $this->get('fos_user.user_manager');
            $email_exist = $userManager->findUserByEmail($formUser->getEmail());

            if(!$email_exist) {
                $formUser = $form->getData();

                $user = $userManager->createUser();
                $user->setUsername($formUser->getUsername());
                $user->setLastName("");
                $user->setFirstName("");
                $user->setPhone($formUser->getPhone());
                $user->setEmail($formUser->getEmail());
                $user->setEmailCanonical($formUser->getEmail());
                $user->setEnabled(1);
                $user->setPlainPassword($formUser->getPlainPassword());
                $user->setRoles(['ROLE_ORGANIZER']);

                $userManager->updateUser($user);

                return $this->redirectToRoute('addOrganizer');
            } else {
                $form->addError(new FormError('Un utilisateur avec cette adresse email est déjà enregistré'));
            }

        }

        return $this->render('admin/addOrganizer.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/organizer/edit/{id}", name="editOrganizer")
     */
    public function editOrganizer(Request $request, $id) {

        $userManager = $this->get('fos_user.user_manager');

        $formUser = $userManager->findUserBy(["id" => $id]);

        if($formUser == null){
            throw $this->createNotFoundException('The organizer does not exist');
        }

        $form = $this->createFormBuilder($formUser)
            ->add('username', TextType::class)
            ->add('phone', TextType::class)
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, array('label' => 'Ajouter un organisateur'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $email_exist = $userManager->findUserByEmail($formUser->getEmail());

            if(!$email_exist || $email_exist->getId() == $formUser->getId()){
                $formUser = $form->getData();
                $user = $userManager->findUserBy(array('id'=> $formUser->getId()));
                $user->setUsername($formUser->getUsername());
                $user->setPhone($formUser->getPhone());
                $user->setEmail($formUser->getEmail());

                $userManager->updateUser($user);

                return $this->redirectToRoute('editOrganizer');

            } else {
                $form->addError(new FormError('Un utilisateur avec cette adresse email est déjà enregistré'));
            }
        }

        return $this->render('admin/editOrganizer.html.twig', [
            'form' => $form->createView(),
            'userId' => $id,
        ]);
    }

    /**
     * @Route("/admin/organizer/delete/{id}", name="deleteOrganizer")
     */
    public function deleteOrganizer(Request $request, $id) {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(["id" => $id]);
        $userManager->deleteUser($user);

        //@todo : redirect to the organizer list
        return $this->redirectToRoute('homepage');
    }
}