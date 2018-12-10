<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 08:27
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\Contact;
use AppBundle\Entity\Helper;
use AppBundle\Entity\PoiType;
use AppBundle\Entity\Raid;
use AppBundle\Entity\User;
use AppBundle\Service\ContactService;
use PHPUnit\Framework\TestCase;

class ContactServiceTest extends TestCase
{
    private $container;

    /** @var ContactService $contactService */
    private $contactService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->contactService = $this->container->get('ContactService');
    }

    public function testContactsArrayToJson()
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername("username");
        $user->setFirstName("firstname");
        $user->setLastName("lastname");

        $raid = new Raid();
        $raid->setId(1);
        $raid->setName("Raid test");
        $raid->setUser($user);

        $poiType = new PoiType();
        $poiType->setId(1);
        $poiType->setColor("#F5F5F5");
        $poiType->setType("TESTPOITYPE");
        $poiType->setUser($user);

        $helper = new Helper();
        $helper->setId(1);
        $helper->setRaid($raid);
        $helper->setUser($user);
        $helper->setFavoritePoiType($poiType);

        $contact = new Contact();
        $contact->setId(1);
        $contact->setRaid($raid);
        $contact->setHelper($helper);
        $contact->setRole("TESTER");

        $contacts = [];
        $contacts[] = $contact;

        $json = $this->contactService->contactsArrayToJson($contacts);
        self::assertEquals('[{"id":1,"role":"TESTER","phoneNumber":"","raid":1,"helper":{"id":1,"user":1,"isCheckedIn":null,"poi":"","favoritePoiType":1,"checkInTime":"","raid":1},"user":{"id":1,"firstname":"firstname","lastname":"lastname"}}]',
            $json);
    }

}