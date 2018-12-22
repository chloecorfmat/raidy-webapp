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
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=45, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le nom ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    protected $latitude;

    /**
     * @ORM\Column(name="required_helpers", type="integer")
     */
    protected $requiredHelpers = 0;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PoiType")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $poiType;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $raid;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isCheckpoint;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
     * @return mixed
     */
    public function getPoiType()
    {
        return $this->poiType;
    }

    /**
     * @param mixed $poiType
     */
    public function setPoiType($poiType)
    {
        $this->poiType = $poiType;
    }

    /**
     * @return mixed
     */
    public function getRaid()
    {
        return $this->raid;
    }

    /**
     * @param mixed $raid
     */
    public function setRaid($raid)
    {
        $this->raid = $raid;
    }

    /**
     * @return mixed
     */
    public function getisCheckpoint()
    {
        return $this->isCheckpoint;
    }

    /**
     * @param mixed $isCheckpoint
     */
    public function setIsCheckpoint($isCheckpoint)
    {
        $this->isCheckpoint = $isCheckpoint;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
