<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 27/09/2018
 * Time: 17:08
 */

namespace AppBundle\Entity;

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
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @ORM\Column(name="is_checked_in", type="boolean")
     */
    protected $isCheckedIn;

    /**
     * @ORM\Column(name="poi_id", type="integer")
     */
    protected $poiId;

    /**
     * @ORM\Column(name="favorite_poi_type_id", type="integer")
     */
    protected $favoritePoiTypeId;

    /**
     * @ORM\Column(name="check_in_time", type="date")
     */
    protected $checkInTime;

    /**
     * Helper constructor.
     * @param $userId
     * @param $isCheckedIn
     * @param $poiId
     * @param $favoritePoiTypeId
     * @param $checkInTime
     */
    public function __construct($userId, $isCheckedIn, $poiId, $favoritePoiTypeId, $checkInTime)
    {
        $this->userId = $userId;
        $this->isCheckedIn = $isCheckedIn;
        $this->poiId = $poiId;
        $this->favoritePoiTypeId = $favoritePoiTypeId;
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
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
     * @return mixed
     */
    public function getPoiId()
    {
        return $this->poiId;
    }

    /**
     * @param mixed $poiId
     */
    public function setPoiId($poiId)
    {
        $this->poiId = $poiId;
    }

    /**
     * @return mixed
     */
    public function getFavoritePoiTypeId()
    {
        return $this->favoritePoiTypeId;
    }

    /**
     * @param mixed $favoritePoiTypeId
     */
    public function setFavoritePoiTypeId($favoritePoiTypeId)
    {
        $this->favoritePoiTypeId = $favoritePoiTypeId;
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