<?php

namespace HelperBundle\Controller;

use AppBundle\Entity\Helper;
use AppBundle\Entity\PoiType;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class HelperRegisterController extends Controller
{
    /**
     * @Route("/helper/invite/{id}", name="inviteHelper")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response|template
     */
    public function inviteHelper(Request $request, $id)
    {
        // Logout user if one user is login.
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('session')->invalidate();
            $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
            $this->get('security.token_storage')->setToken($anonToken);
        }

        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        return $this->render('HelperBundle:Register:inviteHelper.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/helper/invite/success/{id}", name="registerSuccessHelper")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response|template
     */
    public function registerSuccessHelper(Request $request, $id)
    {
        // Logout user if one user is login.
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('session')->invalidate();
            $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
            $this->get('security.token_storage')->setToken($anonToken);
        }

        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        return $this->render('HelperBundle:Register:registerSuccessHelper.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/helper/register/{id}", name="registerHelper")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response|template
     */
    public function registerHelper(Request $request, $id)
    {
        // Logout user if one user is login.
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('session')->invalidate();
            $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
            $this->get('security.token_storage')->setToken($anonToken);
        }

        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        $poiTypes = $poiTypeManager->findBy([
            'user' => $raid->getUser(),
        ]);

        $choices = [];
        foreach ($poiTypes as $poiType) {
            $choices[$poiType->getType()] = $poiType->getId();
        }

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
            ->add('poitype', ChoiceType::class, [
                'label' => 'Type de poste souhaité pour le bénévolat',
                'choices' => $choices,
            ])
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
            if (!is_null($phone) && strlen($phone) === 10) {
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
                        $user->addRole('ROLE_HELPER');

                        $userManager->updateUser($user);

                        // Connect the user manually
                        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                        $this->get('security.token_storage')->setToken($token);

                        $this->get('session')->set('_security_main', serialize($token));

                        $event = new InteractiveLoginEvent($request, $token);
                        $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

                        $helperManager = $em->getRepository('AppBundle:Helper');
                        $alreadyRegistered = $helperManager->findBy(['raid' => $raid, 'user' => $user]);

                        if ($alreadyRegistered) {
                            return $this->redirectToRoute('helper');
                        } else {
                            $poitype = $em->getRepository('AppBundle:PoiType')->find($formData['poitype']);

                            $helper = new Helper();
                            $helper->setRaid($raid);
                            $helper->setFavoritePoiType($poitype);
                            $helper->setUser($user);
                            $helper->setIsCheckedIn(false);

                            $em->persist($helper);
                            $em->flush();

                            return $this->redirectToRoute('registerSuccessHelper', ['id' => $id]);
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
                        'Le numéro de téléphone d\'un bénévole doit être un mobile et commencer par 06 ou 07. ' .
                        'Il comporte 10 numéros.'
                    )
                );
            }
        }

        return $this->render('HelperBundle:Register:registerHelper.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/helper/join/{id}", name="joinHelper")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function joinHelper(Request $request, $id)
    {
        // Logout user if one user is login.
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('session')->invalidate();
            $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
            $this->get('security.token_storage')->setToken($anonToken);
        }

        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        if (null === $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        $poiTypes = $poiTypeManager->findBy([
            'user' => $raid->getUser(),
        ]);

        $choices = [];
        foreach ($poiTypes as $poiType) {
            $choices[$poiType->getType()] = $poiType->getId();
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
            //->add('poitype', TextType::class, ['label' => 'Type de poste']) // @todo : Use list instead of raw data
            ->add('poitype', ChoiceType::class, [
                'label' => 'Type de poste souhaité pour le bénévolat',
                'choices' => $choices,
            ])
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
                    $form->addError(new FormError('Un compte organisateur existe déjà avec cette adresse email'));

                    $encoder = $this->get('security.password_encoder');
                    $isPasswordValid = $encoder->isPasswordValid($user, $formData['password']);

                    if (!$isPasswordValid) { // Le mot de passe n'est pas correct
                        $form->addError(new FormError('Identifiants invalides'));
                    } else {
                        if (!$user->hasRole('ROLE_HELPER')) {
                            $user->addRole('ROLE_HELPER');
                            $em->flush();
                        }

                        // Connect the user manually
                        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                        $this->get('security.token_storage')->setToken($token);

                        $this->get('session')->set('_security_main', serialize($token));

                        $event = new InteractiveLoginEvent($request, $token);
                        $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

                        $helperManager = $em->getRepository('AppBundle:Helper');
                        $alreadyRegistered = $helperManager->findBy(['raid' => $raid, 'user' => $user]);

                        if ($alreadyRegistered) {
                            return $this->redirectToRoute('helper');
                        } else {
                            $poitype = $em->getRepository('AppBundle:PoiType')->find($formData['poitype']);

                            $helper = new Helper();
                            $helper->setRaid($raid);
                            $helper->setFavoritePoiType($poitype);
                            $helper->setUser($user);
                            $helper->setIsCheckedIn(false);

                            $em->persist($helper);
                            $em->flush();

                            return $this->redirectToRoute('registerSuccessHelper', ['id' => $id]);
                        }
                    }
                }
            } else {
                $form->addError(new FormError('Tous les champs doivent être remplis.'));
            }
        }

        return $this->render('HelperBundle:Register:joinHelper.html.twig', [
            'form' => $form->createView(),
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/helper", name="listRaidHelper")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRaids()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        //$raids = $raidManager->findAll();
        $raids = $raidManager->findBy([
            'user' => $user,
        ]);

        return $this->render(
            'OrganizerBundle:Raid:listRaid.html.twig',
            [
                'raids' => $raids,
                'user' => $user,
            ]
        );
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
