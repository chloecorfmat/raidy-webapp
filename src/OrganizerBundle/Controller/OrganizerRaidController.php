<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 01/10/2018
 * Time: 13:29.
 */

namespace OrganizerBundle\Controller;

use AppBundle\Entity\Raid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;

class OrganizerRaidController extends Controller
{
    /**
     * @Route("/raid/new", name="addRaid")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRaid(Request $request)
    {
        $formRaid = new Raid();

        $form = $this->createFormBuilder($formRaid)
            ->add('name', TextType::class)
            ->add('date', DateType::class)
            ->add('address', TextType::class)
            ->add('addressAddition', TextType::class, array('required' => false))
            ->add('postCode', IntegerType::class)
            ->add('city', TextType::class)
            ->add('editionNumber', IntegerType::class)
            ->add('picture', FileType::class)
            ->add('submit', SubmitType::class, array('label' => 'Créer un raid'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $raidManager = $em->getRepository('AppBundle:Raid');
            $raidExist = $raidManager->findBy(
                array('name' => $formRaid->getName(), 'editionNumber' => $formRaid->getEditionNumber())
            );
            if (!$raidExist) {
                $formRaid = $form->getData();

                $fileName = $this->saveFile($formRaid->getPicture());

                $raid = new Raid();

                $raid->setName($formRaid->getName());
                $raid->setDate($formRaid->getDate());
                $raid->setAddress($formRaid->getAddress());
                if ($formRaid->getAddressAddition()) {
                    $raid->setAddressAddition($formRaid->getAddressAddition());
                }
                $raid->setPostCode($formRaid->getPostCode());
                $raid->setCity($formRaid->getCity());
                $raid->setEditionNumber($formRaid->getEditionNumber());
                $raid->setUser($this->getUser());
                $raid->setPicture($fileName);

                $em->persist($raid);
                $em->flush();

                return $this->redirectToRoute('raidList');
            } else {
                $form->addError(new FormError('Ce raid existe déjà.'));
            }
        }

        return $this->render('OrganizerBundle:Raid:addRaid.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/raid/{id}", name="displayRaid")
     *
     * @param Request $request request
     * @param int     $id      raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayRaid(Request $request, $id)
    {
        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raid = $raidManager->find($id);

        return $this->render('OrganizerBundle:Raid:raid.html.twig', [
            'raid' => $raid,
        ]);
    }

    /**
     * @Route("/raid/edit/{id}", name="editRaid")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRaid(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        $formRaid = $raidManager->findOneBy(['id' => $id]);

        if (null == $formRaid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }
        // Get the previous picture in case a new one is not submitted
        $myPicture = $formRaid->getPicture();

        $form = $this->createFormBuilder($formRaid)
            ->add('name', TextType::class)
            ->add('date', DateType::class)
            ->add('address', TextType::class)
            ->add('addressAddition', TextType::class, array('required' => false))
            ->add('postCode', IntegerType::class)
            ->add('city', TextType::class)
            ->add('editionNumber', IntegerType::class)
            ->add('picture', FileType::class, array('required' => false, 'data_class' => null))
            ->add('submit', SubmitType::class, array('label' => 'Editer un raid'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raidExist = $raidManager->findOneBy(
                array('name' => $formRaid->getName(), 'editionNumber' => $formRaid->getEditionNumber())
            );

            if (!$raidExist || $raidExist->getId() == $formRaid->getId()) {
                $formRaid = $form->getData();

                $raid = $raidManager->findOneBy(array('id' => $formRaid->getId()));

                $raid->setName($formRaid->getName());
                $raid->setDate($formRaid->getDate());
                $raid->setAddress($formRaid->getAddress());
                if ($formRaid->getAddressAddition()) {
                    $raid->setAddressAddition($formRaid->getAddressAddition());
                }
                $raid->setPostCode($formRaid->getPostCode());
                $raid->setCity($formRaid->getCity());
                $raid->setEditionNumber($formRaid->getEditionNumber());
                if (null != $formRaid->getPicture()) {
                    $fileName = $this->saveFile($formRaid->getPicture());
                    $raid->setPicture($fileName);
                } else {
                    $picturePath = $this->getParameter('raids_img_directory') . '/' . $myPicture;
                    $fileName = $this->saveFile(new File($picturePath));
                    $raid->setPicture($fileName);
                }

                $em->persist($raid);
                $em->flush();

                return $this->redirectToRoute('displayRaid', ['id' => $id]);
            } else {
                $form->addError(new FormError('Un raid avec ce nom a été trouvé.'));
            }
        }

        return $this->render('OrganizerBundle:Raid:editRaid.html.twig', [
            'form' => $form->createView(),
            'raidId' => $id,
        ]);
    }

    /**
     * @Route("/raid/delete/{id}", name="deleteRaid")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteRaid(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em
            ->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('id' => $id));
        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $em->remove($raid);
        $em->flush();

        return $this->redirectToRoute('raidList');
    }

    /**
     * @Route("/raid", name="raidList")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRaids()
    {
        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raids = $raidManager->findAll();

        return $this->render(
            'OrganizerBundle:Raid:listRaid.html.twig',
            [
                'raids' => $raids,
            ]
        );
    }

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
        $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $this->getParameter('raids_img_directory'),
                $fileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }
}
