<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/12/18
 * Time: 08:58
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="race_timing")
 */
class RaceTiming
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="start_time", type="datetime", nullable=false)
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RaceCheckpoint")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checkpoint;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Competitor")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competitor;

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
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getCheckpoint()
    {
        return $this->checkpoint;
    }

    /**
     * @param mixed $checkpoint
     */
    public function setCheckpoint($checkpoint)
    {
        $this->checkpoint = $checkpoint;
    }

    /**
     * @return mixed
     */
    public function getCompetitor()
    {
        return $this->competitor;
    }

    /**
     * @param mixed $competitor
     */
    public function setCompetitor($competitor)
    {
        $this->competitor = $competitor;
    }
}
