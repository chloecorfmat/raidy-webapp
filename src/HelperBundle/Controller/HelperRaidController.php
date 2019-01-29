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

namespace HelperBundle\Controller;

use AppBundle\Entity\Helper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class HelperRaidController extends Controller
{
    /**
     * @Route("/helper/raid/{id}", name="displayHelperRaid")
     *
     * @param Request $request request
     * @param int     $id      raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayHelperRaid(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $user = $this->getUser();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $helper = $helperManager->findOneBy(['raid' => $raid, 'user' => $user]);

        if (is_null($helper)) {
            throw $this->createAccessDeniedException();
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        $poiTypes = $poiTypeManager->findBy(
            [
            'user' => $raid->getUser(),
            ]
        );

        $choices = [];
        foreach ($poiTypes as $poiType) {
            $choices[$poiType->getType()] = $poiType->getId();
        }

        $defaultData = [];

        $form = $this->createFormBuilder($defaultData)
            ->add(
                'poitype',
                ChoiceType::class,
                [
                'label' => 'Type de poste souhaité pour le bénévolat',
                'choices' => $choices,
                'data' => $helper->getFavoritePoiType()->getId(),
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                'label' => 'Modifier ma préférence',
                'attr' => array('class' => 'btn'),
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $poitype = $em->getRepository('AppBundle:PoiType')->find($formData['poitype']);
            $helper->setFavoritePoiType($poitype);

            $em->persist($helper);
            $em->flush();

            $this->addFlash('success', 'Le type de poste souhaité a bien été mis à jour.');
        }

        $contactManager = $em->getRepository('AppBundle:Contact');
        $contacts = $contactManager->findBy(array('raid' => $raid));

        if (!is_null($helper->getPoi())) {
            $messageManager = $em->getRepository('AppBundle:Message');
            $messages = $messageManager->findByPoitype($raid->getId(), $helper->getPoi()->getPoitype()->getId());
        }

        return $this->render(
            'HelperBundle:Raid:raid.html.twig',
            [
            'raid' => $raid,
            'contacts' => $contacts,
            'form' => $form->createView(),
            'messages' => $messages ?? '',
            ]
        );
    }
}
