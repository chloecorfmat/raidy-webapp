<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 29/10/18
 * Time: 11:16
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Raid;
use AppBundle\Entity\SportType;
use AppBundle\Entity\Track;
use AppBundle\Service\TrackService;
use PHPUnit\Framework\TestCase;

class TrackServiceTest extends TestCase
{
    private $container;

    /** @var TrackService $trackService */
    private $trackService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->trackService = $this->container->get('TrackService');
    }

    public function testTrackFromArray()
    {
        $obj = [];
        $obj['id'] = 1;
        $obj['name'] = "trackname";
        $obj['color'] = "#f5f5f5";
        $obj['raid'] = 2;
        $obj['sportType'] = 3;
        $obj['trackpoints'] = [];
        $obj['isVisible'] = false;
        $obj['isCalibration'] = false;

        $track = $this->trackService->trackFromArray($obj, 2);

        $this->assertEquals(1, $track->getId());
        $this->assertEquals("trackname", $track->getName());
        $this->assertEquals("#f5f5f5", $track->getColor());
        /* Can't test raid id, service use database */
        /* Can't test sport type, service use database */
        $this->assertEquals(0, count($track->getTrackPoints()));
        $this->assertEquals(false, $track->getisVisible());
    }


    public function testTrackToJson(){
        $track = new Track();
        $track->setId(1);
        $track->setName("trackname");
        $track->setColor("#f2f2f2");

        $raid = new Raid();
        $raid->setId(2);
        $track->setRaid($raid);

        $st = new SportType("testSport", "noicon");
        $st->setId(3);
        $track->setSportType($st);

        $track->setTrackPoints([]);
        $track->setIsVisible(true);

        $json = $this->trackService->trackToJson($track);

        $this->assertEquals('{"id":1,"name":"trackname","color":"#f2f2f2","raid":2,"sportType":3,"trackpoints":[],"isVisible":true,"isCalibration":false}', $json);
    }

    public function testTracksArrayToJson(){
        $track = new Track();
        $track->setId(1);
        $track->setName("trackname");
        $track->setColor("#f2f2f2");

        $raid = new Raid();
        $raid->setId(2);
        $track->setRaid($raid);

        $st = new SportType("testSport", "noicon");
        $st->setId(3);
        $track->setSportType($st);

        $track->setTrackPoints([]);
        $track->setIsVisible(true);

        $tracks   = [];
        $tracks[] = $track;

        $json = $this->trackService->tracksArrayToJson($tracks);

        $this->assertEquals('[{"id":1,"name":"trackname","color":"#f2f2f2","raid":2,"sportType":3,"trackpoints":[],"isVisible":true,"isCalibration":false}]', $json);
    }

    public function testCheckDataArray(){
        $obj = [];
        $obj['id'] = 1;
        $obj['name'] = "trackname";
        $obj['color'] = "#f5f5f5";
        $obj['raid'] = 2;
        $obj['sportType'] = 3;
        $obj['trackpoints'] = [];
        $obj['isVisible'] = false;
        $obj['isCalibration'] = false;
        $check = $this->trackService->checkDataArray($obj, true);
        $this->assertTrue($check);

        $obj = [];
        $obj['name'] = "trackname";
        $obj['color'] = "#f5f5f5";
        $obj['raid'] = 2;
        $obj['sportType'] = 3;
        $obj['trackpoints'] = [];
        $obj['isVisible'] = false;
        $obj['isCalibration'] = false;
        $check = $this->trackService->checkDataArray($obj, true);
        $this->assertFalse($check);

        $obj = [];
        $obj['id'] = 1;
        $obj['color'] = "#f5f5f5";
        $obj['raid'] = 2;
        $obj['sportType'] = 3;
        $obj['trackpoints'] = [];
        $obj['isVisible'] = false;
        $obj['isCalibration'] = false;
        $check = $this->trackService->checkDataArray($obj, true);
        $this->assertFalse($check);

        $obj = [];
        $obj['id'] = 1;
        $obj['name'] = '';
        $obj['color'] = "#f5f5f5";
        $obj['raid'] = 2;
        $obj['sportType'] = 3;
        $obj['trackpoints'] = [];
        $obj['isVisible'] = false;
        $obj['isCalibration'] = false;
        $check = $this->trackService->checkDataArray($obj, true);
        $this->assertFalse($check);

    }
}