<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/12/18
 * Time: 08:40
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="race_track")
 */
class RaceTrack
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="orderIdx", type="integer")
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Track")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $track;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Race", inversedBy="tracks")
     */
    protected $race;

    /**
     * @ORM\OneToMany(targetEntity=RaceCheckpoint::class, cascade={"persist", "remove"}, mappedBy="raceTrack")
     */
    protected $checkpoints = [];

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
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param mixed $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @param mixed $race
     */
    public function setRace($race)
    {
        $this->race = $race;
    }

    /**
     * @return mixed
     */
    public function getCheckpoints()
    {
        return $this->checkpoints;
    }

    /**
     * @param mixed $checkpoints
     */
    public function setCheckpoints($checkpoints)
    {
        $this->checkpoints = $checkpoints;
    }

    /**
     * @param mixed $tracks
     */
    public function addCheckpoint($checkpoint)
    {
        $this->checkpoints[] = $checkpoint;
        $checkpoint->setRaceTrack($this);
    }
}
