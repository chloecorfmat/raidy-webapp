<?php

namespace OrganizerBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Message;
use AppBundle\Form\Type\QuillType;
use OrganizerBundle\Security\RaidVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerMessageController extends AjaxAPIController
{
    /**
     * @Route("/organizer/raid/{raidId}/message", name="listMessages")
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
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
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'Danger / Important / Erreur' => 'danger',
                        'Information' => 'info',
                        'Succès' => 'success',
                    ],
                    'multiple' => false,
                    'label' => 'Type de message',
                ]
            )
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
                $message->setText($data['text']);
                $message->setType($data['form']['type']);
                $message->setRaid($raid);

                foreach ($data['form']['targetPoiTypes'] as $poitype) {
                    $tp[] = $poitypeManager->find($poitype);
                }

                $message->setTargetPoitypes($tp);

                $message->setDatetime(new \DateTime());
                $em->persist($message);
                $em->flush();

                $this->addFlash('success', 'Le message a bien été créé.');

                /* Send email to all helpers */

                $poiManager = $em->getRepository('AppBundle:Poi');

                $pois[] = null;
                foreach ($tp as $poitype) {
                    $pois = $poiManager->findBy(array('poiType' => $poitype, 'raid' => $raid->getId()));
                }

                if (null != $pois) {
                    $helperManager = $em->getRepository('AppBundle:Helper');
                    $helpers = $helperManager->findBy(array('poi' => $pois));

                    $host = ($request->server->get('HTTP_X_FORWARDED_HOST')) ?
                        $request->getScheme() . '://' . $request->server->get('HTTP_X_FORWARDED_HOST') :
                        $request->getScheme() . '://' . $request->server->get('HTTP_HOST');

                    foreach ($helpers as $helper) {
                        $mail = \Swift_Message::newInstance()
                            ->setSubject('Nouvelle notification pour le raid ' . $raid->getName())
                            ->setFrom($this->container->getParameter('app.mail.from'))
                            ->setReplyTo($this->container->getParameter('app.mail.reply_to'))
                            ->setTo($helper->getUser()->getEmail())
                            ->setBody(
                                $this->renderView(
                                    'OrganizerBundle:Emails:notification.html.twig',
                                    array(
                                        'helper' => $helper->getUser(),
                                        'raid' => $raid,
                                        'message' => $message,
                                        'host' => $host,
                                    )
                                ),
                                'text/html'
                            );

                        $this->get('mailer')->send($mail);
                    }
                }

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

    /**
     * @Route("/organizer/raid/{raidId}/message/{messageId}/edit", name="patchMessageEdit", methods={"PATCH"})
     *
     * @param Request $request
     * @param int     $raidId    raid id
     * @param int     $messageId message id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchMessageEdit(Request $request, $raidId, $messageId)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(['uniqid' => $raidId]);

        if (null == $raid) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This raid does not exist');
        }

        $authChecker = $this->get('security.authorization_checker');
        if (!$authChecker->isGranted(RaidVoter::EDIT, $raid)) {
            throw $this->createAccessDeniedException();
        }

        $messageManager = $em->getRepository('AppBundle:Message');
        $message = $messageManager->find($messageId);

        if (null == $message) {
            return parent::buildJSONStatus(Response::HTTP_NOT_FOUND, 'This message does not exist');
        }

        $data = $request->request->all();
        $message->setText($data['content']);

        $em->flush();

        $ret = [];
        $ret['message'] = $message->getId();
        $ret['content'] = $message->getText();
        $ret['code'] = Response::HTTP_OK;

        return new Response(json_encode($ret));
    }
}
