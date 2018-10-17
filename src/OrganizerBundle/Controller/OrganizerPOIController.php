<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 13/10/2018
 * Time: 19:33
 */

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Poi;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Track;
use AppBundle\Entity\PoiType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class OrganizerPOIController extends Controller
{
    /**
     * @Route("/organizer/raid/{raidId}/track/{trackId}/poi/add", name="addPoi")
     *
     * @param Request $request request
     * @param int     $raidId  raidId
     * @param int     $trackId trackId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPoi(Request $request, $raidId, $trackId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');
        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raidId = (int) $raidId;
        $trackId = (int) $trackId;

        $raid = $raidManager->findOneBy(array('id' => $raidId));
        $track = $trackManager->findOneBy(array('id' => $trackId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (null == $track) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        if ($track->getRaid()->getId() != $raid->getId()) {
            throw $this->createAccessDeniedException();
        }

        $poiTypes = $poiTypeManager->findAll();

        // Create form
        $formPoi = new Poi();

        $form = $this->createFormBuilder($formPoi)
            ->add('name', TextType::class, array('label' => 'Nom du point d\'intérêt'))
            ->add('longitude', TextType::class, array('label' => 'Longitude'))
            ->add('latitude', TextType::class, array('label' => 'Latitude'))
            ->add('requiredHelpers', IntegerType::class, array('label' => 'Nombre de bénévoles'))
            ->add('poiType', ChoiceType::class, array(
                'choices' => $poiTypes,
                'choice_label' => function ($poiType, $key, $value) {
                    /** @var PoiType $poiType */

                    return $poiType->getType();
                },
                'choice_attr' => function ($poiType, $key, $value) {

                    return ['class' => 'poiType_' . strtolower($poiType->getType())];
                },
            ))
            ->add('submit', SubmitType::class, array('label' => 'Créer un POI'))
            ->getForm();

        $form->handleRequest($request);

        // Form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $poiManager = $em->getRepository('AppBundle:Poi');
            $poiExist = $poiManager->findBy(
                array('track' => $track, 'longitude' => $formPoi->getLongitude(), 'latitude' => $formPoi->getLatitude())
            );
            if (!$poiExist) {
                $formPoi = $form->getData();

                $poi = new Poi();

                $poi->setName($formPoi->getName());
                $poi->setLongitude($formPoi->getLongitude());
                $poi->setLatitude($formPoi->getLatitude());
                $poi->setRequiredHelpers($formPoi->getRequiredHelpers());
                $poi->setTrack($track);
                $poi->setPoiType($formPoi->getPoiType());

                $em->persist($poi);
                $em->flush();

                return $this->redirectToRoute('listPoi', array('raidId' => $raidId, 'trackId' => $trackId));
            } else {
                $form->addError(new FormError('Ce POI existe déjà.'));
            }
        }

        return $this->render('OrganizerBundle:Poi:addPoi.html.twig', array(
            'form' => $form->createView(),
            'raidId' => $raidId,
            'trackId' => $trackId,
        ));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/{trackId}/poi/{id}", name="displayPoi")
     *
     * @param Request $request request
     * @param int     $raidId  raidId
     * @param int     $trackId trackId
     * @param int     $id      poi identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayPoi(Request $request, $raidId, $trackId, $id)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $poiManager = $em->getRepository('AppBundle:Poi');
        $trackManager = $em->getRepository('AppBundle:Track');
        $raidManager = $em->getRepository('AppBundle:Raid');
        $poiTypeManager = $em->getRepository('AppBundle:PoiType');

        // Get values from database
        $poiTypes = $poiTypeManager->findAll();
        $track = $trackManager->findOneBy(array('id' => $trackId));
        $raid = $raidManager->findOneBy(array('id' => $raidId));
        $formPoi = $poiManager->findOneBy(array('id' => $id));

        $poi = $poiManager->find($id);

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (null == $track) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        if ($track->getRaid()->getId() != $raid->getId()) {
            throw $this->createAccessDeniedException();
        }

        // Check if Poi exists
        if (null == $formPoi) {
            throw $this->createNotFoundException('Ce POI n\'existe pas');
        }

        // Create form
        $form = $this->createFormBuilder($formPoi)
            ->add('name', TextType::class, array('label' => 'Nom du point d\'intérêt'))
            ->add('longitude', TextType::class, array('label' => 'Longitude'))
            ->add('latitude', TextType::class, array('label' => 'Latitude'))
            ->add('requiredHelpers', IntegerType::class, array('label' => 'Nombre de bénévoles'))
            ->add('poiType', ChoiceType::class, array(
                'choices' => $poiTypes,
                'choice_label' => function ($poiType, $key, $value) {
                    /** @var PoiType $poiType */

                    return $poiType->getType();
                },
                'choice_attr' => function ($poiType, $key, $value) {

                    return ['class' => 'poiType_' . strtolower($poiType->getType())];
                },
            ))
            ->add('submit', SubmitType::class, array('label' => 'Editer un POI'))
            ->getForm();

        $form->handleRequest($request);

        // When form is submitted, check data and send it
        if ($form->isSubmitted() && $form->isValid()) {
            $poiExist = $poiManager->findOneBy(
                array('track' => $track, 'longitude' => $formPoi->getLongitude(), 'latitude' => $formPoi->getLatitude())
            );

            if (!$poiExist || $poiExist->getId() == $formPoi->getId()) {
                $formPoi = $form->getData();

                $poi = $poiManager->findOneBy(array('id' => $formPoi->getId()));

                $poi->setName($formPoi->getName());
                $poi->setLongitude($formPoi->getLongitude());
                $poi->setLatitude($formPoi->getLatitude());
                $poi->setRequiredHelpers($formPoi->getRequiredHelpers());
                $poi->setTrack($track);
                $poi->setPoiType($formPoi->getPoiType());

                $em->persist($poi);
                $em->flush();
            }
        }

        return $this->render('OrganizerBundle:Poi:poi.html.twig', [
            'form' => $form->createView(),
            'poi' => $poi,
            'raidId' => $raidId,
            'trackId' => $trackId,
        ]);
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/{trackId}/poi/delete/{id}", name="deletePoi")
     *
     * @param Request $request request
     * @param mixed   $raidId  raidId
     * @param int     $trackId trackId
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePoi(Request $request, $raidId, $trackId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $poiManager = $em->getRepository('AppBundle:Poi');
        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');

        $poi = $poiManager->findOneBy(array('id' => $id));
        $track = $trackManager->findOneBy(array('id' => $trackId));
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (null == $track) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        if ($track->getRaid()->getId() != $raid->getId()) {
            throw $this->createAccessDeniedException();
        }

        // Check if Poi exists
        if (null == $poi) {
            throw $this->createNotFoundException('Ce POI n\'existe pas');
        }

        $em->remove($poi);
        $em->flush();

        return $this->redirectToRoute('listPoi', array('raidId' => $raidId, 'trackId' => $trackId));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/{trackId}/poi", name="listPoi")
     *
     * @param mixed $raidId  raidId
     * @param int   $trackId trackId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPois($raidId, $trackId)
    {
        $em = $this->getDoctrine()->getManager();
        $poiManager = $em->getRepository('AppBundle:Poi');
        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');

        $raid = $raidManager->findOneBy(array('id' => $raidId));
        $track = $trackManager->findOneBy(array('id' => $trackId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if (null == $track) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        $pois = $poiManager->findBy(array('track' => $trackId));

        return $this->render(
            'OrganizerBundle:Poi:listPoi.html.twig',
            [
                'pois' => $pois,
            ]
        );
    }
}