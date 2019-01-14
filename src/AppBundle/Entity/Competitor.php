<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 19/12/18
 * Time: 08:53.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="competitor")
 */
class Competitor
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
    private $lastname;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le prénom ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le numéro de dossard ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    private $numberSign;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "La catégorie ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 45,
     *      maxMessage = "Le sexe ne doit pas dépasser {{ limit }} caractères",
     * )
     */
    private $sex;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $birthYear;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn(nullable=false)
     */
    private $raid;

    /**
     * @ORM\Column(name="uniqid", type="string", unique=true)
     */
    private $uniqid;

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
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getNumberSign()
    {
        return $this->numberSign;
    }

    /**
     * @param mixed $numberSign
     */
    public function setNumberSign($numberSign)
    {
        $this->numberSign = $numberSign;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return mixed
     */
    public function getBirthYear()
    {
        return $this->birthYear;
    }

    /**
     * @param mixed $birthYear
     */
    public function setBirthYear($birthYear)
    {
        $this->birthYear = $birthYear;
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
