<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 02/11/2018
 * Time: 11:04
 */

namespace AppBundle\Controller;

use AppBundle\Entity\SportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportTypeController extends Controller
{
    /**
     * @Route("/admin/sporttype/add", name="addSportType")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSportType(Request $request)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $formSportType = new SportType();

        $form = $this->createFormBuilder($formSportType)
            ->add('sport', TextType::class, ['label' => 'Sport'])
            ->add('icon', FileType::class, [
                'label' => 'Icone',
                'label_attr' => ['class' => 'form--fixed-label'],
                'attr' => ['class' => 'form-input--image'],
                'data_class' => null,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter un sport'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sportTypeManager = $em->getRepository('AppBundle:SportType');
            $sportTypeExist = $sportTypeManager->findBy(
                ['sport' => $formSportType->getSport()]
            );
            if (!$sportTypeExist) {
                $sportTypeService = $this->container->get('SportTypeService');
                $sportType = $sportTypeService->sportTypeFromForm(
                    $formSportType,
                    $this->getParameter('sporttypes_img_directory')
                );

                $em->persist($sportType);
                $em->flush();

                return $this->redirectToRoute('listSportType');
            }
            $form->addError(new FormError('Ce type de sport existe déjà.'));
        }

        return $this->render('AppBundle:SportType:addSportType.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/sporttype/edit/{sportTypeId}", name="editSportType")
     *
     * @param Request $request     request
     * @param mixed   $sportTypeId sportType id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editSportType(Request $request, $sportTypeId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        $formSportType = $sportTypeManager->findOneBy(['id' => $sportTypeId]);

        $sportType = $formSportType;
        $oldIcon = $formSportType->getIcon();

        if (null === $sportType) {
            throw $this->createNotFoundException('Ce type de sport n\'existe pas');
        }

        $form = $this->createFormBuilder($formSportType)
            ->add('sport', TextType::class, ['label' => 'Sport'])
            ->add('icon', FileType::class, [
                'label_attr' => ['class' => 'form--fixed-label'],
                'label' => 'Icone',
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'data_url' => 'uploads/sporttypes/',
                    'class' => 'form-input--image',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Editer le sport'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sportTypeExist = $sportTypeManager->findOneBy(
                ['sport' => $formSportType->getSport()]
            );

            if (!$sportTypeExist || $sportTypeExist->getId() === $formSportType->getId()) {
                $formSportType = $form->getData();

                $sportType = $sportTypeManager->findOneBy(['id' => $formSportType->getId()]);

                $sportTypeService = $this->container->get('SportTypeService');
                $sportType = $sportTypeService->updateSportTypeFromForm(
                    $sportType,
                    $formSportType,
                    $oldIcon,
                    $this->getParameter('sporttypes_img_directory')
                );

                $em->persist($sportType);
                $em->flush();

                $this->addFlash('success', 'Le sport a bien été modifié.');

                return $this->redirectToRoute('listSportType');
            }
        }

        return $this->render('AppBundle:SportType:sportType.html.twig', [
            'form' => $form->createView(),
            'sportType' => $sportType,
        ]);
    }

    /**
     * @Route("/admin/sporttype/delete/{sportTypeId}", name="deleteSportType")
     *
     * @param Request $request     request
     * @param mixed   $sportTypeId sportType id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSportType(Request $request, $sportTypeId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $sportTypeManager = $em->getRepository('AppBundle:SportType');
        $sportType = $sportTypeManager->find($sportTypeId);

        if (null === $sportType) {
            throw $this->createNotFoundException('Ce type de sport n\'existe pas');
        }

        $em->remove($sportType);
        $em->flush();

        $this->addFlash('success', 'Le sport a bien été supprimé.');

        return $this->redirectToRoute('listSportType');
    }

    /**
     * @Route("/admin/sporttype", name="listSportType")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listSportTypes(Request $request)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $sportTypeManager = $em->getRepository('AppBundle:SportType');
        $formSportType = new SportType();

        $form = $this->createFormBuilder($formSportType)
            ->add('sport', TextType::class, ['label' => 'Sport'])
            ->add('icon', FileType::class, [
                'label' => 'Icone',
                'label_attr' => ['class' => 'form--fixed-label'],
                'attr' => ['class' => 'form-input--image'],
                'data_class' => null,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter un sport'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sportTypeManager = $em->getRepository('AppBundle:SportType');
            $sportTypeExist = $sportTypeManager->findBy(
                ['sport' => $formSportType->getSport()]
            );
            if (!$sportTypeExist) {
                $sportTypeService = $this->container->get('SportTypeService');
                $sportType = $sportTypeService->sportTypeFromForm(
                    $formSportType,
                    $this->getParameter('sporttypes_img_directory')
                );

                $em->persist($sportType);
                $em->flush();
                $this->addFlash('success', 'Le sport a bien été ajouté.');

                return $this->redirectToRoute('listSportType');
            } else {
                $form->addError(new FormError('Ce type de sport existe déjà.'));
            }
        }

        // Get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $sportTypes = $sportTypeManager->findAll();

        return $this->render(
            'AppBundle:SportType:listSportType.html.twig',
            [
                'sportTypes' => $sportTypes,
                'form' => $form->createView(),
            ]
        );
    }
}
