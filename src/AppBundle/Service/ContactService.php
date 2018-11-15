<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 15/11/2018
 * Time: 15:41
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class ContactService
{
    /**
     * ContactService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
            $obj['phoneNumber'] = htmlentities($contact->getPhoneNumber());
            $obj['raid'] = $contact->getRaid()->getId();

            if (null != $contact->getHelper()) {
                $obj['helper'] = $contact->getHelper()->getId();
            } else {
                $obj['helper'] = '';
            }

            $contactsObj[] = $obj;
        }

        return json_encode($contactsObj);
    }
}
