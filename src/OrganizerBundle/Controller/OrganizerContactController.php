<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 13/11/2018
 * Time: 13:52
 */

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Helper;
use FOS\UserBundle\Event\FormEvent;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OrganizerContactController extends Controller
{
    /**
     * @Route("/organizer/raid/{raidId}/contact/{contactId}", name="editContact")
     *
     * @param Request $request   request
     * @param int     $raidId    raid identifier
     * @param int     $contactId contact identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editContact(Request $request, $raidId, $contactId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            throw $this->createNotFoundException('Accès refusé.');
        }

        $contactManager = $em->getRepository('AppBundle:Contact');

        $formContact = $contactManager->findOneBy(['id' => $contactId]);

        $contact = $formContact;

        if (null === $formContact) {
            throw $this->createNotFoundException('Ce contact n\'existe pas');
        }

        $form = $this->createFormBuilder($formContact)
            ->add('role', TextType::class, ['label' => 'Rôle'])
            ->add('phoneNumber', TextType::class, ['label' => 'Téléphone', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Editer le contact'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formatService = $this->container->get('FormatService');
            $phone = $formatService->telephoneNumber($formContact->getPhoneNumber());

            $contactExist = $contactManager->findOneBy([
                'phoneNumber' => $phone,
                'role' => $formContact->getRole(),
                'raid' => $formContact->getRaid(),
            ]);

            if (!$contactExist || $contactExist->getId() === $formContact->getId()) {
                $formContact = $form->getData();

                $contact = $contactManager->findOneBy(['id' => $formContact->getId()]);
                $contact->setRole($formContact->getRole());
                $phone = $formatService->telephoneNumber($phone);
                $contact->setPhoneNumber($phone);
                $contact->setRaid($raid);

                $em->persist($contact);
                $em->flush();

                $this->addFlash('success', 'Le contact a bien été mis à jour.');

                return $this->redirectToRoute('listContacts', array('raid' => $raid, 'raidId' => $raidId));
            }
        }

        return $this->render('OrganizerBundle:Contact:contact.html.twig', [
            'form' => $form->createView(),
            'raid' => $raid,
            'raidId' => $raidId,
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/organizer/raid/{raidId}/contact/delete/{contactId}", name="deleteContact")
     *
     * @param Request $request   request
     * @param int     $raidId    raid identifier
     * @param int     $contactId contact identifier
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContact(Request $request, $raidId, $contactId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createNotFoundException('Accès refusé.');
        }

        $contactManager = $em->getRepository('AppBundle:Contact');
        $contact = $contactManager->find($contactId);

        if (null != $contact) {
            $em->remove($contact);
            $em->flush();
        } else {
            throw $this->createNotFoundException('Ce contact n\'existe pas');
        }

        return $this->redirectToRoute('listContacts', array('raid' => $raid, 'raidId' => $raidId));
    }

    /**
     * @Route("/organizer/raid/{raidId}/contact", name="listContacts")
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listContacts(Request $request, $raidId)
    {
        $em =  $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->find($raidId);

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        // Get the user
        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::COLLAB, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $formContact = new Contact();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $helpers = $helperManager->findBy(array('raid' => $raid));

        $form = $this->createFormBuilder($formContact)
            ->add('role', TextType::class, ['label' => 'Rôle'])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('helper', ChoiceType::class, array(
                'label' => 'Bénévole responsable',
                'required' => false,
                'choices'  => $helpers,
                'choice_label' => function ($helper) {
                    /** @var Helper $helper */
                    $helperName = $helper->getUser()->getFirstName() . ' ' . $helper->getUser()->getLastName();

                    return $helperName;
                },
            ))
            ->add('submit', SubmitType::class, ['label' => 'Créer un contact'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactManager = $em->getRepository('AppBundle:Contact');

            $contactExit = $contactManager->findBy([
                'phoneNumber' => $formContact->getPhoneNumber(),
                'role' => $formContact->getRole(),
                'raid' => $formContact->getRaid(),
            ]);

            if (!$contactExit) {
                if (null != $formContact->getHelper() || null != $formContact->getPhoneNumber()) {
                    $formContact = $form->getData();

                    $contact = new Contact();

                    $contact->setRole($formContact->getRole());
                    $contact->setRaid($raid);
                    if (null != $formContact->getHelper()) {
                        $contact->setHelper($formContact->getHelper());
                        $em->persist($contact);
                        $em->flush();

                        return $this->redirectToRoute('listContacts', array('raidId' => $raidId));
                    } else {
                        $formatService = $this->container->get('FormatService');
                        $phone = $formatService->telephoneNumber($formContact->getPhoneNumber());

                        $contact->setPhoneNumber($phone);

                        $em->persist($contact);
                        $em->flush();

                        return $this->redirectToRoute('listContacts', array('raidId' => $raidId));
                    }
                } else {
                    $form->addError(
                        new FormError('Un contact doit être lié à un bénévole ou avoir un numéro de téléphone.')
                    );
                }
            } else {
                $form->addError(new FormError('Ce contact existe déjà.'));
            }
        }

        $contactManager = $em->getRepository('AppBundle:Contact');
        $contacts = $contactManager->findBy(array('raid' => $raid));

        return $this->render(
            'OrganizerBundle:Contact:listContact.html.twig',
            [
                'form' => $form->createView(),
                'raid' => $raid,
                'contacts' => $contacts,
            ]
        );
    }
}
