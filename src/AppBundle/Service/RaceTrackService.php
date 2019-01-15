<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 27/12/18
 * Time: 22:29
 */

namespace AppBundle\Service;


use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTrack;

class RaceTrackService
{

    private $raceCheckpointService;

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
        foreach ($raceTrack->getCheckpoints() as $checkpoint){
            $racesTrackObj['checkpoints'][$checkpoint->getOrder()] = $this->raceCheckpointService->raceCheckpointToObj($checkpoint);
        }

        return $racesTrackObj;
    }

    public function raceTrackFromArray($arr, $race, $track){
        $raceTrack = new RaceTrack();

        $raceTrack->setId($arr['id']);
        $raceTrack->setOrder($arr['order']);
        $raceTrack->setRace($race);
        $raceTrack->setTrack($track);

        $checkpoints = $arr['checkpoints'];
        $CheckpointsArr = [];

        /** @var RaceCheckpoint $checkpoint */
        foreach ($checkpoints as $checkpoint) {
            $CheckpointsArr[] = $this->raceCheckpointService->raceCheckpointFromArray($checkpoint);
        }
        $raceTrack->setCheckpoints($CheckpointsArr);

        return $raceTrack;
    }

    public function emptyRaceTrackFromArray($arr, $race, $track){
        $raceTrack = new RaceTrack();

        $raceTrack->setId($arr['id']);
        $raceTrack->setOrder($arr['order']);
        $raceTrack->setRace($race);
        $raceTrack->setTrack($track);

        $CheckpointsArr = [];
        $raceTrack->setCheckpoints($CheckpointsArr);

        return $raceTrack;
    }

    /**
     * @param $arr
     * @return bool
     */
    public function checkDataArray($arr, $checkId){
        return true;
    }
}