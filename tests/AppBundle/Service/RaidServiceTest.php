<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 09:50
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Entity\Raid;
use AppBundle\Service\RaidService;
use PHPUnit\Framework\TestCase;

class RaidServiceTest extends TestCase
{
    private $container;

    /** @var RaidService $raidService */
    private $raidService;

    public function __construct()
    {
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->raidService = $this->container->get('RaidService');
    }

    public function testRaidToJson()
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
        $raid->setAddress("address");
        $raid->setAddressAddition("address addition");
        $raid->setCity("city");
        $raid->setPostCode("22300");
        $raid->setEditionNumber(2);
        $uniqid = uniqid();
        $raid->setUniqid($uniqid);

        $json = $this->raidService->raidToJson($raid);

        self::assertEquals('{"id":1,"name":"Raid test","date":null,"address":"address","addressAddition":"address addition","postCode":"22300","city":"city","editionNumber":2,"picture":null,"uniqid":"'.$uniqid.'"}', $json);
    }

    public function testRaidsArrayToJson()
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
        $raid->setAddress("address");
        $raid->setAddressAddition("address addition");
        $raid->setCity("city");
        $raid->setPostCode("22300");
        $raid->setEditionNumber(2);
        $uniqid = uniqid();
        $raid->setUniqid($uniqid);

        $obj = [];
        $obj[] = $raid;

        $json = $this->raidService->raidsArrayToJson($obj);

        self::assertEquals('[{"id":1,"name":"Raid test","date":null,"address":"address","addressAddition":"address addition","postCode":"22300","city":"city","editionNumber":2,"picture":null,"uniqid":"'.$uniqid.'"}]', $json);
    }

}
