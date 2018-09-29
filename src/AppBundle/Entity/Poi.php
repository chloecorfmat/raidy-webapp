<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 27/09/2018
 * Time: 17:09
 */

namespace AppBundle\Entity;

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
     * @param $location
     * @param $requiredHelpers
     * @param $track
     * @param $poiType
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