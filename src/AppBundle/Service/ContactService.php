<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 15/11/2018
 * Time: 15:41
 */

namespace AppBundle\Service;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Raid;
use Doctrine\ORM\EntityManagerInterface;

class ContactService
{

    private $helperService;

    /**
     * ContactService constructor.
     * @param EntityManagerInterface $em
     * @param HelperService          $helperService
     */
    public function __construct(EntityManagerInterface $em, HelperService $helperService)
    {
        $this->em            = $em;
        $this->helperService = $helperService;
    }

    /**
     * @param array $contacts
     *
     * @return false|string
     */
    public function contactsArrayToJson($contacts)
    {
        $contactsObj = [];

        foreach ($contacts as $contact) {
            $obj = [];

            $obj['id'] = $contact->getId();
            $obj['role'] = htmlentities($contact->getRole());

            if ($contact->getHelper() != null) {
                $obj['phoneNumber'] = htmlentities($contact->getHelper()->getUser()->getPhone());
            } else {
                $obj['phoneNumber'] = htmlentities($contact->getPhoneNumber());
            }

            $obj['raid'] = $contact->getRaid()->getId();

            if (null != $contact->getHelper()) {
                $h = json_decode($this->helperService->helperToJson($contact->getHelper()));
                $obj['helper'] = $h;

                $obj['user']['id'] = $contact->getHelper()->getUser()->getId();
                $obj['user']['firstname'] = $contact->getHelper()->getUser()->getFirstName();
                $obj['user']['lastname'] = $contact->getHelper()->getUser()->getLastName();
            } else {
                $obj['helper'] = '';
                $obj['user'] = '';
            }

            $contactsObj[] = $obj;
        }

        return json_encode($contactsObj);
    }

    /**
     * @param Raid $raidToClone
     * @param Raid $raid
     */
    public function cloneContacts($raidToClone, $raid)
    {
        // Clone contacts
        $contactRepository = $this->em->getRepository('AppBundle:Contact');
        $contacts = $contactRepository->findBy(array('raid' => $raidToClone->getId()));

        if (null != $contacts) {
            foreach ($contacts as $contact) {
                if (is_null($contact->getHelper())) {
                    $c = new Contact();

                    $c->setRole($contact->getRole());
                    $c->setPhoneNumber($contact->getPhoneNumber());
                    $c->setRaid($raid);

                    $this->em->persist($c);
                    $this->em->flush();
                }
            }
        }
    }
}
