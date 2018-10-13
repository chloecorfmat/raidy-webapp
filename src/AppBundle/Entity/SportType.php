<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sport_type")
 */
class SportType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, unique=true)
     */
    protected $sport;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $icon;

    /**
     * SportType constructor.
     *
     * @param mixed $sport sport
     * @param mixed $icon  icon
     */
    public function __construct($sport, $icon)
    {
        $this->sport = $sport;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getSport();
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
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * @param mixed $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}
