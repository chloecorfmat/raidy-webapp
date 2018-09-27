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
 * @ORM\Table(name="raid")
 */
class Raid
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
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $place;

    /**
     * @ORM\Column(name="edition_number", type="integer")
     */
    protected $editionNumber;

    /**
     * @ORM\Column(name="organizer_id", type="integer")
     */
    protected $organizerId;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $picture;

    /**
     * Raid constructor.
     * @param $name
     * @param $date
     * @param $place
     * @param $editionNumber
     * @param $organizerId
     * @param $picture
     */
    public function __construct($name, $date, $place, $editionNumber, $organizerId, $picture)
    {
        $this->name = $name;
        $this->date = $date;
        $this->place = $place;
        $this->editionNumber = $editionNumber;
        $this->organizerId = $organizerId;
        $this->picture = $picture;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * @return mixed
     */
    public function getEditionNumber()
    {
        return $this->editionNumber;
    }

    /**
     * @param mixed $editionNumber
     */
    public function setEditionNumber($editionNumber)
    {
        $this->editionNumber = $editionNumber;
    }

    /**
     * @return mixed
     */
    public function getOrganizerId()
    {
        return $this->organizerId;
    }

    /**
     * @param mixed $organizerId
     */
    public function setOrganizerId($organizerId)
    {
        $this->organizerId = $organizerId;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }
}