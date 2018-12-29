<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 18/10/2018
 * Time: 13:37.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Poi;
use AppBundle\Entity\Raid;
use Doctrine\ORM\EntityManagerInterface;

class PoiService
{
    /**
     * PoiService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $obj
     * @param mixed $raidId
     *
     * @return Poi
     */
    public function poiFromArray($obj, $raidId)
    {
        $poi = new Poi();

        $poi->setName($obj['name']);

        $poi->setLongitude($obj['longitude']);
        $poi->setLatitude($obj['latitude']);
        $poi->setRequiredHelpers($obj['requiredHelpers']);

        $poiTypeRepository = $this->em->getRepository('AppBundle:PoiType');
        $poiType = $poiTypeRepository->find($obj['poiType']);
        $poi->setPoiType($poiType);

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $poi->setRaid($raid);

        $poi->setIsCheckpoint($obj['isCheckpoint']);

        return $poi;
    }

    /**
     * @param Poi $poi
     *
     * @return false|string
     */
    public function poiToJson($poi)
    {
        $obj = [];

        $obj['id'] = $poi->getId();
        $obj['name'] = htmlentities($poi->getName());
        $obj['longitude'] = htmlentities($poi->getLongitude());
        $obj['latitude'] = htmlentities($poi->getLatitude());
        $obj['requiredHelpers'] = htmlentities($poi->getRequiredHelpers());
        $obj['raid'] = $poi->getRaid()->getId();
        $obj['isCheckpoint'] = htmlentities($poi->getisCheckpoint());

        if (null != $poi->getPoiType()) {
            $obj['poiType'] = $poi->getPoiType()->getId();
        } else {
            $obj['poiType'] = '';
        }

        return json_encode($obj);
    }

    /**
     * @param Poi   $poi
     * @param int   $raidId
     * @param mixed $obj
     *
     * @return mixed
     */
    public function updatePoiFromArray($poi, $raidId, $obj)
    {
        $poi->setName($obj['name']);

        $poi->setLongitude($obj['longitude']);
        $poi->setLatitude($obj['latitude']);
        $poi->setRequiredHelpers($obj['requiredHelpers']);

        $poiTypeRepository = $this->em->getRepository('AppBundle:PoiType');
        $poiType = $poiTypeRepository->find($obj['poiType']);
        $poi->setPoiType($poiType);

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $poi->setRaid($raid);

        $poi->setIsCheckpoint($obj['isCheckpoint']);

        return $poi;
    }

    /**
     * @param array $pois
     *
     * @return false|string
     */
    public function poisArrayToJson($pois)
    {
        $poisObj = [];

        foreach ($pois as $poi) {
            $obj = [];

            $obj['id'] = $poi->getId();
            $obj['name'] = htmlentities($poi->getName());
            $obj['longitude'] = htmlentities($poi->getLongitude());
            $obj['latitude'] = htmlentities($poi->getLatitude());
            $obj['requiredHelpers'] = htmlentities($poi->getRequiredHelpers());
            $obj['raid'] = $poi->getRaid()->getId();
            $obj['isCheckpoint'] = htmlentities($poi->getisCheckpoint());

            if (null != $poi->getPoiType()) {
                $obj['poiType'] = $poi->getPoiType()->getId();
            } else {
                $obj['poiType'] = '';
            }

            $poisObj[] = $obj;
        }

        return json_encode($poisObj);
    }

    /**
     * @param mixed $obj
     * @param bool  $checkId
     *
     * @return bool
     */
    public function checkDataArray($obj, $checkId)
    {
        if ($checkId) {
            if (!isset($obj['id']) || '' == $obj['id']) {
                return false;
            }
        }

        if (!isset($obj['name']) || '' == $obj['name'] || strlen($obj['name'] > 45)) {
            return false;
        }

        if (!isset($obj['longitude']) || '' == $obj['longitude']) {
            return false;
        }

        if (!isset($obj['latitude']) || '' == $obj['latitude']) {
            return false;
        }

        if (!isset($obj['requiredHelpers']) || $obj['requiredHelpers'] < 0) {
            return false;
        }

        if (!isset($obj['poiType']) || '' == $obj['poiType']) {
            return false;
        }

        if (!isset($obj['isCheckpoint']) || "boolean" != gettype($obj['isCheckpoint'])) {
            return false;
        }

        return true;
    }

    /**
     * @param Raid $raidToClone
     * @param Raid $raid
     */
    public function clonePois($raidToClone, $raid)
    {
        // Clone POIs
        $poiRepository = $this->em->getRepository('AppBundle:Poi');
        $pois = $poiRepository->findBy(array('raid' => $raidToClone->getId()));

        if (null != $pois) {
            foreach ($pois as $poi) {
                $p = new Poi();

                $p->setName($poi->getName());
                $p->setLongitude($poi->getLongitude());
                $p->setLatitude($poi->getLatitude());
                $p->setRequiredHelpers($poi->getRequiredHelpers());
                $p->setPoiType($poi->getPoiType());
                $p->setRaid($raid);
                $p->setIsCheckpoint($poi->getIsCheckpoint());

                $this->em->persist($p);
                $this->em->flush();
            }
        }
    }
}
