<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 27/09/2018
 * Time: 17:08.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organizer")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $organizer;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $picture;

    /**
     * Raid constructor.
     *
     * @param string $name          name
     * @param mixed  $date          date
     * @param mixed  $place         place
     * @param int    $editionNumber edition number
     * @param mixed  $organizer     organizer
     * @param mixed  $picture       picture
     */
    public function __construct($name, $date, $place, $editionNumber, $organizer, $picture)
    {
        $this->name = $name;
        $this->date = $date;
        $this->place = $place;
        $this->editionNumber = $editionNumber;
        $this->organizer = $organizer;
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
     * @return Organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @param Organizer $organizer
     */
    public function setOrganizer(Organizer $organizer)
    {
        $this->organizer = $organizer;
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
