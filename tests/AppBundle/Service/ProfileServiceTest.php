<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 09:36
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Service\ProfileService;
use PHPUnit\Framework\TestCase;

class ProfileServiceTest extends TestCase
{
    private $container;

    /** @var ProfileService $profileService */
    private $profileService;

    public function __construct()
    {
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->profileService = $this->container->get('ProfileService');
    }

    public function testProfileToJSON()
    {
        $user = new User();
        $user->setId(1);
        $user->setLastName("LastName");
        $user->setFirstName("FirstName");
        $user->setUsername("Username");
        $user->setEmail("test@test.fr");
        $user->setPhone("0613865838");

        $json = $this->profileService->profileToJson($user);
        $this->assertEquals('{"id":1,"username":"Username","firstname":"FirstName","lastname":"LastName","email":"test@test.fr","phone":"0613865838"}', $json);
    }

    public function testUpdateProfileFromArray()
    {
        $user = new User();
        $user->setId(1);
        $user->setLastName("LastName");
        $user->setFirstName("FirstName");
        $user->setUsername("Username");
        $user->setEmail("test@test.fr");
        $user->setPhone("0613865838");

        $obj = [];
        $obj['id'] = 1;
        $obj['lastname'] = "LastName2";
        $obj['firstname'] = "FirstName2";
        $obj['username'] = "Username2";
        $obj['email'] = "test@test.fr2";
        $obj['phone'] = "06138658382";

        /** @var User $user **/
        $user = $this->profileService->updateProfileFromArray($user, $obj);

        $this->assertEquals(1, $user->getId());
        $this->assertEquals("LastName2", $user->getLastName());
        $this->assertEquals("FirstName2", $user->getFirstName());
        $this->assertEquals("Username2", $user->getUsername());
        $this->assertEquals("test@test.fr2", $user->getEmail());
        $this->assertEquals("06138658382", $user->getPhone());
    }
}