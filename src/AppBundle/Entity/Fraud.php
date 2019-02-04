<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fraud
 *
 * @ORM\Table(name="fraud")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FraudRepository")
 */
class Fraud
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Competitor")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competitor;

    /**
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RaceCheckpoint")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checkpoint;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set competitor.
     *
     * @param Competitor $competitor
     *
     * @return Fraud
     */
    public function setCompetitor($competitor)
    {
        $this->competitor = $competitor;

        return $this;
    }

    /**
     * Get competitor.
     *
     * @return Competitor
     */
    public function getCompetitor()
    {
        return $this->competitor;
    }

    /**
     * Set checkpoint.
     *
     * @param Competitor $checkpoint
     *
     * @return Fraud
     */
    public function setCheckpoint($checkpoint)
    {
        $this->checkpoint = $checkpoint;

        return $this;
    }

    /**
     * Get checkpoint.
     *
     * @return Competitor
     */
    public function getCheckpoint()
    {
        return $this->checkpoint;
    }
}
