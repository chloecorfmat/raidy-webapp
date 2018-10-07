<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="helper")
 */
class Helper
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(name="is_checked_in", type="boolean")
     */
    protected $isCheckedIn;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Poi")
     */
    protected $poi;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PoiType")
     */
    protected $favoritePoiType;

    /**
     * @ORM\Column(name="check_in_time", type="date")
     */
    protected $checkInTime;

    /**
     * Helper constructor.
     *
     * @param mixed $user            user
     * @param mixed $isCheckedIn     is checked in
     * @param mixed $poi             POI
     * @param mixed $favoritePoiType favorite POI type
     * @param mixed $checkInTime     check-in time
     */
    public function __construct($user, $isCheckedIn, $poi, $favoritePoiType, $checkInTime)
    {
        $this->user = $user;
        $this->isCheckedIn = $isCheckedIn;
        $this->poi = $poi;
        $this->favoritePoiType = $favoritePoiType;
        $this->checkInTime = $checkInTime;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getisCheckedIn()
    {
        return $this->isCheckedIn;
    }

    /**
     * @param mixed $isCheckedIn
     */
    public function setIsCheckedIn($isCheckedIn)
    {
        $this->isCheckedIn = $isCheckedIn;
    }

    /**
     * @return Poi $poi
     */
    public function getPoi()
    {
        return $this->poi;
    }

    /**
     * @param Poi $poi
     */
    public function setPoi(Poi $poi = null)
    {
        $this->poi = $poi;
    }

    /**
     * @return PoiType $favoritePoiType
     */
    public function getFavoritePoiType()
    {
        return $this->favoritePoiType;
    }

    /**
     * @param PoiType $favoritePoiType
     */
    public function setFavoritePoiType($favoritePoiType = null)
    {
        $this->favoritePoiType = $favoritePoiType;
    }

    /**
     * @return mixed
     */
    public function getCheckInTime()
    {
        return $this->checkInTime;
    }

    /**
     * @param mixed $checkInTime
     */
    public function setCheckInTime($checkInTime)
    {
        $this->checkInTime = $checkInTime;
    }
}
