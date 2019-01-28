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
     * @param mixed $raidId
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

    /**
     * @param array $array
     * @param mixed $raidId
     *
     * @return Competitor
     */
    public function competitorFromCsv($array, $raidId)
    {
        $competitor = new Competitor();
        $competitor->setLastname($array[0]);
        $competitor->setFirstname($array[1]);
        $competitor->setNumberSign($array[2]);
        $competitor->setCategory($array[3]);
        $competitor->setSex($array[4]);
        $competitor->setBirthYear($array[5]);
        $competitor->setRace($array[6]);

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $competitor->setRaid($raid);

        $competitor->setUniqid(uniqid());

        return $competitor;
    }

    /**
     * @param array $competitors
     *
     * @return false|string
     */
    public function competitorsArrayToJson($competitors)
    {
        $competitorsObj = [];

        foreach ($competitors as $competitor) {
            $obj = [];

            $obj['id'] = $competitor->getUniqid();
            $obj['firstname'] = $competitor->getFirstname();
            $obj['lastname'] = $competitor->getLastname();
            $obj['number_sign'] = $competitor->getNumberSign();
            $obj['category'] = $competitor->getCategory();
            $obj['sex'] = $competitor->getSex();
            $obj['birthyear'] = $competitor->getBirthYear();
            if ($competitor->getRace() != null) {
                $obj['race'] = [];
                $obj['race']['id'] = $competitor->getRace()->getId();
                $obj['race']['name'] = $competitor->getRace()->getName();
            } else {
                $obj['race'] = null;
            }

            $obj['raid'] = $competitor->getRaid()->getId();

            $competitorsObj[] = $obj;
        }

        return json_encode($competitorsObj);
    }
}
