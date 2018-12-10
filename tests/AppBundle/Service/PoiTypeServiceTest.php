<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 09:27
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\PoiType;
use AppBundle\Entity\Raid;
use AppBundle\Entity\User;
use AppBundle\Service\PoiTypeService;
use PHPUnit\Framework\TestCase;

class PoiTypeServiceTest extends TestCase
{
    private $container;

    /** @var PoiTypeService $poiTypeService */
    private $poiTypeService;

    public function __construct()
    {
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->poiTypeService = $this->container->get('PoiTypeService');
    }

    public function testPoiTypesArrayToJson()
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

        $obj = [];
        $obj[] = $poiType;

        $json = $this->poiTypeService->poiTypesArrayToJson($obj);
        $this->assertEquals('[{"id":1,"type":"TESTPOITYPE","color":"#F5F5F5","user":1}]', $json);
    }
}