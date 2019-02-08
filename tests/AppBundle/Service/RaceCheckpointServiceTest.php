<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 08/02/19
 * Time: 13:31
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Poi;
use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTrack;
use AppBundle\Service\RaceCheckpointService;
use PHPUnit\Framework\TestCase;

class RaceCheckpointServiceTest extends TestCase
{

    private $container;

    /** @var RaceCheckpointService $raceCheckpointService */
    private $raceCheckpointService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        /** @var RaceCheckpointService raceCheckpointService */
        $this->raceCheckpointService = $this->container->get('RaceCheckpointService');
    }

    public function testRaceCheckpointToObj()
    {
        $raceTrack = new RaceTrack();
        $poi       = new Poi();

        $raceCheckpoint = new RaceCheckpoint();
        $raceCheckpoint->setRaceTrack($raceTrack);
        $raceCheckpoint->setOrder(0);
        $raceCheckpoint->setPoi($poi);
        $raceCheckpoint->setId(1);

        $obj = $this->raceCheckpointService->raceCheckpointToObj($raceCheckpoint);

        self::assertEquals(1, $raceCheckpoint->getId());
        self::assertEquals(0, $raceCheckpoint->getOrder());
        self::assertEquals($poi, $raceCheckpoint->getPoi());
        self::assertEquals($raceTrack, $raceCheckpoint->getRaceTrack());
    }

    public function testRaceCheckpointFromArray()
    {
        $raceTrack = new RaceTrack();
        $poi       = new Poi();
        $arr = [
            'id' => 1,
            'order' => 0,
        ];

        $raceCheckpoint = $this->raceCheckpointService->raceCheckpointFromArray($arr, $raceTrack, $poi);

        self::assertEquals(1 , $raceCheckpoint->getId());
        self::assertEquals(0 , $raceCheckpoint->getOrder());
        self::assertEquals($poi, $raceCheckpoint->getPoi());
        self::assertEquals($raceTrack, $raceCheckpoint->getRaceTrack());
    }


}