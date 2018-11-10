<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 25/10/2018
 * Time: 22:42
 */

namespace AppBundle\Service;

use AppBundle\Entity\Poi;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;

class RaidService
{

    /**
     * @param array $raids
     *
     * @return false|string
     */
    public function raidsArrayToJson($raids)
    {
        $raidsObj = [];

        foreach ($raids as $raid) {
            $obj = [];

            $obj['id'] = $raid->getId();
            $obj['name'] = $raid->getName();
            $obj['date'] = $raid->getDate();
            $obj['address'] = $raid->getAddress();

            if (null != $raid->getAddressAddition()) {
                $obj['addressAddition'] = $raid->getAddressAddition();
            } else {
                $obj['addressAddition'] = '';
            }

            $obj['postCode'] = $raid->getPostCode();
            $obj['city'] = $raid->getCity();
            $obj['editionNumber'] = $raid->getEditionNumber();
            $obj['picture'] = $raid->getPicture();

            $raidsObj[] = $obj;
        }

        return json_encode($raidsObj);
    }

    /**
     * @param Raid $raid
     *
     * @return false|string
     */
    public function raidToJson($raid)
    {
        $obj = [];

        $obj['id'] = $raid->getId();
        $obj['name'] = $raid->getName();
        $obj['date'] = $raid->getDate();
        $obj['address'] = $raid->getAddress();

        if (null != $raid->getAddressAddition()) {
            $obj['addressAddition'] = $raid->getAddressAddition();
        } else {
            $obj['addressAddition'] = '';
        }

        $obj['postCode'] = $raid->getPostCode();
        $obj['city'] = $raid->getCity();
        $obj['editionNumber'] = $raid->getEditionNumber();
        $obj['picture'] = $raid->getPicture();

        return json_encode($obj);
    }

    /**
     * @param Raid $obj
     */
    public function cloneRaid($obj)
    {

        $raid = new Raid();

        // Clone raid
        $raid->setName($obj->getName());
        $raid->setDate($obj->getDate());
        $raid->setAddress($obj->getAddress());

        if (null != $obj->getAddressAddition()) {
            $raid->setAddressAddition($obj->getAddressAddition());
        } else {
            $raid->setAddressAddition(null);
        }

        $raid->setPostCode($obj->getPostCode());
        $raid->setCity($obj->getCity());
        $raid->setEditionNumber($obj->getEditionNumber());
        $raid->setPicture($obj->getPicture());

        $userRepository = $this->em->getRepository('AppBundle:User');
        $user = $userRepository->find($obj->getUser());
        $raid->setUser($user);

        $this->persist($raid);
        $this->em->flush();

        // Clone tracks
        $trackRepository = $this->em->getRepository('AppBundle:Track');
        $tracks = $trackRepository->findAll(array('raid' => $obj->getId()));

        if (null != $tracks) {
            foreach ($tracks as $track) {
                $t = new Track();

                $t->setName($track->getName());
                $t->setRaid($raid->getId());
                $t->setTrackPoints($track->getTrackPoints());
                $t->setColor($track->getColor());
                $t->setSportType($track->getSportType());
                $t->setIsVisible($track->getIsVisible());
                $t->setIsCalibration($track->getIsCalibration());

                $this->persist($t);
                $this->em->flush();
            }
        }

        // Clone POIs
        $poiRepository = $this->em->getRepository('AppBundle:Poi');
        $pois = $poiRepository->findAll(array('raid' => $obj->getId()));

        if (null != $pois) {
            foreach ($pois as $poi) {
                $p = new Poi();

                $p->setName($poi->getName());
                $p->setLongitude($poi->getLongitude());
                $p->setLatitude($poi->getLatitude());
                $p->setRequiredHelpers($poi->getRequiredHelpers());

                $poiTypeRepository = $this->em->getRepository('AppBundle:PoiType');
                $poiType = $poiTypeRepository->find($obj->getPoiType());
                $p->setPoiType($poiType);

                $p->setRaid($raid->getId());

                $this->persist($p);
                $this->em->flush();
            }
        }

        // Clone contacts
    }
}
