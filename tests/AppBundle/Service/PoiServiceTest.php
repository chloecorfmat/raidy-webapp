<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 09:08
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Poi;
use AppBundle\Entity\PoiType;
use AppBundle\Entity\Raid;
use AppBundle\Entity\User;

use AppBundle\Service\PoiService;
use PHPUnit\Framework\TestCase;

class PoiServiceTest extends TestCase
{
    private $container;

    /** @var PoiService $poiService */
    private $poiService;

    public function __construct()
    {
        parent::__construct(null, [], '');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->poiService = $this->container->get('PoiService');
    }

    public function testPoiToJSON()
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
        $poi->setDescription("Test description");
        $poi->setImage(null);

        $json = $this->poiService->poiToJson($poi);
        $this->assertEquals('{"id":1,"name":"POI","longitude":"0","latitude":"0","requiredHelpers":"0","raid":1,"poiType":1,"description":"Test description","image":""}', $json);
    }

    public function testPoiArrayToJSON()
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
        $poi->setDescription("Test description");
        $poi->setImage(null);

        $pois = [];
        $pois[] = $poi;

        $json = $this->poiService->poisArrayToJson($pois);
        $this->assertEquals('[{"id":1,"name":"POI","longitude":"0","latitude":"0","requiredHelpers":"0","raid":1,"image":"","description":"Test description","poiType":1}]', $json);
    }

    public function testCheckDataArray()
    {
        $obj = [];
        $obj['name'] = "name";
        $obj['longitude'] = 1;
        $obj['latitude'] = 2;
        $obj['requiredHelpers'] = 0;
        $obj['poiType'] = 5;
        $obj['description'] = "Test description";
        $obj['image'] = null;

        $check = $this->poiService->checkDataArray($obj, true);
        $this->assertFalse($check);
        $check = $this->poiService->checkDataArray($obj, false);
        $this->assertTrue($check);

        $obj['id'] = 1;
        $check = $this->poiService->checkDataArray($obj, true);
        $this->assertTrue($check);
    }

}
