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
        $obj['firstname'] = $competitor->getFirstname();
        $obj['lastname'] = $competitor->getLastname();
        $obj['number_sign'] = $competitor->getNumberSign();
        $obj['category'] = $competitor->getCategory();
        $obj['sex'] = $competitor->getSex();
        $obj['birthyear'] = $competitor->getBirthYear();
        $obj['race'] = $competitor->getRace()->getId();

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
        $competitor->setRace($obj->getRace());

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $competitor->setRaid($raid);

        $competitor->setUniqid(uniqid());


        return $competitor;
    }
}
