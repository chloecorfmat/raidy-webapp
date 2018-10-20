<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 18/10/2018
 * Time: 13:37.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Poi;
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
        $obj['name'] = $poi->getName();
        $obj['longitude'] = $poi->getLongitude();
        $obj['latitude'] = $poi->getLatitude();
        $obj['requiredHelpers'] = $poi->getRequiredHelpers();
        $obj['raid'] = $poi->getRaid()->getId();

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
            $obj['name'] = $poi->getName();
            $obj['longitude'] = $poi->getLongitude();
            $obj['latitude'] = $poi->getLatitude();
            $obj['requiredHelpers'] = $poi->getRequiredHelpers();
            $obj['raid'] = $poi->getRaid()->getId();

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
        $status = true;

        if ($checkId) {
            if (null == $obj['id'] || '' == $obj['id']) {
                $status = false;
            }
        }

        if (null == $obj['name'] || '' == $obj['name']) {
            $status = false;
        }

        if (null == $obj['longitude'] || '' == $obj['longitude']) {
            $status = false;
        }

        if (null == $obj['latitude'] || '' == $obj['latitude']) {
            $status = false;
        }

        if (null == $obj['requiredHelpers'] || '' == $obj['requiredHelpers']) {
            $status = false;
        }

        if (null == $obj['poiType'] || '' == $obj['poiType']) {
            $status = false;
        }

        return $status;
    }
}
