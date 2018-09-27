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
     * @ORM\Column(name="track_id", type="integer")
     */
    protected $trackId;

    /**
     * @ORM\Column(name="poi_type_id", type="integer")
     */
    protected $poiTypeId;

    /**
     * Poi constructor.
     * @param $location
     * @param $requiredHelpers
     * @param $trackId
     * @param $poiTypeId
     */
    public function __construct($location, $requiredHelpers, $trackId, $poiTypeId)
    {
        $this->location = $location;
        $this->requiredHelpers = $requiredHelpers;
        $this->trackId = $trackId;
        $this->poiTypeId = $poiTypeId;
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
     * @return mixed
     */
    public function getTrackId()
    {
        return $this->trackId;
    }

    /**
     * @param mixed $trackId
     */
    public function setTrackId($trackId)
    {
        $this->trackId = $trackId;
    }

    /**
     * @return mixed
     */
    public function getPoiTypeId()
    {
        return $this->poiTypeId;
    }

    /**
     * @param mixed $poiTypeId
     */
    public function setPoiTypeId($poiTypeId)
    {
        $this->poiTypeId = $poiTypeId;
    }




}