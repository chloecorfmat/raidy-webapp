<?php
/**
 * Created by PhpStorm.
 * User: Alicia
 * Date: 07/01/2019
 * Time: 10:53
 */

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Competitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

class CompetitorController extends Controller
{
    /**
     * @Route("/organizer/raid/{raidId}/competitor", name="listCompetitor")
     *
     * @param Request $request request
     * @param mixed   $raidId      raidId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCompetitor(Request $request, $raidId)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $competitorManager = $em->getRepository('AppBundle:Competitor');

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        $competitors = $competitorManager->findBy(
            [
                'raid' => $raid->getId(),
            ]
        );

        $formCompetitor = new Competitor();
        $raceManager = $em->getRepository('AppBundle:Race');
        $races = $raceManager->findBy(array('raid' => $raid));

        $form = $this->createFormBuilder($formCompetitor)
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('number_sign', TextType::class, ['label' => 'N° de dossard'])
            ->add('category', TextType::class, ['label' => 'Catégorie','required' => false])
            ->add('sex', TextType::class, ['label' => 'Sexe','required' => false])
            ->add('birth_year', TextType::class, ['label' => 'Année de naissance','required' => false])
            ->add('race', ChoiceType::class,
                ['label' => 'Épreuve', 'required' => false, 'choices' => $races, 'choice_label' => function ($race) {
                    /**
                     * @var Race $race
                     */
                    return $race->getName();
                }]
            )

            ->add('submit', SubmitType::class, ['label' => 'Ajouter un participant'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorManager = $em->getRepository('AppBundle:Competitor');
            $competitorNameExist = $competitorManager->findBy(
                ['firstname' => $formCompetitor->getFirstname(), 'lastname' => $formCompetitor->getLastname(), 'raid' => $raid->getId()]
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

        return $this->render(
            'OrganizerBundle:Competitor:listCompetitor.html.twig',
            [
                'raid_id' => $raidId,
                'raidName' => $raid->getName(),
                'competitors' => $competitors,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/organizer/raid/{raidId}/competitor/{competitorId}", name="editCompetitor")
     *
     * @param Request $request   request
     * @param int     $raidId    raid identifier
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
            ->add('category', TextType::class, ['label' => 'Catégorie','required' => false])
            ->add('sex', TextType::class, ['label' => 'Sexe','required' => false])
            ->add('birth_year', TextType::class, ['label' => 'Année de naissance','required' => false])
            ->add('race', ChoiceType::class,
                ['label' => 'Épreuve', 'required' => false, 'choices' => $races, 'choice_label' => function ($race) {
                    /**
                     * @var Race $race
                     */
                    return $race->getName();
                }]
            )
            ->add('submit', SubmitType::class, ['label' => 'Editer le participant'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $competitorExist = $competitorManager->findBy(
                ['firstname' => $formCompetitor->getFirstname(), 'lastname' => $formCompetitor->getLastname(), 'raid' => $raid->getId()]
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
     * @param Request $request   request
     * @param int     $raidId    raid identifier
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

        return $this->redirectToRoute('listCompetitor', ['raidId' => $raidId]);
    }

}