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
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="last_name", type="string", length=45)
     * @Assert\NotBlank(groups={"editProfile"})
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le nom ne doit pas dépasser {{ limit }} caractères",
     *      groups={"editProfile", "Profile"}
     * )
     */
    protected $lastName;

    /**
     * @ORM\Column(name="first_name", type="string", length=45)
     * @Assert\NotBlank(groups={"editProfile", "Profile"})
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le prénom ne doit pas dépasser {{ limit }} caractères",
     *      groups={"editProfile", "Profile"}
     * )
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(groups={"editProfile"})
     */
    protected $phone;

    /**
     * @Assert\NotBlank(groups={"changePassword"})
     * @Assert\Regex(
     *     pattern="/[A-Z]/",
     *     message="Le mot de passe doit comporter au moins une lettre majuscule.",
     *     groups={"editProfile", "Profile", "changePassword"}
     * )
     * @Assert\Regex(
     *     pattern="/[0-9]/",
     *     message="Le mot de passe doit comporter au moins un chiffre.",
     *     groups={"editProfile", "Profile", "changePassword"}
     * )
     * @Assert\Regex(
     *     pattern="/[a-z]/",
     *     message="Le mot de passe doit comporter au moins une lettre minuscule.",
     *     groups={"editProfile", "Profile", "changePassword"}
     * )
     * @Assert\Regex(
     *     pattern="/[@;,&()!?:%*€$£+=#_\/\\.\[\]\{\}-]/",
     *     message="Le mot de passe doit comporter au moins un caractère spécial parmi
       la liste suivante : @ ; , & ( ) ! ? : % * € $ £ + = # _ \ / [ ] { } - .",
     *     groups={"editProfile", "Profile", "changePassword"}
     * )
     *
     * @Assert\Length(
     *      min = 8,
     *      max = 50,
     *      maxMessage = "Le mot de passe ne doit pas dépasser {{ limit }} caractères.",
     *      minMessage = "Le mot de passe doit dépasser {{ limit }} caractères.",
     *      groups={"editProfile", "Profile", "changePassword"}
     * )
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $tutorialTime;

    /**
     * @ORM\Column(name="licence_number", type="string", length=9, nullable=true)
     */
    protected $licenceNumber;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
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
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getTutorialTime()
    {
        return $this->tutorialTime;
    }

    /**
     * @param mixed $tutorialTime
     */
    public function setTutorialTime($tutorialTime)
    {
        $this->tutorialTime = $tutorialTime;
    }

    /**
     * @return mixed
     */
    public function getLicenceNumber()
    {
        return $this->licenceNumber;
    }

    /**
     * @param mixed $licenceNumber
     */
    public function setLicenceNumber($licenceNumber)
    {
        $this->licenceNumber = $licenceNumber;
    }
}
