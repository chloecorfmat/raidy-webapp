<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 17/10/18
 * Time: 13:44.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;

class TrackService
{
    /**
     * TrackService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $obj
     * @param mixed $raidId
     *
     * @return Track
     */
    public function trackFromArray($obj, $raidId)
    {
        $track = new Track();

        if ('' != $obj['id']) {
            $track->setId($obj['id']);
        }

        $track->setName($obj['name']);
        $track->setIsVisible(boolval($obj['isVisible']));

        $track->setColor($obj['color']);
        $track->setTrackPoints($obj['trackpoints']);

        $sportRepository = $this->em->getRepository('AppBundle:SportType');
        $sport = $sportRepository->find($obj['sportType']);
        $track->setSportType($sport);

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $track->setRaid($raid);

        return $track;
    }

    /**
     * @param Track $track
     *
     * @return false|string
     */
    public function trackToJson($track)
    {
        $obj = [];

        $obj['id'] = $track->getId();
        $obj['name'] = $track->getName();
        $obj['color'] = $track->getColor();
        $obj['raid'] = $track->getRaid()->getId();

        if (null != $track->getSportType()) {
            $obj['sportType'] = $track->getSportType()->getId();
        } else {
            $obj['sportType'] = '';
        }

        $obj['trackpoints'] = $track->getTrackpoints();
        $obj['isVisible'] = $track->getisVisible();

        return json_encode($obj);
    }

    /**
     * @param array $tracks
     *
     * @return false|string
     */
    public function tracksArrayToJson($tracks)
    {
        $tracksObj = [];

        foreach ($tracks as $track) {
            $obj = [];

            $obj['id'] = $track->getId();
            $obj['name'] = $track->getName();
            $obj['color'] = $track->getColor();
            $obj['raid'] = $track->getRaid()->getId();

            if (null != $track->getSportType()) {
                $obj['sportType'] = $track->getSportType()->getId();
            } else {
                $obj['sportType'] = '';
            }

            $obj['trackpoints'] = $track->getTrackpoints();
            $obj['isVisible'] = $track->getisVisible();

            $tracksObj[] = $obj;
        }

        return json_encode($tracksObj);
    }

    /**
     * @param Track $track
     * @param int   $raidId
     * @param mixed $obj
     *
     * @return mixed
     */
    public function updateTrackFromArray($track, $raidId, $obj)
    {
        $track->setName($obj['name']);
        $track->setIsVisible(boolval($obj['isVisible']));

        $track->setColor($obj['color']);
        $track->setTrackPoints($obj['trackpoints']);

        $sportRepository = $this->em->getRepository('AppBundle:SportType');
        $sport = $sportRepository->find($obj['sportType']);
        $track->setSportType($sport);

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $track->setRaid($raid);

        return $track;
    }

    /**
     * @param mixed $obj
     * @param bool  $checkId
     *
     * @return bool
     */
    public function checkDataArray($obj, $checkId)
    {
        $status = true;

        if ($checkId) {
            if (null == $obj['id'] || '' == $obj['id']) {
                $status = false;
            }
        }

        if (null == $obj['name'] || '' == $obj['name']) {
            $status = false;
        }

        if (null == $obj['color'] || '' == $obj['color']) {
            $status = false;
        }

        if (null == $obj['sportType'] || '' == $obj['sportType']) {
            $status = false;
        }

        if (null == $obj['trackpoints'] || '' == $obj['trackpoints']) {
            $status = false;
        }

        if (null == $obj['isVisible'] || '' == $obj['isVisible']) {
            $status = false;
        }

        return $status;
    }
}
