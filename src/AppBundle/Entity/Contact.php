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
 * @ORM\Table(name="contact")
 */
class Contact
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
     *      maxMessage = "Le role ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    protected $role;

    /**
     * @ORM\Column(name="phone_number", type="string", length=45, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le numéro de téléphone ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    protected $phoneNumber;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Helper")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $helper;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $raid;

    /**
     * Contact constructor.
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @param mixed $helper
     */
    public function setHelper($helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return Raid
     */
    public function getRaid()
    {
        return $this->raid;
    }

    /**
     * @param Raid $raid
     */
    public function setRaid(Raid $raid)
    {
        $this->raid = $raid;
    }
}
