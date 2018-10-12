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
 * @ORM\Table(name="poi")
 */
class Poi
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $location;

    /**
     * @ORM\Column(name="required_helpers", type="integer")
     */
    protected $requiredHelpers;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Track")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $track;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PoiType")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $poiType;

    /**
     * Poi constructor.
     *
     * @param mixed $location        location
     * @param mixed $requiredHelpers required helpers
     * @param mixed $track           track
     * @param mixed $poiType         POI type
     */
    public function __construct($location, $requiredHelpers, $track, $poiType)
    {
        $this->location = $location;
        $this->requiredHelpers = $requiredHelpers;
        $this->track = $track;
        $this->poiType = $poiType;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getRequiredHelpers()
    {
        return $this->requiredHelpers;
    }

    /**
     * @param mixed $requiredHelpers
     */
    public function setRequiredHelpers($requiredHelpers)
    {
        $this->requiredHelpers = $requiredHelpers;
    }

    /**
     * @return Track
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param Track $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return PoiType
     */
    public function getPoiType()
    {
        return $this->poiType;
    }

    /**
     * @param PoiType $poiType
     */
    public function setPoiType($poiType)
    {
        $this->poiType = $poiType;
    }
}
