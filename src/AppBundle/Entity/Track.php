<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 27/09/2018
 * Time: 17:09
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="track")
 */
class Track
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="track_points", type="string", length=45)
     */
    protected $trackPoints;

    /**
     * @ORM\Column(name="raid_id", type="integer")
     */
    protected $raidId;

    /**
     * @ORM\Column(name="sport_type_id", type="string", length=45)
     */
    protected $sportTypeId;

    /**
     * Track constructor.
     * @param $trackPoints
     * @param $raidId
     * @param $sportTypeId
     */
    public function __construct($trackPoints, $raidId, $sportTypeId)
    {
        $this->trackPoints = $trackPoints;
        $this->raidId = $raidId;
        $this->sportTypeId = $sportTypeId;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTrackPoints()
    {
        return $this->trackPoints;
    }

    /**
     * @param mixed $trackPoints
     */
    public function setTrackPoints($trackPoints)
    {
        $this->trackPoints = $trackPoints;
    }

    /**
     * @return mixed
     */
    public function getRaidId()
    {
        return $this->raidId;
    }

    /**
     * @param mixed $raidId
     */
    public function setRaidId($raidId)
    {
        $this->raidId = $raidId;
    }

    /**
     * @return mixed
     */
    public function getSportTypeId()
    {
        return $this->sportTypeId;
    }

    /**
     * @param mixed $sportTypeId
     */
    public function setSportTypeId($sportTypeId)
    {
        $this->sportTypeId = $sportTypeId;
    }


}