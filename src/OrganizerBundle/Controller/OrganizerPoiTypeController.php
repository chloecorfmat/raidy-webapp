<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\PoiType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerPoiTypeController extends AjaxAPIController
{
    /**
     * @Route("/organizer/raid/{raidId}/poitype/add", name="addPoiType")
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPoiType(Request $request, $raidId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raidId = (int) $raidId;

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $formPoiType = new PoiType();

        $form = $this->createFormBuilder($formPoiType)
            ->add('type', TextType::class, ['label' => 'Type de point d\'intérêt'])
            ->add('color', ColorType::class, ['label' => 'Couleur'])
            ->add('submit', SubmitType::class, ['label' => 'Créer un type de point d\'intérêt'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $poiTypeManager = $em->getRepository('AppBundle:PoiType');
            $poiTypeExist = $poiTypeManager->findBy(
                ['type' => $formPoiType->getType(), 'raid' => $formPoiType->getRaid()]
            );
            if (!$poiTypeExist) {
                $poiTypeService = $this->container->get('PoiTypeService');
                $poiType = $poiTypeService->poiTypeFromForm($formPoiType, $raidId);

                $em->persist($poiType);
                $em->flush();

                return $this->redirectToRoute('listPoiType', array('raidId' => $raidId));
            }
            $form->addError(new FormError('Ce type de point d\'intérêt existe déjà.'));
        }

        return $this->render('OrganizerBundle:PoiType:addPoiType.html.twig', [
            'form' => $form->createView(),
            'raidId' => $raidId,
        ]);
    }

    /**
     * @Route("/organizer/raid/{raidId}/poitype/{poiTypeId}/edit", name="displayPoiType")
     *
     * @param Request $request   request
     * @param int     $raidId    raid identifier
     * @param int     $poiTypeId poiType identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayPoiType(Request $request, $raidId, $poiTypeId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        $formPoiType = $poiTypeManager->findOneBy(['id' => $poiTypeId]);

        $poiType = $formPoiType;

        if (null === $poiType) {
            throw $this->createNotFoundException('Ce type de point d\'intérêt n\'existe pas');
        }

        $form = $this->createFormBuilder($formPoiType)
            ->add('type', TextType::class, ['label' => 'Type de point d\'intérêt'])
            ->add('color', ColorType::class, ['label' => 'Couleur'])
            ->add('submit', SubmitType::class, ['label' => 'Créer un type de point d\'intérêt'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $poiTypeManager = $em->getRepository('AppBundle:PoiType');
            $poiTypeExist = $poiTypeManager->findBy(
                ['type' => $formPoiType->getType(), 'raid' => $formPoiType->getRaid()]
            );
            if (!$poiTypeExist || $poiTypeExist->getId() === $formPoiType->getId()) {
                $formPoiType = $form->getData();

                $poiType = $poiTypeManager->findOneBy(['id' => $formPoiType->getId()]);

                $poiTypeService = $this->container->get('PoiTypeService');
                $poiType = $poiTypeService->updatePoiTypeFromForm($poiType, $raidId, $formPoiType);

                $em->persist($poiType);
                $em->flush();

                return $this->redirectToRoute('listPoiType', array('raidId' => $raidId));
            }
        }

        return $this->render('OrganizerBundle:PoiType:poiType.html.twig', [
            'form' => $form->createView(),
            'raidId' => $raidId,
            'poiType' => $poiType,
        ]);
    }

    /**
     * @Route("/organizer/raid/{raidId}/poitype/delete/{poiTypeId}", name="deletePoiType")
     *
     * @param Request $request   request
     * @param mixed   $raidId    raid identifier
     * @param id      $poiTypeId poiType identifier
     *
     * @return Response
     */
    public function deletePoiType(Request $request, $raidId, $poiTypeId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');
        $poiType = $poiTypeManager->find($poiTypeId);

        if (null === $poiType) {
            throw $this->createNotFoundException('Ce type de point d\'intérêt n\'existe pas');
        }

            $em->remove($poiType);
            $em->flush();

        return $this->redirectToRoute('listPoiType', array('raidId' => $raidId));
    }

/**
     * @Route("/organizer/raid/{raidId}/poitype", name="listPoiType")
     *
     * @param mixed $raidId raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPoiType($raidId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $poiTypeManager = $em->getRepository('AppBundle:PoiType');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $poiTypes = $poiTypeManager->findBy([
            'raid' => $raid,
        ]);

        return $this->render(
            'OrganizerBundle:PoiType:listPoiType.html.twig',
            [
                'poiTypes' => $poiTypes,
                'raidId' => $raidId,
                'user' => $user,
            ]
        );
    }

    // TODO API route for Raid Editor
}
