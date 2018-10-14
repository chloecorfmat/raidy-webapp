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

/**
 * @ORM\Entity
 * @ORM\Table(name="emergency_phone_number")
 */
class EmergencyPhoneNumber
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=45, unique=true)
     */
    protected $role;

    /**
     * @ORM\Column(name="phone_number", type="string", length=45)
     */
    protected $phoneNumber;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $raid;

    /**
     * EmergencyPhoneNumber constructor.
     *
     * @param mixed $role        role
     * @param mixed $phoneNumber phone number
     * @param mixed $raid        raid
     */
    public function __construct($role, $phoneNumber, $raid)
    {
        $this->role = $role;
        $this->phoneNumber = $phoneNumber;
        $this->raid = $raid;
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
