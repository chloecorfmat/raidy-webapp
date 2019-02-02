<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 27/12/18
 * Time: 14:31
 */

namespace AppBundle\Service;

use AppBundle\Entity\Race;
use AppBundle\Entity\RaceTrack;
use AppBundle\Entity\Raid;
use Doctrine\ORM\PersistentCollection;

class RaceService
{

    private $raceTrackService;

    /**
     * RaceService constructor.
     * @param RaceTrackService $raceTrackService
     */
    public function __construct(RaceTrackService $raceTrackService)
    {
        $this->raceTrackService = $raceTrackService;
    }

    /**
     * @param Race $race
     * @return false|string
     */
    public function raceToJson($race)
    {
        $obj              = [];
        $obj['id']        = $race->getId();
        $obj['name']      = $race->getName();
        $obj['startTime'] = $race->getStartTime();
        $obj['endTime']   = $race->getEndTime();
        $obj['raid']      = $race->getRaid()->getId();

        $tracks           = $race->getTracks();
        $obj['tracks']    = [];

        /** @var RaceTrack $track */
        foreach ($tracks as $track) {
            $obj['tracks'][] = $this->raceTrackService->raceTrackToObj($track);
        }

        return json_encode($obj);
    }

    /**
     * @param array $races
     *
     * @return false|string
     */
    public function racesArrayToJson($races)
    {
        $racesObj = [];

        /** @var Race $race */
        foreach ($races as $race) {
            $obj              = [];
            $obj['id']        = $race->getId();
            $obj['name']      = $race->getName();
            $obj['startTime'] = $race->getStartTime();
            $obj['endTime']   = $race->getEndTime();
            $obj['raid']      = $race->getRaid()->getId();

            /** @var PersistentCollection $tracks */
            $tracks           = $race->getTracks();
            $obj['tracks']    = [];

            /** @var RaceTrack $track */
            foreach ($tracks as $track) {
                $obj['tracks'][$track->getOrder()] = $this->raceTrackService->raceTrackToObj($track);
            }

            $racesObj[] = $obj;
        }

        return json_encode($racesObj);
    }

    /**
     * @param mixed $raceArr
     * @param Raid  $raid
     * @return Race
     */
    public function raceFromArray($raceArr, $raid)
    {
        $race = new Race();

        $race->setId($raceArr['id']);
        $race->setName($raceArr['name']);
        $race->setRaid($raceArr[$raid]);
        $race->setStartTime($raceArr['starTime']);

        $tracks = $raceArr['tracks'];
        $tracksArr = [];
        foreach ($tracks as $track) {
            $tracksArr[] = $this->raceTrackService->raceTrackFromArray($track);
        }

        $race->setTracks($tracksArr);

        return $race;
    }

    /**
     * @param mixed $raceArr
     * @param Raid  $raid
     * @return Race
     */
    public function emptyRaceFromArray($raceArr, $raid)
    {
        $race = new Race();

        $race->setId($raceArr['id']);
        $race->setName($raceArr['name']);
        $race->setRaid($raid);
        $race->setStartTime($raceArr['startTime']);

        $tracksArr = [];
        $race->setTracks($tracksArr);

        return $race;
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
