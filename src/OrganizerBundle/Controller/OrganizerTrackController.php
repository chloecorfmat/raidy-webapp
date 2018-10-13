<?php

namespace OrganizerBundle\Controller;

use AppBundle\Entity\SportType;
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
     * @Route("/organizer/track/add", name="addTrack")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTrack(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        $raids = $raidManager->findAll();
        $sportTypes = $sportTypeManager->findAll();

        $formTrack = new Track();

        $form = $this->createFormBuilder($formTrack)
            ->add('trackPoints', FileType::class, array(
                'label' => 'Fichier GPX',
                'label_attr' => array('class' => 'form--fixed-label'),
                'required' => false,
                'data_class' => null,
            ))
            // AddTrackCode
            ->add('raid', ChoiceType::class, array(
                'choices' => $raids,
                'choice_label' => function ($raid, $key, $value) {
                    /** @var Raid $raid */

                    return $raid->getName();
                },
                'choice_attr' => function ($raid, $key, $value) {

                    return ['class' => 'raid_s' . strtolower($raid->getName())];
                },
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

        if ($form->isSubmitted() && $form->isValid()) {
            $trackManager = $em->getRepository('AppBundle:Track');
            $trackExist = $trackManager->findBy(
                array('raid' => $formTrack->getRaid(), 'sportType' => $formTrack->getSportType())
            );
            if (!$trackExist) {
                $formTrack = $form->getData();

                $fileName = $this->saveFile($formTrack->getTrackPoints());

                $track = new Track();

                $track->setRaid($formTrack->getRaid());
                $track->setSportType($formTrack->getSportType());
                $track->setTrackPoints($fileName);

                $em->persist($track);
                $em->flush();

                return $this->redirectToRoute('listTrack');
            } else {
                $form->addError(new FormError('Ce parcours existe déjà.'));
            }
        }

        return $this->render('OrganizerBundle:Track:addTrack.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/organizer/track/{id}", name="displayTrack")
     *
     * @param Request $request request
     * @param int     $id      track identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayTrack(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $trackManager = $em->getRepository('AppBundle:Track');
        $raidManager = $em->getRepository('AppBundle:Raid');
        $sportTypeManager = $em->getRepository('AppBundle:SportType');

        $track = $trackManager->find($id);

        $sportTypes = $sportTypeManager->findAll();

        $formTrack = $trackManager->findOneBy(['id' => $id]);

        $raid = $raidManager->find($formTrack->getRaid());

        $oldTrackPoints = $formTrack->getTrackPoints();

        if (null == $formTrack) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        $form = $this->createFormBuilder($formTrack)
            ->add('trackPoints', FileType::class, array(
                'label' => 'Fichier GPX',
                'label_attr' => array('class' => 'form--fixed-label'),
                'required' => false,
                'data_class' => null,
            ))
            //EditTrackCode
            ->add('raid', TextType::class, array(
                'disabled' => 'true',
                'label' => 'Nom du raid',
                'data' => $raid->getName(),
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
     * @Route("/organizer/track/delete/{id}", name="deleteTrack")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTrack(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $trackManager = $em
            ->getRepository('AppBundle:Track');
        $track = $trackManager->findOneBy(array('id' => $id));
        if (null == $track) {
            throw $this->createNotFoundException('Ce parcours n\'existe pas');
        }

        $em->remove($track);
        $em->flush();

        return $this->redirectToRoute('listTrack');
    }

    /**
     * @Route("/organizer/track", name="listTrack")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTracks()
    {
        $trackManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Track');

        $tracks = $trackManager->findAll();

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
                $this->getParameter('tracks_track_directory'),
                $fileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }
}
