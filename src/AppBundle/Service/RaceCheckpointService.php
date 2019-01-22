<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 27/12/18
 * Time: 22:29
 */

namespace AppBundle\Service;

use AppBundle\Entity\Poi;
use AppBundle\Entity\RaceCheckpoint;
use AppBundle\Entity\RaceTrack;

class RaceCheckpointService
{
    /**
     * @param RaceCheckpoint $raceCheckpoint
     *
     * @return array
     */
    public function raceCheckpointToObj($raceCheckpoint)
    {
        $raceCheckpointObj                = [];
        $raceCheckpointObj['id']          = $raceCheckpoint->getId();
        $raceCheckpointObj['order']       = $raceCheckpoint->getOrder();

        $raceCheckpointObj['poi']         = [];
        $raceCheckpointObj['poi']['id']   = $raceCheckpoint->getPoi()->getId();
        $raceCheckpointObj['poi']['name'] = $raceCheckpoint->getPoi()->getName();

        $raceCheckpointObj['raceTrack']   = $raceCheckpoint->getRaceTrack()->getId();

        return $raceCheckpointObj;
    }

    /**
     * @param mixed     $arr
     * @param RaceTrack $raceTrack
     * @param Poi       $poi
     *
     * @return RaceCheckpoint
     */
    public function raceCheckpointFromArray($arr, $raceTrack, $poi)
    {
        $raceCheckpoint = new RaceCheckpoint();

        $raceCheckpoint->setId($arr['id']);
        $raceCheckpoint->setOrder($arr['order']);
        $raceCheckpoint->setPoi($poi);
        $raceCheckpoint->setRaceTrack($raceTrack);

        return $raceCheckpoint;
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
