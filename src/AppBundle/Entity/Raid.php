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
 * @ORM\Table(name="raid", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="uniqid", columns={"uniqid"})})
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
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le nom ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "L'adresse ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    protected $address;

    /**
     * @ORM\Column(name="address_addition", type="string", length=45, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le complément d'adresse ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    protected $addressAddition;

    /**
     * @ORM\Column(type="integer")
     */
    protected $postCode;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "La ville ne doit pas dépasser {{ limit }} caractères",
     *      groups={"editProfile", "Profile"}
     * )
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
     * @Assert\Image(
     *     minWidth = 50,
     *     minHeight = 50
     *    )
     */
    protected $picture;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastEdition;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $lastEditor;

    /**
     * @ORM\Column(name="uniqid", type="string", unique=true)
     */
    protected $uniqid;

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

    /**
     * @return mixed
     */
    public function getLastEdition()
    {
        return $this->lastEdition;
    }

    /**
     * @param mixed $lastEdition
     */
    public function setLastEdition($lastEdition)
    {
        $this->lastEdition = $lastEdition;
    }

    /**
     * @return mixed
     */
    public function getLastEditor()
    {
        return $this->lastEditor;
    }

    /**
     * @param mixed $lastEditor
     */
    public function setLastEditor($lastEditor)
    {
        $this->lastEditor = $lastEditor;
    }

    /**
     * @param mixed $lastEditor
     * @param mixed $em
     */
    public function notifyChange($lastEditor, $em)
    {
        $this->setLastEdition(new \DateTime(date('Y-m-d H:i:s')));
        $this->setLastEditor($lastEditor);
        $em->flush();
    }

    /**
     * @return mixed
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * @param mixed $uniqid
     */
    public function setUniqid($uniqid)
    {
        $this->uniqid = $uniqid;
    }
}
