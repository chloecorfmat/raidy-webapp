<?php

namespace LiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use OrganizerBundle\Security\RaidVoter;

class OrganizerRaidController extends Controller
{
    /**
     * @Route("/organizer/raid/{id}/live", name="liveRaid")
     *
     * @param Request $request request
     * @param int     $id      raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function raidLiveAdmin(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'twitterHashtags',
                TextType::class,
                [
                    'required' => false,
                    'data' => $raid->getTwitterHashtags(),
                    'label' => 'Twitter : hashtags',
                    'attr' => [
                        'data-help' => 'Les différents hashtags doivent être séparés par une virgule.',
                    ],
                ]
            )
            ->add(
                'twitterAccounts',
                TextType::class,
                [
                    'required' => false,
                    'data' => $raid->getTwitterAccounts(),
                    'label' => 'Twitter : comptes à suivre',
                    'attr' => [
                        'data-help' => 'Les différents comptes à suivre doivent être séparés par une virgule.',
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Enregistrer',
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formRaid = $form->getData();

            $raid->setTwitterHashtags($formRaid['twitterHashtags']);
            $raid->setTwitterAccounts($formRaid['twitterAccounts']);

            $em->persist($raid);
            $em->flush();

            $this->addFlash('success', 'Les données ont bien été mises à jour.');
        }

        return $this->render('LiveBundle:Organizer:live.html.twig', [
            'raid' => $raid,
            'form' => $form->createView(),
        ]);
    }
}
