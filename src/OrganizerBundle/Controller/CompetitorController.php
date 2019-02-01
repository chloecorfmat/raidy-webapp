<?php
/**
 * Created by PhpStorm.
 * User: Alicia
 * Date: 07/01/2019
 * Time: 10:53
 */

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Competitor;
use AppBundle\Entity\Fraud;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;

class CompetitorController extends Controller
{

    /**
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     */
    private $csvFile;

    /**
     * @Route("/organizer/raid/{raidId}/competitor", name="listCompetitor")
     *
     * @param Request $request request
     * @param mixed   $raidId  raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCompetitor(Request $request, $raidId)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $competitorManager = $em->getRepository('AppBundle:Competitor');

        $raidManager = $em->getRepository('AppBundle:Raid');
        $fraudManager = $em->getRepository('AppBundle:Fraud');
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        $competitors = $competitorManager->findBy(
            [
                'raid' => $raid->getId(),
            ]
        );

        $frauds = [];
        /** @var Competitor $competitor */
        foreach ($competitors as $competitor) {
            $fraud = $fraudManager->findBy(["competitor" => $competitor]);

            if (count($fraud) > 0) {
                $frauds[$competitor->getId()] = [];
                /** @var Fraud $f */
                foreach ($fraud as $f) {
                    $frauds[$competitor->getId()][] = $f->getCheckpoint()->getPoi()->getName();
                }
            }
        }

        $formFactory = $this->get('form.factory');

        $formCompetitor = new Competitor();
        $raceManager = $em->getRepository('AppBundle:Race');
        $races = $raceManager->findBy(array('raid' => $raid));

        $form = $formFactory->createNamedBuilder(
            'addCompetitor',
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $formCompetitor
        )
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('number_sign', TextType::class, ['label' => 'N° de dossard'])
            ->add('category', TextType::class, ['label' => 'Catégorie', 'required' => false])
            ->add('sex', TextType::class, ['label' => 'Sexe', 'required' => false])
            ->add('birth_year', TextType::class, ['label' => 'Année de naissance', 'required' => false])
            ->add(
                'race',
                ChoiceType::class,
                ['label' => 'Épreuve',
                    'required' => false,
                    'choices' => $races,
                    'choice_label' => function ($race) {
                        /**
                         * @var Race $race
                         */
                        return $race->getName();
                    },
                ]
            )

            ->add('submit', SubmitType::class, ['label' => 'Ajouter un participant'])
            ->getForm();

        $file = [];
        $formImport = $formFactory->createNamedBuilder(
            'importCsv',
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $file
        )
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'label_attr' => ['class' => 'form--fixed-label'],
                'attr' => [
                    'accept' => '.csv',
                    'class' => 'form--input-file',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Importer le fichier'])
            ->getForm();

        $form->handleRequest($request);
        $formImport->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorManager = $em->getRepository('AppBundle:Competitor');
            $competitorNameExist = $competitorManager->findBy(
                ['firstname' => $formCompetitor->getFirstname(),
                    'lastname' => $formCompetitor->getLastname(),
                    'raid' => $raid->getId(),
                ]
            );

            $competitorSignExist = $competitorManager->findBy(
                ['numberSign' => $formCompetitor->getNumberSign(), 'raid' => $raid->getId()]
            );

            if (!$competitorNameExist) {
                if (!$competitorSignExist) {
                    $competitorService = $this->container->get('CompetitorService');
                    $competitor = $competitorService->competitorFromForm(
                        $formCompetitor,
                        $raid->getId()
                    );

                    $em->persist($competitor);
                    $em->flush();
                    $this->addFlash('success', 'Le participant a bien été ajouté.');

                    return $this->redirectToRoute('listCompetitor', ['raidId' => $raidId]);
                } else {
                    $form->addError(new FormError('Ce dossard existe déjà.'));
                }
            } else {
                $form->addError(new FormError('Ce participant existe déjà.'));
            }
        }

        if ($formImport->isSubmitted() && $formImport->isValid()) {
            $file = $formImport->getData();
            $dir = $this->getParameter('competitors_csv_directory');
            $uploadedFileService = $this->container->get('UploadedFileService');
            $fileName = $uploadedFileService->saveFile(
                $file['file'],
                $dir
            );

            $row = 1;
            $errors = array();
            $correctLines = array();
            if (($handle = fopen($dir . $fileName, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                    // format : lastname; firstname; numbersign; cat; sex; birthyear; race

                    $competitorManager = $em->getRepository('AppBundle:Competitor');
                    $competitorNameExist = $competitorManager->findBy(
                        ['firstname' => $data[1], 'lastname' => $data[0], 'raid' => $raid->getId()]
                    );

                    $competitorSignExist = $competitorManager->findBy(
                        ['numberSign' => $data[2], 'raid' => $raid->getId()]
                    );

                    $hasError = false;
                    if ($data[0] == "" || $data[1] == "" || $data[2] == "") {
                        array_push($errors, array("line" => $row,
                            "msg" => "Un champ requis est manquant",
                        ));
                        $hasError = true;
                    }

                    if ($competitorNameExist != false) {
                        array_push($errors, array("line" => $row,
                            "msg" => 'Le participant "' . $data[0] . " " . $data[1] . '" existe déjà',
                        ));
                        $hasError = true;
                    }
                    if ($competitorSignExist != false) {
                        array_push($errors, array("line" => $row,
                            "msg" => "Le dossard " . $data[2] . " existe déjà",
                        ));
                        $hasError = true;
                    }
                    if ($data[5] != "" && !is_numeric($data[5])) {
                        array_push($errors, array("line" => $row,
                            "msg" => 'L\'année de naissance "' . $data[5] . '" n\'est pas valide',
                        ));
                        $hasError = true;
                    }
                    if ($data[6] != "") {
                        $raceManager = $em->getRepository('AppBundle:Race');
                        $raceExist = $raceManager->findOneBy(
                            ['name' => $data[6], 'raid' => $raid->getId()]
                        );
                        if ($raceExist == null) {
                            array_push($errors, array("line" => $row,
                                "msg" => 'L\'épreuve "' . $data[6] . '" n\'existe pas',
                            ));
                            $hasError = true;
                        }
                    }

                    if (!$hasError) {
                        $data[6] = $data[6] == "" ? null : $raceExist;
                        array_push($correctLines, $data);
                    }

                    $row++;
                }
                fclose($handle);
            }

            foreach ($errors as $e) {
                $formImport->addError(new FormError("Ligne " . $e["line"] . " : " . $e["msg"]));
            }

            if (empty($errors)) {
                $competitorService = $this->container->get('CompetitorService');
                foreach ($correctLines as $data) {
                    $competitor = $competitorService->competitorFromCsv(
                        $data,
                        $raid->getId()
                    );
                    $em->persist($competitor);
                    $em->flush();
                }
                $this->addFlash('success', count($correctLines) . ' participants ont bien été ajoutés.');

                return $this->redirectToRoute('listCompetitor', ['raidId' => $raidId]);
            }
        }

        return $this->render(
            'OrganizerBundle:Competitor:listCompetitor.html.twig',
            [
                'raid_id'     => $raidId,
                'raidName'    => $raid->getName(),
                'competitors' => $competitors,
                'frauds'      => $frauds,
                'form'        => $form->createView(),
                'formImport'  => $formImport->createView(),
            ]
        );
    }

    /**
     * @Route("/organizer/raid/{raidId}/competitor/{competitorId}", name="editCompetitor")
     *
     * @param Request $request      request
     * @param int     $raidId       raid identifier
     * @param int     $competitorId competitor identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCompetitor(Request $request, $raidId, $competitorId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');
        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        // Find the user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null == $user->getId()) {
            throw $this->createNotFoundException('Accès refusé.');
        }

        $competitorManager = $em->getRepository('AppBundle:Competitor');

        $formCompetitor = $competitorManager->findOneBy(['id' => $competitorId]);

        $competitor = $formCompetitor;
        $raceManager = $em->getRepository('AppBundle:Race');
        $races = $raceManager->findBy(array('raid' => $raid));

        if (null === $formCompetitor) {
            throw $this->createNotFoundException('Ce participant n\'existe pas');
        }

        $form = $this->createFormBuilder($formCompetitor)
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('number_sign', TextType::class, ['label' => 'N° de dossard'])
            ->add('category', TextType::class, ['label' => 'Catégorie', 'required' => false])
            ->add('sex', TextType::class, ['label' => 'Sexe', 'required' => false])
            ->add('birth_year', TextType::class, ['label' => 'Année de naissance', 'required' => false])
            ->add('race', ChoiceType::class, [
                    'label' => 'Épreuve',
                    'required' => false,
                    'choices' => $races,
                    'choice_label' => function ($race) {
                        /**
                         * @var Race $race
                         */
                        return $race->getName();
                    },
            ])
            ->add('submit', SubmitType::class, ['label' => 'Editer le participant'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorExist = $competitorManager->findBy(
                ['firstname' => $formCompetitor->getFirstname(),
                    'lastname' => $formCompetitor->getLastname(),
                    'raid' => $raid->getId(), ]
            );
            $competitorSignExist = $competitorManager->findBy(
                ['numberSign' => $formCompetitor->getNumberSign(), 'raid' => $raid->getId()]
            );
            if (!$competitorExist || $competitorExist[0]->getId() === $formCompetitor->getId()) {
                if (!$competitorSignExist || $competitorSignExist[0]->getId() === $formCompetitor->getId()) {
                    $formCompetitor = $form->getData();

                    $competitor = $competitorManager->findOneBy(['id' => $formCompetitor->getId()]);
                    $competitor->setFirstname($formCompetitor->getFirstname());
                    $competitor->setLastname($formCompetitor->getLastname());
                    $competitor->setNumberSign($formCompetitor->getNumberSign());
                    $competitor->setCategory($formCompetitor->getCategory());
                    $competitor->setSex($formCompetitor->getSex());
                    $competitor->setBirthYear($formCompetitor->getBirthYear());
                    $competitor->setRace($formCompetitor->getRace());

                    $em->persist($competitor);
                    $em->flush();

                    $this->addFlash('success', 'Le participant a bien été mis à jour.');

                    return $this->redirectToRoute('listCompetitor', ['raidId' => $raidId]);
                } else {
                    $form->addError(new FormError('Ce dossard existe déjà.'));
                }
            } else {
                $form->addError(new FormError('Ce participant existe déjà.'));
            }
        }

        return $this->render(
            'OrganizerBundle:Competitor:competitor.html.twig',
            [
                'form' => $form->createView(),
                'raid' => $raid,
                'raidId' => $raid->getUniqId(),
                'competitor' => $competitor,
            ]
        );
    }

    /**
     * @Route("/organizer/raid/{raidId}/competitor/delete/{competitorId}", name="deleteCompetitor")
     *
     * @param Request $request      request
     * @param int     $raidId       raid identifier
     * @param int     $competitorId competitor identifier
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCompetitor(Request $request, $raidId, $competitorId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $competitor = $competitorManager->find($competitorId);

        if (null != $competitor) {
            $em->remove($competitor);
            $em->flush();
        } else {
            throw $this->createNotFoundException('Ce participant n\'existe pas');
        }

        $this->addFlash('success', 'Le participant a bien été supprimé.');

        return $this->redirectToRoute('listCompetitor', ['raidId' => $raidId]);
    }
}
