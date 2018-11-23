<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 08:52
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\Helper;
use AppBundle\Entity\Poi;
use AppBundle\Entity\PoiType;
use AppBundle\Entity\Raid;
use AppBundle\Entity\User;
use AppBundle\Service\HelperService;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Date;

class HelperServiceTest extends TestCase
{
    private $container;

    /** @var HelperService $helperService */
    private $helperService;

    public function __construct()
    {
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->helperService = $this->container->get('HelperService');
    }

    public function testHelperToJson()
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

        $poi = new Poi();
        $poi->setId(1);
        $poi->setRaid($raid);
        $poi->setName("POI");
        $poi->setLatitude(0);
        $poi->setLongitude(0);
        $poi->setPoiType($poiType);
        $poi->setRequiredHelpers(0);

        $helper = new Helper();
        $helper->setId(1);
        $helper->setRaid($raid);
        $helper->setUser($user);
        $helper->setFavoritePoiType($poiType);
        $helper->setIsCheckedIn(true);
        $helper->setCheckInTime(new DateTime("2018-11-23 08:01:35"));
        $helper->setPoi($poi);

        /*CHECKED-IN HELPER*/
        $json = $this->helperService->helperToJson($helper);
        $this->assertEquals('{"id":1,"user":1,"isCheckedIn":true,"poi":1,"favoritePoiType":1,"checkInTime":{"date":"2018-11-23 08:01:35.000000","timezone_type":3,"timezone":"UTC"},"raid":1}'
            ,$json);

        /*NOT CHECKED-IN HELPER*/
        $helper->setIsCheckedIn(false);
        $helper->setCheckInTime(null);
        $json = $this->helperService->helperToJson($helper);
        $this->assertEquals('{"id":1,"user":1,"isCheckedIn":false,"poi":1,"favoritePoiType":1,"checkInTime":"","raid":1}'
            ,$json);
    }

    public function testCheckDataAffectationArray()
    {
        $obj = [];
        $obj["poi"] = 1;
        $check = $this->helperService->checkDataAffectationArray($obj, false);
        $this->assertTrue($check);
    }

}