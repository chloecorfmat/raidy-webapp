<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 08/02/19
 * Time: 13:48
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\Race;
use AppBundle\Entity\RaceTrack;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Track;
use AppBundle\Service\RaceService;
use PHPUnit\Framework\TestCase;

class RaceServiceTest extends TestCase
{
    private $container;

    /** @var RaceService $raceService */
    private $raceService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->raceService = $this->container->get('RaceService');
    }

    public function testRaceToJson(){

        $raid = new Raid();
        $race = new Race();
        $raceTrack = new RaceTrack();

        $raceTrack->setId(1);
        $raceTrack->setOrder(0);
        $raceTrack->setCheckpoints([]);
        $raceTrack->setTrack(new Track());
        $raceTrack->setRace($race);

        $startTime = new \DateTime();
        $jsonStartTime = json_encode($startTime);

        $endTime = new \DateTime();
        $jsonEndTime = json_encode($endTime);


        $race->setId(1);
        $race->setStartTime($startTime);
        $race->setEndTime($endTime);
        $race->setTracks([$raceTrack]);
        $race->setRaid($raid);
        $race->setName("RaidTest");

        $json = $this->raceService->raceToJson($race);
        self::assertEquals("{\"id\":1,\"name\":\"RaidTest\",\"startTime\":$jsonStartTime,\"endTime\":$jsonEndTime,\"raid\":null,\"tracks\":[{\"id\":1,\"name\":null,\"order\":0,\"race\":1,\"checkpoints\":[]}]}", $json);
    }

    public function testRacesArrayToJson()
    {
        $raid = new Raid();
        $race = new Race();
        $race2 = new Race();
        $raceTrack = new RaceTrack();

        $raceTrack->setId(1);
        $raceTrack->setOrder(0);
        $raceTrack->setCheckpoints([]);
        $raceTrack->setTrack(new Track());
        $raceTrack->setRace($race);

        $startTime = new \DateTime();
        $jsonStartTime = json_encode($startTime);

        $endTime = new \DateTime();
        $jsonEndTime = json_encode($endTime);

        $race->setId(1);
        $race->setStartTime($startTime);
        $race->setEndTime($endTime);
        $race->setTracks([$raceTrack]);
        $race->setRaid($raid);
        $race->setName("RaidTest");

        /* ### */

        $race2->setId(1);
        $race2->setStartTime($startTime);
        $race2->setEndTime($endTime);
        $race2->setTracks([$raceTrack]);
        $race2->setRaid($raid);
        $race2->setName("RaidTest");

        $races = [$race, $race2];

        $json = $this->raceService->racesArrayToJson($races);

        self::assertEquals("[{\"id\":1,\"name\":\"RaidTest\",\"startTime\":$jsonStartTime,\"endTime\":$jsonEndTime,\"raid\":null,\"tracks\":[{\"id\":1,\"name\":null,\"order\":0,\"race\":1,\"checkpoints\":[]}]},{\"id\":1,\"name\":\"RaidTest\",\"startTime\":$jsonStartTime,\"endTime\":$jsonEndTime,\"raid\":null,\"tracks\":[{\"id\":1,\"name\":null,\"order\":0,\"race\":1,\"checkpoints\":[]}]}]", $json);
    }

    public function testEmptyRaceFromArray()
    {
        $raid = new Raid();

        $startTime = new \DateTime();
        $jsonStartTime = json_encode($startTime);

        $raceArr = [];
        $raceArr['id'] = 1;
        $raceArr['name'] = "RaidTest";
        $raceArr['startTime'] = $startTime;

        $race = $this->raceService->emptyRaceFromArray($raceArr, $raid);

        self::assertEquals("1", $race->getId());
        self::assertEquals("RaidTest", $race->getName());
        self::assertEquals($startTime, $race->getStartTime());
    }

}