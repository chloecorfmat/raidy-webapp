<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 18/10/2018
 * Time: 13:37
 */

namespace AppBundle\Service;

use AppBundle\Entity\POI;
use Doctrine\ORM\EntityManagerInterface;

class PoiService
{
    /**
     * PoiService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $obj
     * @param mixed $raidId
     * @return Poi
     */
    public function poiFromArray($obj, $raidId)
    {
        $poi = new Poi();

        if ($obj['id'] != '') {
            $poi->setId($obj['id']);
        }

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

        return $poi;
    }

    /**
     * @param Poi $poi
     * @return false|string
     */
    public function poiToJson($poi)
    {
        $obj = [];

        $obj['id'] = $poi->getId();
        $obj['name'] = $poi->getName();
        $obj['longitude'] = $poi->getLongitude();
        $obj['latitude'] = $poi->getLatitude();
        $obj['requiredHelpers'] = $poi->getRequiredHelpers();
        $obj['raid'] = $poi->getRaid()->getId();

        if ($poi->getPoiType() != null) {
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
     * @return mixed
     */
    public function updateTrackFromArray($poi, $raidId, $obj)
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

        return $poi;
    }

/**
     * @param array $pois
     * @return false|string
     */
    public function poisArrayToJson($pois)
    {
        $poisObj = [];

        foreach ($pois as $poi) {
            $obj = [];

            $obj['id'] = $poi->getId();
            $obj['name'] = $poi->getName();
            $obj['longitude'] = $poi->getLongitude();
            $obj['latitude'] = $poi->getLatitude();
            $obj['requiredHelpers'] = $poi->getRequiredHelpers();
            $obj['raid'] = $poi->getRaid()->getId();

            if ($poi->getPoiType() != null) {
                $obj['poiType'] = $poi->getPoiType()->getId();
            } else {
                $obj['poiType'] = '';
            }

            $poisObj[] = $obj;
        }

        return json_encode($poisObj);
    }
}
