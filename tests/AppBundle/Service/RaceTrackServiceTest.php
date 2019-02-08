<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 08/02/19
 * Time: 14:20
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTrack;
use AppBundle\Entity\Track;
use AppBundle\Service\RaceService;
use AppBundle\Service\RaceTrackService;
use PHPUnit\Framework\TestCase;

class RaceTrackServiceTest extends TestCase
{
    private $container;

    /** @var RaceTrackService $raceTrackService */
    private $raceTrackService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->raceTrackService = $this->container->get('RaceTrackService');
    }

    public function testRaceTrackToObj(){
        $race = new Race();
        $race->setId(2);

        $track = new Track();
        $track->setName("TrackName");

        $raceTrack = new RaceTrack();
        $raceTrack->setRace($race);
        $raceTrack->setTrack($track);
        $raceTrack->setCheckpoints([]);
        $raceTrack->setId(1);
        $raceTrack->setOrder(0);

        $obj = $this->raceTrackService->raceTrackToObj($raceTrack);

        self::assertEquals(1, $obj['id']);
        self::assertEquals(0, $obj['order']);
        self::assertEquals(2, $obj['race']);
    }

    public function testRaceTrackFromArray()
    {
        $arr = [];
        $arr['id'] = 1;
        $arr['order'] = 0;
        $arr['checkpoints'] = [];

        $race = new Race();
        $race->setId(2);

        $track = new Track();
        $track->setName("TrackName");

        $raceTrack = $this->raceTrackService->raceTrackFromArray($arr, $race, $track);

        self::assertEquals(1, $raceTrack->getId());
        self::assertEquals(0, $raceTrack->getOrder());
        self::assertEquals($race, $raceTrack->getRace());
        self::assertEquals($track, $raceTrack->getTrack());
        self::assertEquals([], $raceTrack->getCheckpoints());
    }

    public function testEmptyRaceTrackFromArray()
    {
        $arr = [];
        $arr['id'] = 1;
        $arr['order'] = 0;

        $race = new Race();
        $race->setId(2);

        $track = new Track();
        $track->setName("TrackName");

        $raceTrack = $this->raceTrackService->emptyRaceTrackFromArray($arr, $race, $track);

        self::assertEquals(1, $raceTrack->getId());
        self::assertEquals(0, $raceTrack->getOrder());
        self::assertEquals($race, $raceTrack->getRace());
        self::assertEquals($track, $raceTrack->getTrack());
        self::assertEquals([], $raceTrack->getCheckpoints());
    }
}