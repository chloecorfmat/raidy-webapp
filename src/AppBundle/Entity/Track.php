<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $raid;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SportType")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $sportType;

    /**
     * Track constructor.
     *
     */
    public function __construct()
    {
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
     * @return Raid
     */
    public function getRaid()
    {
        return $this->raid;
    }

    /**
     * @param Raid $raid
     */
    public function setRaid($raid)
    {
        $this->raid = $raid;
    }

    /**
     * @return SportType
     */
    public function getSportType()
    {
        return $this->sportType;
    }

    /**
     * @param SportType $sportType
     */
    public function setSportType($sportType)
    {
        $this->sportType = $sportType;
    }
}
