<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 05/11/18
 * Time: 11:58
 */

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Collaboration;
use AppBundle\Entity\Raid;
use OrganizerBundle\Security\RaidVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class CollaborationController extends Controller
{

    /*
     *
     *  /organizer/raid/{raidId}/collaborator
     *  /organizer/raid/{raidId}/collaborator/add
     *  /organizer/raid/{raidId}/collaborator/{invitationId}/delete
     *
     *  /collaborator/invite/{invitationId}
     *  /collaborator/invite/success/{invitationId}
     *  /collaborator/join/{invitationId}
     *  /collaborator/register/{invitationId}
     *
     * */

    /**
     * @Route("/organizer/raid/{raidId}/collaborator", name="listCollaborators")
     * @param Request $request
     * @param int     $raidId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCollaborators(Request $request, $raidId)
    {
        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raid = $raidManager->find($raidId);

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::COLLAB, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $collaborationManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Collaboration');

        $formCollab = new Collaboration();

        $form = $this->createFormBuilder($formCollab)
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter un collaborateur'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailVerif = $formCollab->getEmail();
            $raidVerif = $formCollab->getRaid();

            $user = $this->getUser();
            if ($user->getEmail() == $emailVerif) {
                $form->addError(new FormError('Vous ne pouvez pas vous ajouter en tant que collaborateur.'));
            } else {
                $collab = $collaborationManager->findOneBy(['email' => $emailVerif, 'raid' => $raidVerif]);
                if ($collab != null) {
                    $form->addError(new FormError('Une invitation pour cet utilisateur existe déjà'));
                } else {
                    $em = $this->getDoctrine()->getManager();

                    do {
                        $uid = uniqid();
                        $collabExist = $collaborationManager->find($uid);
                    } while ($collabExist != null);

                    $formCollab->setInvitationId($uid);
                    $formCollab->setRaid($raid);
                    $em->persist($formCollab);
                    $em->flush();
                }
            }
        }

        $collaborations = $collaborationManager->findBy(["raid" => $raid]);

        return $this->render('OrganizerBundle:Collaborator:listCollaborator.html.twig', [
                "collaborations" => $collaborations,
                "form" => $form->createView(),
                "raid" => $raid,
        ]);
    }

    /**
     * @Route("/editor/raid/{raidId}/collaborator/{invitationId}/delete", name="deleteCollaborator")
     * @param Request $request
     * @param int     $raidId
     * @param int     $invitationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCollaborator(Request $request, $raidId, $invitationId)
    {
        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raid = $raidManager->find($raidId);

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::COLLAB, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $collaborationManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Collaboration');

        $collaboration = $collaborationManager->findOneBy(["invitationId" => $invitationId]);

        if ($collaboration != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($collaboration);
            $em->flush();
        }

        return $this->redirectToRoute('listCollaborators', ["raidId" => $raidId]);
    }

    /**
     * @Route("/collaborator/invite/{invitationId}", name="inviteCollaborator")
     * @param Request $request
     * @param int     $invitationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteCollaborator(Request $request, $invitationId)
    {
        // Logout user if one user is login.
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('session')->invalidate();
            $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
            $this->get('security.token_storage')->setToken($anonToken);
        }

        $em = $this->getDoctrine()->getManager();
        $collaborationManager = $em->getRepository('AppBundle:Collaboration');
        $collaboration = $collaborationManager->findOneBy(["invitationId" => $invitationId]);

        if (null === $collaboration) {
            throw $this->createNotFoundException('Cette invitation n\'existe pas');
        }

        if (null === $collaboration->getRaid()) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        return $this->render('OrganizerBundle:Collaborator:inviteCollaborator.html.twig', [
            'raid' => $collaboration->getRaid(),
            'collaboration' => $collaboration,
        ]);
    }

    /**
     * @Route("/collaborator/invite/success/{invitationId}", name="registerSuccessCollaborator")
     *
     * @param int $invitationId
     *
     * @return \Symfony\Component\HttpFoundation\Response|template
     */
    public function registerSuccessCollaborator($invitationId)
    {
        // Logout user if one user is login.
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('session')->invalidate();
            $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
            $this->get('security.token_storage')->setToken($anonToken);
        }

        $em = $this->getDoctrine()->getManager();
        $collaborationManager = $em->getRepository('AppBundle:Collaboration');
        $collaboration = $collaborationManager->findOneBy(["invitationId" => $invitationId]);

        if (null === $collaboration) {
            throw $this->createNotFoundException('Cette invitation n\'existe pas');
        }

        $raid = $collaboration->getRaid();

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        return $this->render('OrganizerBundle:Collaborator:registerSuccessCollaborator.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/collaborator/register/{invitationId}", name="registerCollaborator")
     *
     * @param Request $request
     * @param int     $invitationId
     *
     * @return \Symfony\Component\HttpFoundation\Response|template
     */
    public function registerCollaborator(Request $request, $invitationId)
    {
        $em = $this->getDoctrine()->getManager();
        $collaborationManager = $em->getRepository('AppBundle:Collaboration');
        $collaboration = $collaborationManager->findOneBy(["invitationId" => $invitationId]);

        if (null === $collaboration) {
            throw $this->createNotFoundException('Cette invitation n\'existe pas');
        }

        $raid = $collaboration->getRaid();

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('phone', TelType::class, ['label' => 'Numéro de téléphone'])
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
            ->add('plainPassword', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('repeatPassword', PasswordType::class, ['label' => 'Répéter le mot de passe'])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => array('class' => 'btn'),
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $formatService = $this->container->get('FormatService');

            $userManager = $this->get('fos_user.user_manager');
            $phone = $formatService->mobilePhoneNumber($formData['phone']);
            if (!is_null($phone)) {
                $emailExist = $userManager->findUserByEmail($formData['email']);

                if ($formData['plainPassword'] == $formData['repeatPassword']) {
                    if (!$emailExist) {
                        $user = $userManager->createUser();
                        $user->setUsername($formData['email']);
                        $user->setLastName($formData['lastName']);
                        $user->setFirstName($formData['firstName']);
                        $user->setPhone($phone);
                        $user->setEmail($formData['email']);
                        $user->setEmailCanonical($formData['email']);
                        $user->setEnabled(1);
                        $user->setPlainPassword($formData['plainPassword']);
                        $user->addRole('ROLE_COLLABORATOR');

                        $userManager->updateUser($user);

                        $helperManager = $em->getRepository('AppBundle:Helper');
                        $alreadyRegistered = $helperManager->findBy(['raid' => $raid, 'user' => $user]);

                        $collaboration = null;
                        $collaboration = $collaborationManager->findOneBy([
                            'email' => $user->getEmail(),
                            'invitationId' => $invitationId,
                        ]);

                        if ($collaboration == null) {
                            $form->addError(
                                new FormError(
                                    "Vous n'êtes pas invité à rejoindre ce raid avec ce lien d'invitation"
                                )
                            );
                        } else {
                            // Connect the user manually
                            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                            $this->get('security.token_storage')->setToken($token);

                            $this->get('session')->set('_security_main', serialize($token));

                            $event = new InteractiveLoginEvent($request, $token);
                            $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

                            $collaboration->setUser($user);
                            $em->flush();

                            return $this->redirectToRoute('registerSuccessCollaborator', [
                                'invitationId' => $invitationId,
                            ]);
                        }
                    } else {
                        $form->addError(
                            new FormError('Un utilisateur avec cette adresse email est déjà enregistré')
                        );
                    }
                } else {
                    $form->addError(
                        new FormError('Le champ Répéter le mot de passe n\'est pas rempli correctectement')
                    );
                }
            } else {
                $form->addError(
                    new FormError(
                        'Le numéro de téléphone d\'un bénévole doit être un mobile et commencer par 06 ou 07.'
                    )
                );
            }
        }

        return $this->render('OrganizerBundle:Collaborator:registerCollaborator.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/collaborator/join/{invitationId}", name="joinCollaborator")
     *
     * @param Request $request
     * @param int     $invitationId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function joinHelper(Request $request, $invitationId)
    {
        $em = $this->getDoctrine()->getManager();
        $collaborationManager = $em->getRepository('AppBundle:Collaboration');
        $collaboration = $collaborationManager->findOneBy(["invitationId" => $invitationId]);

        if (null === $collaboration) {
            throw $this->createNotFoundException('Cette invitation n\'existe pas');
        }

        $raid = $collaboration->getRaid();

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('submit', SubmitType::class, ['label' => 'Se connecter', 'attr' => array('class' => 'btn')])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if ($this->areNotEmpty($formData)) {
                $userManager = $this->get('fos_user.user_manager');

                $user = $userManager->findUserByEmail($formData['email']);

                //Reject Organizer accounts
                if (!$user) {
                    $form->addError(new FormError('Identifiants invalides'));
                } else {
                    $encoder = $this->get('security.password_encoder');
                    $isPasswordValid = $encoder->isPasswordValid($user, $formData['password']);

                    if (!$isPasswordValid) { // Le mot de passe n'est pas correct
                        $form->addError(new FormError('Identifiants invalides'));
                    } else {
                        if (!$user->hasRole('ROLE_COLLABORATOR')) {
                            $user->addRole('ROLE_COLLABORATOR');
                            $em->flush();
                        }

                        $collaboration = null;
                        $collaboration = $collaborationManager->findOneBy([
                            'email' => $user->getEmail(),
                            'invitationId' => $invitationId,
                        ]);

                        if ($collaboration == null) {
                            $form->addError(
                                new FormError(
                                    "Vous n'êtes pas invité à rejoindre ce raid avec ce lien d'invitation"
                                )
                            );
                        } else {
                            // Connect the user manually
                            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                            $this->get('security.token_storage')->setToken($token);

                            $this->get('session')->set('_security_main', serialize($token));

                            $event = new InteractiveLoginEvent($request, $token);
                            $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

                            $collaboration->setUser($user);
                            $em->flush();

                            return $this->redirectToRoute('registerSuccessCollaborator', [
                                'invitationId' => $invitationId,
                            ]);
                        }
                    }
                }
            } else {
                $form->addError(new FormError('Tous les champs doivent être remplis.'));
            }
        }

        return $this->render('OrganizerBundle:Collaborator:joinCollaborator.html.twig', [
            'raid' => $raid,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param mixed $formdata data from form
     *
     * @return bool
     */
    private function areNotEmpty($formdata)
    {
        foreach ($formdata as $field) {
            if (null == $field) {
                return false;
            }
        }

        return true;
    }
}
