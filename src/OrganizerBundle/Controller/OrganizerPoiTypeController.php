<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\PoiType;
use OrganizerBundle\Security\RaidVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerPoiTypeController extends AjaxAPIController
{
    /**
     * @Route("/organizer/poitype/add", name="addPoiType")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPoiType(Request $request)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $formPoiType = new PoiType();

        $form = $this->createFormBuilder($formPoiType)
            ->add('type', TextType::class, ['label' => 'Type de point d\'intérêt', 'data' => ''])
            ->add('color', ColorType::class, ['label' => 'Couleur'])
            ->add('submit', SubmitType::class, ['label' => 'Créer un type de point d\'intérêt'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $poiTypeManager = $em->getRepository('AppBundle:PoiType');
            $poiTypeExist = $poiTypeManager->findBy(
                ['type' => $formPoiType->getType(), 'user' => $user]
            );
            if (!$poiTypeExist) {
                $poiTypeService = $this->container->get('PoiTypeService');
                $poiType = $poiTypeService->poiTypeFromForm($formPoiType, $user);

                $em->persist($poiType);
                $em->flush();

                $this->addFlash('success', 'Le type de point d\'intérêt a bien été ajouté.');

                return $this->redirectToRoute('listPoiType');
            }
            $form->addError(new FormError('Ce type de point d\'intérêt existe déjà.'));
        }

        return $this->render(
            'OrganizerBundle:PoiType:addPoiType.html.twig',
            [
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/organizer/poitype/{poiTypeId}/edit", name="displayPoiType")
     *
     * @param Request $request   request
     * @param int     $poiTypeId poiType identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayPoiType(Request $request, $poiTypeId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
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
            ->add('submit', SubmitType::class, ['label' => 'Editer le type de point d\'intérêt'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $poiTypeManager = $em->getRepository('AppBundle:PoiType');
            $poiTypeExist = $poiTypeManager->findOneBy(
                ['type' => $formPoiType->getType(), 'user' => $formPoiType->getUser()]
            );

            if (!$poiTypeExist || $poiTypeExist->getId() === $formPoiType->getId()) {
                $formPoiType = $form->getData();

                $poiType = $poiTypeManager->findOneBy(['id' => $formPoiType->getId()]);

                $poiTypeService = $this->container->get('PoiTypeService');
                $poiType = $poiTypeService->updatePoiTypeFromForm($poiType, $user, $formPoiType);

                $em->persist($poiType);
                $em->flush();

                $this->addFlash('success', 'Le type de point d\'intérêt a bien été mis à jour.');

                return $this->redirectToRoute('listPoiType');
            }
        }

        return $this->render(
            'OrganizerBundle:PoiType:poiType.html.twig',
            [
            'form' => $form->createView(),
            'poiType' => $poiType,
            ]
        );
    }

    /**
     * @Route("/organizer/poitype/delete/{poiTypeId}", name="deletePoiType")
     *
     * @param Request $request   request
     * @param id      $poiTypeId poiType identifier
     *
     * @return Response
     */
    public function deletePoiType(Request $request, $poiTypeId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');
        $poiType = $poiTypeManager->find($poiTypeId);

        if (null === $poiType) {
            throw $this->createNotFoundException('Ce type de point d\'intérêt n\'existe pas');
        }

        $em->remove($poiType);
        $em->flush();

        $this->addFlash('success', 'Le type de point d\'intérêt a bien été supprimé.');

        return $this->redirectToRoute('listPoiType');
    }

    /**
     * @Route("/organizer/poitypes", name="listPoiType")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPoiType(Request $request)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $formPoiType = new PoiType();

        $form = $this->createFormBuilder($formPoiType)
            ->add('type', TextType::class, ['label' => 'Type de point d\'intérêt', 'data' => ''])
            ->add('color', ColorType::class, ['label' => 'Couleur'])
            ->add('submit', SubmitType::class, ['label' => 'Créer un type de point d\'intérêt'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $poiTypeManager = $em->getRepository('AppBundle:PoiType');
            $poiTypeExist = $poiTypeManager->findBy(
                ['type' => $formPoiType->getType(), 'user' => $user]
            );
            if (!$poiTypeExist) {
                $poiTypeService = $this->container->get('PoiTypeService');
                $poiType = $poiTypeService->poiTypeFromForm($formPoiType, $user);

                $em->persist($poiType);
                $em->flush();

                $this->addFlash('success', 'Le type de point d\'intérêt a bien été ajouté.');

                return $this->redirectToRoute('listPoiType');
            } else {
                $form->addError(new FormError('Ce type de point d\'intérêt existe déjà.'));
            }
        }

        $poiTypes = $poiTypeManager->findBy(
            [
            'user' => $user,
            ]
        );

        return $this->render(
            'OrganizerBundle:PoiType:listPoiType.html.twig',
            [
                'poiTypes' => $poiTypes,
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    // API route for Raid Editor

    /**
     * @Route("/editor/poitype", name="listPoiTypeAPI", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPoiTypeAPI()
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }
        $poiTypes = $poiTypeManager->findAll();
        $poiTypesService = $this->container->get('PoiTypeService');

        return new Response($poiTypesService->poiTypesArrayToJson($poiTypes));
    }

    /**
     * @Route("/editor/raid/{raidId}/poitype", name="listPoiTypeByRaidAPI", methods={"GET"})
     *
     * @param int $raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPoiTypeByRaidAPI($raidId)
    {
        // Get managers.
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        //$raid = $raidManager->find($raidId);

        $raid = $raidManager->findOneBy(['uniqid' => $raidId]);

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid) && !$authChecker->isGranted(RaidVoter::HELPER, $raid)) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'You are not allowed to access this raid');
        }

        $user = $raid->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $poiTypeManager = $em->getRepository('AppBundle:PoiType');
        $poiTypes = $poiTypeManager->findBy(['user' => $user]);
        $poiTypesService = $this->container->get('PoiTypeService');

        return new Response($poiTypesService->poiTypesArrayToJson($poiTypes));
    }
}
