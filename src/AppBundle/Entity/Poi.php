<?php

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
     * @ORM\Column(type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $latitude;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
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
