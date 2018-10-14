<?php

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Track;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class OrganizerTrackController extends Controller
{
    /**
     * @Route("/organizer/raid/{raidId}/track/add", name="addTrack")
     *
     * @param Request $request request
     * @param int     $raidId  raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTrack(Request $request, $raidId)
    {
        // Set up managers
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $raidId = (int) $raidId;

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if ($raid->getUser()->getId() != $user->getId()) {
            throw $this->createNotFoundException('Ce raid ne vous appartient pas');
        }

        $sportTypes = $sportTypeManager->findAll();

        // Create form
        $formTrack = new Track();

        $form = $this->createFormBuilder($formTrack)
            ->add('trackPoints', FileType::class, array(
                'label' => 'Fichier GPX',
                'label_attr' => array('class' => 'form--fixed-label'),
                'required' => false,
                'data_class' => null,
            ))
            ->add('sportType', ChoiceType::class, array(
                'choices' => $sportTypes,
                'choice_label' => function ($sportType, $key, $value) {
                    /** @var Raid $raid */

                    return $sportType->getSport();
                },
                'choice_attr' => function ($sportType, $key, $value) {

                    return ['class' => 'sportType_' . strtolower($sportType->getSport())];
                },
            ))
            ->add('submit', SubmitType::class, array('label' => 'Créer un parcours'))
            ->getForm();

        $form->handleRequest($request);

        // Form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $trackManager = $em->getRepository('AppBundle:Track');
            $trackExist = $trackManager->findBy(
                array('raid' => $formTrack->getRaid(), 'sportType' => $formTrack->getSportType())
            );
            if (!$trackExist) {
                $formTrack = $form->getData();

                $fileName = $this->saveFile($formTrack->getTrackPoints());

                $track = new Track();

                $track->setRaid($raidId);
                $track->setSportType($formTrack->getSportType());
                $track->setTrackPoints($fileName);

                $em->persist($track);
                $em->flush();

                return $this->redirectToRoute('listTrack', array('raidId' => $raidId));
            } else {
                $form->addError(new FormError('Ce parcours existe déjà.'));
            }
        }

        return $this->render('OrganizerBundle:Track:addTrack.html.twig', array(
            'form' => $form->createView(),
            'raidId' => $raidId,
        ));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/{id}", name="displayTrack")
     *
     * @param Request $request request
     * @param int     $raidId  raidId
     * @param int     $id      track identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayTrack(Request $request, $raidId, $id)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $trackManager = $em->getRepository('AppBundle:Track');
        $raidManager = $em->getRepository('AppBundle:Raid');
        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        // Get values from database
        $track = $trackManager->find($id);
        $sportTypes = $sportTypeManager->findAll();
        $formTrack = $trackManager->findOneBy(['id' => $id]);
        $raid = $raidManager->findOneBy(array('id' => $raidId));

        $oldTrackPoints = $formTrack->getTrackPoints();

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        // Check if track exists
        if (null == $formTrack) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        // Create form
        $form = $this->createFormBuilder($formTrack)
            ->add('trackPoints', FileType::class, array(
                'label' => 'Fichier GPX',
                'label_attr' => array('class' => 'form--fixed-label'),
                'required' => false,
                'data_class' => null,
            ))
            ->add('sportType', ChoiceType::class, array(
                'choices' => $sportTypes,
                'choice_label' => function ($sportType, $key, $value) {
                    /** @var Raid $raid */

                    return $sportType->getSport();
                },
                'choice_attr' => function ($sportType, $key, $value) {
                    return ['class' => 'sportType_' . strtolower($sportType->getSport())];
                },
            ))
            ->add('submit', SubmitType::class, array('label' => 'Editer un parcours'))
            ->getForm();

        $form->handleRequest($request);

        // Whenform is submitted, check datas and send it
        if ($form->isSubmitted() && $form->isValid()) {
            $trackExist = $trackManager->findOneBy(
                array('raid' => $formTrack->getRaid(), 'sportType' => $formTrack->getSportType())
            );

            if (!$trackExist || $trackExist->getId() == $formTrack->getId()) {
                $formTrack = $form->getData();

                $track = $trackManager->findOneBy(array('id' => $formTrack->getId()));

                $track->setRaid($formTrack->getRaid());
                $track->setSportType($formTrack->getSportType());
                if (null != $formTrack->getTrackPoints()) {
                    $fileName = $this->saveFile($formTrack->getTrackPoints());
                    $track->setTrackPoints($fileName);
                } else {
                    $track->setTrackPoints($oldTrackPoints);
                }

                $em->persist($track);
                $em->flush();
            }
        }

        return $this->render('OrganizerBundle:Track:track.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
        ]);
    }

    /**
     * @Route("/organizer/raid/{raidId}/track/delete/{id}", name="deleteTrack")
     *
     * @param Request $request request
     * @param mixed   $raidId  raidId
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTrack(Request $request, $raidId, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');
        $track = $trackManager->findOneBy(array('id' => $id));

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        if (null == $track) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        $em->remove($track);
        $em->flush();

        return $this->redirectToRoute('listTrack', array('raidId' => $raidId));
    }

    /**
     * @Route("/organizer/raid/{raidId}/track", name="listTrack")
     *
     * @param mixed $raidId raidId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTracks($raidId)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        $trackManager = $em->getRepository('AppBundle:Track');

        $raid = $raidManager->findOneBy(array('id' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $tracks = $trackManager->findBy(array('raid' => $raidId));

        return $this->render(
            'OrganizerBundle:Track:listTrack.html.twig',
            [
                'tracks' => $tracks,
            ]
        );
    }

    /* @todo : Refactor to make an UploadedFileService */

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @param mixed $file the file to save
     * @return string
     */
    private function saveFile($file)
    {
        // $file stores the uploaded file
        /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $fileName = $this->generateUniqueFileName() . '.gpx';

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $this->getParameter('tracks_gpx_directory'),
                $fileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }
}
