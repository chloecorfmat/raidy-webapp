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
 * @ORM\Table(name="race_checkpoint")
 */
class RaceCheckpoint
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Poi")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $poi;

    /**
     * @ORM\ManyToOne(targetEntity=RaceTrack::class, inversedBy="checkpoints")
     */
    protected $raceTrack;

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
    public function getPoi()
    {
        return $this->poi;
    }

    /**
     * @param mixed $poi
     */
    public function setPoi($poi)
    {
        $this->poi = $poi;
    }

    /**
     * @return mixed
     */
    public function getRaceTrack()
    {
        return $this->raceTrack;
    }

    /**
     * @param mixed $raceTrack
     */
    public function setRaceTrack($raceTrack)
    {
        $this->raceTrack = $raceTrack;
    }
}
