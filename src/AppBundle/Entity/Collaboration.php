<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 05/11/18
 * Time: 11:44
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="collaboration")
 */
class Collaboration
{

    /**
     * @ORM\Id
     * @ORM\Column(name="invitationId", type="string", length=13)
     * @Assert\NotBlank()
     */
    private $invitationId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn()
     * @Assert\NotBlank()
     */
    protected $raid;

    /**
     * @ORM\Column(name="email", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * Collaboration constructor.
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getInvitationId()
    {
        return $this->invitationId;
    }

    /**
     * @param mixed $invitationId
     */
    public function setInvitationId($invitationId)
    {
        $this->invitationId = $invitationId;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
