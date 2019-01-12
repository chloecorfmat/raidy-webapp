<?php

namespace AppBundle\Service;

use AppBundle\Entity\Competitor;
use AppBundle\Entity\Raid;
use Doctrine\ORM\EntityManagerInterface;

class CompetitorService
{
    /**
     * CompetitorService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Competitor $competitor
     *
     * @return false|string
     */
    public function competitorToJson($competitor)
    {
        $obj = [];

        $obj['id'] = $competitor->getId();
        $obj['user'] = $competitor->getUser()->getId();


        $obj['raid'] = $competitor->getRaid()->getId();

        return json_encode($obj);
    }

    /**
     * @param mixed $obj
     * @param mixed $directory
     *
     * @return Competitor
     */
    public function competitorFromForm($obj, $raidId)
    {
        $competitor = new Competitor();

        $competitor->setFirstname($obj->getFirstname());
        $competitor->setLastname($obj->getLastname());
        $competitor->setNumberSign($obj->getNumberSign());
        $competitor->setCategory($obj->getCategory());
        $competitor->setSex($obj->getSex());
        $competitor->setBirthYear($obj->getBirthYear());

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $competitor->setRaid($raid);

        $competitor->setUniqid(uniqid());


        return $competitor;
    }
}
