<?php

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\Type\QuillType;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class OrganizerMessageController extends Controller
{
    /**
     * @Route("/organizer/raid/{raidId}/message", name="listMessages")
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMessages(Request $request, $raidId)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        // Get the user.
        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        // Get used poitypes in this raid.
        $poiQueryBuilder = $em->getRepository('AppBundle:Poi')->createQueryBuilder('p');
        $poitypesQuery = $poiQueryBuilder->select('IDENTITY(p.poiType)')
            ->andWhere('p.raid = :raid')
            ->setParameter('raid', $raid)
            ->distinct(true)
            ->getQuery()
            ->getResult();

        $poitypes = [];
        $poitypeManager = $em->getRepository('AppBundle:PoiType');
        foreach ($poitypesQuery as $poitype) {
            $p = $poitypeManager->find(current($poitype));
            $poitypes[$p->getType()] = current($poitype);
        }

        // Manage message form.
        $formMessage = new Message();

        $form = $this->createFormBuilder($formMessage)
            ->add(
                'targetPoiTypes',
                ChoiceType::class,
                array(
                    'choices' => $poitypes,
                    'multiple' => true,
                    'label' => 'Type de point d\'intérêt des bénévoles',
                )
            )
            ->add('text', QuillType::class, ['label' => 'Message'])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter un message'])
            ->getForm();

        $form->handleRequest($request);

        $data = $request->request->all();
        if ($form->isSubmitted() && $form->isValid()) {
            if (null != $data['text']) {
                $message = new Message();
                $message->setText(addslashes($data['text']));
                $message->setRaid($raid);

                foreach ($data['form']['targetPoiTypes'] as $poitype) {
                    $tp[] = $poitypeManager->find($poitype);
                }

                $message->setTargetPoitypes($tp);

                $message->setDatetime(new \DateTime());
                $em->persist($message);
                $em->flush();

                $this->addFlash('success', 'Le message a bien été créé.');

                return $this->redirectToRoute('listMessages', array('raidId' => $raidId));
            }
        }

        // Get all messages.
        $messageRepository = $em->getRepository('AppBundle:Message');
        $messages = $messageRepository->findBy(['raid' => $raid], ['datetime' => 'DESC']);

        return $this->render(
            'OrganizerBundle:Message:listMessage.html.twig',
            [
                'raid' => $raid,
                'form' => $form->createView(),
                'messages' => $messages,
            ]
        );
    }

    /**
     * @Route("/organizer/raid/{raidId}/message/delete/{messageId}", name="deleteMessage")
     *
     * @param Request $request   request
     * @param string  $raidId    raid identifier
     * @param int     $messageId message identifier
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMessage(Request $request, $raidId, $messageId)
    {
        // Get managers
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->findOneBy(array('id' => $raidId));
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createNotFoundException('Accès refusé.');
        }

        $messageManager = $em->getRepository('AppBundle:Message');
        $message = $messageManager->find($messageId);

        if (null != $message) {
            $em->remove($message);
            $em->flush();
        } else {
            throw $this->createNotFoundException('Ce message n\'existe pas');
        }

        $this->addFlash('success', 'Le message a bien été supprimé.');

        return $this->redirectToRoute('listMessages', array('raidId' => $raid->getUniqId()));
    }
}
