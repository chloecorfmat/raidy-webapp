<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TwitterApiData
 *
 * @ORM\Table(name="twitter_api_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TwitterApiDataRepository")
 */
class TwitterApiData
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
     * @var \DateTime
     *
     * @ORM\Column(name="request_datetime", type="datetime")
     */
    private $requestDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Raid")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $raid;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set request datetime
     *
     * @param \DateTime $requestDatetime
     *
     * @return TwitterApiData
     */
    public function setRequestDatetime($requestDatetime)
    {
        $this->requestDatetime = $requestDatetime;

        return $this;
    }

    /**
     * Get request datetime
     *
     * @return \DateTime
     */
    public function getRequestDatetime()
    {
        return $this->requestDatetime;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return TwitterApiData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
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
}
