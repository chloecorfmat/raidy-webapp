<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 27/09/2018
 * Time: 17:08.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    protected $address;

    /**
     * @ORM\Column(name="address_addition", type="string", length=45, nullable=true)
     */
    protected $addressAddition;

    /**
     * @ORM\Column(type="integer")
     */
    protected $postCode;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $city;

    /**
     * @ORM\Column(name="edition_number", type="integer")
     */
    protected $editionNumber;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="Insérez une image.")
     * @Assert\Image(
     *     minWidth = 50,
     *     maxWidth = 700,
     *     minHeight = 50,
     *     maxHeight = 700
     *    )
     */
    protected $picture;

    /**
     * Raid constructor.
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address = null)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddressAddition()
    {
        return $this->addressAddition;
    }

    /**
     * @param mixed $addressAddition
     */
    public function setAddressAddition($addressAddition)
    {
        $this->addressAddition = $addressAddition;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param mixed $postCode
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture = null)
    {
        $this->picture = $picture;
    }
}
