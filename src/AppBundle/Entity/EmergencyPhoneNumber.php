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
     * @ORM\Column(name="raid_id", type="integer")
     */
    protected $raidId;

    /**
     * EmergencyPhoneNumber constructor.
     * @param $role
     * @param $phoneNumber
     * @param $raidId
     */
    public function __construct($role, $phoneNumber, $raidId)
    {
        $this->role = $role;
        $this->phoneNumber = $phoneNumber;
        $this->raidId = $raidId;
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
    public function getRaidId()
    {
        return $this->raidId;
    }

    /**
     * @param mixed $raidId
     */
    public function setRaidId($raidId)
    {
        $this->raidId = $raidId;
    }
}