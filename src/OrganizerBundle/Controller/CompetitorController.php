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

        $form = $this->createFormBuilder($formCompetitor)
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('number_sign', TextType::class, ['label' => 'N° de dossard'])
            ->add('category', TextType::class, ['label' => 'Catégorie','required' => false])
            ->add('sex', TextType::class, ['label' => 'Sexe','required' => false])
            ->add('birth_year', TextType::class, ['label' => 'Année de naissance','required' => false])

            ->add('submit', SubmitType::class, ['label' => 'Ajouter un participant'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorManager = $em->getRepository('AppBundle:Competitor');
            $competitorExist = $competitorManager->findBy(
                ['firstname' => $formCompetitor->getFirstname(), 'lastname' => $formCompetitor->getLastname()]
            );
            if (!$competitorExist) {
                $competitorService = $this->container->get('CompetitorService');
                $competitor = $competitorService->competitorFromForm(
                    $formCompetitor,
                    $raid->getId()
                );

                $em->persist($competitor);
                $em->flush();
                $this->addFlash('success', 'Le participant a bien été ajouté.');

                return $this->redirectToRoute('listCompetitor',  ['raidId' => $raidId]);
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

}