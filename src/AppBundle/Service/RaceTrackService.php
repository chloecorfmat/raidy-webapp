<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 27/12/18
 * Time: 22:29
 */

namespace AppBundle\Service;

use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTrack;
use AppBundle\Entity\Track;

class RaceTrackService
{

    private $raceCheckpointService;

    /**
     * RaceTrackService constructor.
     * @param RaceCheckpointService $raceCheckpointService
     */
    public function __construct(RaceCheckpointService $raceCheckpointService)
    {
        $this->raceCheckpointService = $raceCheckpointService;
    }

    /**
     * @param RaceTrack $raceTrack
     *
     * @return array
     */
    public function raceTrackToObj($raceTrack)
    {
        $racesTrackObj          = [];
        $racesTrackObj['id']    = $raceTrack->getId();
        $racesTrackObj['name']    = $raceTrack->getTrack()->getName();
        $racesTrackObj['order'] = $raceTrack->getOrder();
        $racesTrackObj['race']  = $raceTrack->getRace()->getId();

        $racesTrackObj['checkpoints'] = [];

        /** @var RaceCheckpoint $checkpoint */
        foreach ($raceTrack->getCheckpoints() as $checkpoint) {
            $racesTrackObj['checkpoints'][$checkpoint->getOrder()] =
                $this->raceCheckpointService->raceCheckpointToObj($checkpoint);
        }

        return $racesTrackObj;
    }

    /**
     * @param mixed $arr
     * @param Race  $race
     * @param Track $track
     * @return RaceTrack
     */
    public function raceTrackFromArray($arr, $race, $track)
    {
        $raceTrack = new RaceTrack();

        $raceTrack->setId($arr['id']);
        $raceTrack->setOrder($arr['order']);
        $raceTrack->setRace($race);
        $raceTrack->setTrack($track);

        $checkpoints = $arr['checkpoints'];
        $checkpointsArr = [];

        /** @var RaceCheckpoint $checkpoint */
        foreach ($checkpoints as $checkpoint) {
            $checkpointsArr[] = $this->raceCheckpointService->raceCheckpointFromArray($checkpoint);
        }
        $raceTrack->setCheckpoints($checkpointsArr);

        return $raceTrack;
    }

    /**
     * @param mixed $arr
     * @param Race  $race
     * @param Track $track
     * @return RaceTrack
     */
    public function emptyRaceTrackFromArray($arr, $race, $track)
    {
        $raceTrack = new RaceTrack();

        $raceTrack->setId($arr['id']);
        $raceTrack->setOrder($arr['order']);
        $raceTrack->setRace($race);
        $raceTrack->setTrack($track);

        $checkpointsArr = [];
        $raceTrack->setCheckpoints($checkpointsArr);

        return $raceTrack;
    }

    /**
     * @param mixed $arr
     * @param int   $checkId
     * @return bool
     */
    public function checkDataArray($arr, $checkId)
    {
        return true;
    }
}
